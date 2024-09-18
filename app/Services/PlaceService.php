<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Place;
use Illuminate\Support\Facades\Auth;

class PlaceService
{
    public function add($data)
    {
        try {
            $place = Place::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'address' => $data['address'],
            ]);
            $user = User::where('id',$data['user_id'])->first();
            $user->place_id = $place->id;
            $user->save();
            return response()->json('Place added successfully.');
        } catch(Exception $e) {
            return response()->json('Module PlaceService (add) '.$e->getMessage());
        }
        
    }

    public function get_places()
    {
        return response()->json(Place::where('user_id', Auth::id())->get(),200);
    }
}