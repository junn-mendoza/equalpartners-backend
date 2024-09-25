<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invite;
use App\Mail\invitation;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\PlaceService;
use App\Services\ImageDataService;
use App\Http\Requests\PlaceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Notification;

class ProfileController extends Controller
{
    protected UserService $userService;
    protected ImageDataService $imageDataService;
    protected PlaceService $placeService;
    public function __construct(
        UserService $userService, 
        ImageDataService $imageDataService,
        PlaceService $placeService
        )
    {
        $this->userService = $userService;
        $this->imageDataService = $imageDataService;
        $this->placeService = $placeService;
    }

    public function places(PlaceRequest $request)
    {
        return $this->placeService->add($request->validated());
    }

    public function get_places()
    {
        return $this->placeService->get_places();
    }
    public function homename(Request $request)
    {
        $user = Auth::user();
        $user->home_name = $request->input('homename');
        $user->save();
        return response()->json('Home name successfully saved', 200);
    }

    public function homeaddress(Request $request)
    {
        $user = Auth::user();
        $user->home_address = $request->input('homeaddress');
        $user->save();
        return response()->json('Home address successfully saved', 200);
    }

    public function update_place($place_id)
    {
        $id = Auth::id();
        $user = User::where('id', $id)->first();
        $user->place_id = $place_id;
        $user->save();
        return response()->json('Sucessfully updated.', 200);
    }
    public function invitation(Request $request)
    {
        $recipientName = $request->input('name'); // The recipient's name
        $playStoreLink = 'https://play.google.com/store/apps/details?id=com.equalpartner';
        $appStoreLink = 'https://apps.apple.com/app/equalpartners';


        Mail::to($request->input('email'))->send(new invitation($recipientName, $playStoreLink, $appStoreLink));

        Invite::updateOrCreate(
            [
                'email' => $request->input('email')

            ],
            [
                'user_id' => Auth::id(),
                'name' => $request->input('name'),
                'place_id' => $request->input('place_id'),

            ]
        );
        return 'Invitation sent!';
    }

    public function show_invite(Request $request)
    {
         $invite = Invite::where('email',$request->input('email'))->get();
         return response()->json($invite,200);
    }

    public function profile(ProfileUpdateRequest $request)
    {
        $request->validated();
        $user = Auth::user();
        $hasImage = false;
        if ($request->hasFile('image')) {
            $hasImage = true;
            $image = $request->file('image');
            $path = 'profile/' . $user->id . '/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Convert the image directly to .webp
            $webpPath = $this->imageDataService->convertToWebp($path);

            // Save the image to the public/profile directory
            Storage::disk('public')->put($path, file_get_contents($image));

            // Convert the temporary image to webp
            $tempPath = storage_path('app/public/' . $path);
            $this->imageDataService->webpImage($tempPath, 100, true);

            // Save the data to the database


        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        // $user->age = $request->input('age');
        // $user->role = $request->input('role');
        if ($hasImage) {
            $user->profile =  $webpPath;
        }
        $user->save();

        return response()->json(['message' => 'Profile image uploaded successfully!', 'user' => $user], 200);

        //return response()->json(['message' => 'No image uploaded!'], 400);
    }
}
