<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardGetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'place_id' => $this->place_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'users' => $this->user_rewards->map(function ($userReward) {
                return [
                    'id' => $userReward->user->id,
                    'name' => $userReward->user->name,
                    'email' => $userReward->user->email,
                    'profile' => $userReward->user->profile,
                    'created_at' => $userReward->user->created_at,
                    // Add any other user fields you want to include
                ];
            }),
        ];
    }
}
