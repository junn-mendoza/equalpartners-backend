<?php

namespace App\Services;

class UserService
{
    public function saveProfile($data)
    {
        // Handle the file upload
        if ($request->hasFile('image')) {
            $user = Auth::user();
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
            $profile = new Image();
            $profile->user_id = $user->id;
            $profile->path =  $webpPath;
            $profile->description = $request->input('source');
            $profile->save();

            return response()->json(['message' => 'Profile image uploaded successfully!', 'profile' => $profile]);
        }

        return response()->json(['message' => 'No image uploaded!'], 400);
    }
}
