<?php

namespace App\Services;

use App\Http\Resources\UserLoginResource;
use Exception;
use App\Models\User;
use App\Models\Place;
use App\Models\Invite;
use App\Models\Assignee;
use App\Models\UserPlace;
use App\Mail\ForgotPassword;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function forgotpassword($data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return response()->json(
                [
                    'error' => 'Not found!',
                    'error_message' => 'Unable to locate your email!',
                ]
            );
        }
        $key = env('EQUALPARTNER_API_KEY');
        // send email 
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        if ($key != '') {
            $details = [
                'email' => $data['email'],
                'api_key' => $key,
                'url' =>  $protocol . '://' . $_SERVER['HTTP_HOST'] . '/changepassword'
            ];
            try {

                Mail::to($data['email'])->send(new ForgotPassword($details));
                //dd(1);
                return response()->json(
                    [
                        'message' => 'Send successfully'
                    ],
                    200
                );
            } catch (Exception $e) {
                // Failed to send email
                return response()->json(['message' => 'Failed to send email. Please try again.', 'error' => $e->getMessage()], 500);
            }
        }
        return response()->json('Cannot generate the api_key.', 500);
    }



    public function passwordchange($data)
    {
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $user->password = $data['password'];
            $user->save();
            return response()->json('Sucessfully changed your password.', 200);
        } else
            return response()->json('Email not found.', 401);
    }

    public function register($data): JsonResponse | string
    {
        //dd($data);
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'place_id'=> 1,
            ]);

            $place = Place::create([
                'user_id' => $user->id,
                'alias' => $user->name . "'s home",
                'name' => 'My home',
                'address' => null,
            ]);
            
            
            $user->place_id = $place->id; //default
            $user->save();

            Log::info('Creating Assignee with place_id: ' . $place->id);
            Assignee::create([
                'place_id'=> 1,
                'user_id' => $user->id,
                'taskowner_id' => $user->id,
            ]);

            // DB::insert('INSERT INTO assignees (place_id, user_id, taskowner_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?)', [
            //     $place->id,
            //     $user->id,
            //     $user->id,
            //     now(),
            //     now(),
            // ]);

           
            UserPlace::create([
                'place_id' => $place->id,
                'user_id' =>  $user->id,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $invite = Invite::where('email', $data['email'])
                ->first();

            if ($invite) {

                Notification::create([
                    'user_id' => $invite->user_id,
                    'message' => $invite->name . ' accepted your invitation.',
                ]);
                Assignee::create([
                    'place_id'=> $place->id,
                    'user_id' => $user->id,
                    'taskowner_id' => $invite->user_id,
                ]);
                $invite->delete();
            }
            Auth::login($user);
            DB::commit();
            return response()->json(new UserLoginResource($user, $token), 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['error' => "Validate your entry.", "error_message" => $e->getMessage()], 500);
        }
        $user = User::find($data['id']);
        return response()->json(['user' => $user]);
    }

    public function login($data): JsonResponse
    {
        if (!Auth::attempt($data)) {
            $message = [
                'error' => 'Unable to login',
                'error_message' => 'Please check your email and password combination are correct',
            ];
            return response()->json($message, 401);
        }

        request()->session()->regenerate();

        $user = Auth::user()->load('places');

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        //return response()->json(['user' => $user, 'access_token' => $token, 'token_type' => 'Bearer'], 200);
        return response()->json(new UserLoginResource($user, $token), 200);
    }
}
