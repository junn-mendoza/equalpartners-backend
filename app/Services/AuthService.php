<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Assignee;
use App\Mail\ForgotPassword;
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
        if(!$user) {
            return response()->json([
                'error' => 'Not found!',
                'error_message' => 'Unable to locate your email!',
            ]
            );
        }
        $key = env('EQUALPARTNER_API_KEY');
        // send email 
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        if($key != '') {
            $details = [
                'email' => $data['email'],
                'api_key' => $key,
                'url' =>  $protocol . '://' . $_SERVER['HTTP_HOST'] . '/changepassword'
            ];
            try {
    
            Mail::to($data['email'])->send(new ForgotPassword($details));
            //dd(1);
            return response()->json([
                'message'=>'Send successfully']
                ,200);
            } catch (Exception $e) {
                // Failed to send email
                return response()->json(['message' => 'Failed to send email. Please try again.', 'error' => $e->getMessage()], 500);
            }
        }
        return response()->json('Cannot generate the api_key.', 500);
    }
        
        

    public function passwordchange($data)
    {
        $user = User::where('email',$data['email'])->first();
        if($user) {
            $user->password = $data['password'];
            $user->save();
            return response()->json('Sucessfully changed your password.', 200);
        } else 
        return response()->json('Email not found.', 401);
    }
    
    public function register($data): JsonResponse | string
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),

            ]);
            Assignee::create([
                'user_id' => $user->id,
                'taskowner_id' => $user->id,
            ]);
            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json(['user' => $user, 'access_token' => $token], 200);
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

        $user = Auth::user();

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['user' => $user, 'access_token' => $token, 'token_type' => 'Bearer'], 200);
    }
}
