<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForfeitGetResource extends JsonResource
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
            'must_complete' => $this->must_complete,
            'challenges' => $this->challenges,
            'created_at' => $this->created_at,
            'users' => $this->user_forfeits->map(function ($userForfeit) {
                return [
                    'user_id' => $userForfeit->user->id,
                    'name' => $userForfeit->user->name,
                    'email' => $userForfeit->user->email,
                    'profile' => $userForfeit->user->profile,
                    'created_at' => $userForfeit->user->created_at,
                    // Add any other user fields you want to include
                ];
            }),
        ];
    }
}
