<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Place;
use App\Models\Assignee;
use App\Models\UserPlace;
use Illuminate\Support\Facades\Auth;

class PlaceService
{
    public function add($data)
    {
        try {
            $place = Place::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'alias' => $data['name'],
                'address' => $data['address'],
            ]);
            $user = User::where('id',$data['user_id'])->first();
            $user->place_id = $place->id;
            $user->save();

            UserPlace::create([
                'user_id'=> $user->id,
                'place_id' => $place->id,
            ]);

            Assignee::create([
                'place_id' => $place->id,
                'user_id'=> $user->id,
                'taskowner_id'=> $user->id,

            ]);
            return response()->json('Place added successfully.');
        } catch(Exception $e) {
            return response()->json('Module PlaceService (add) '.$e->getMessage());
        }
        
    }

    public function get_places()
    {
        //return response()->json(Place::all(),200);

        $authUserId = Auth::id(); // Get the authenticated user's ID
    
    // Query to get places the user owns or has tasks assigned to
    $places = Place::where('user_id', $authUserId) // Places owned by the authenticated user
        ->orWhereHas('tasks', function($query) use ($authUserId) { // Places where the user has tasks assigned
            $query->whereHas('users', function($query) use ($authUserId) {
                $query->where('user_id', $authUserId);
            });
        })
        ->get();
    //return response()->json(Place::all(),200);
    return response()->json($places, 200);
    }
}