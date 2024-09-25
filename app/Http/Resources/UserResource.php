<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null; // Disable wrapping for this specific resource
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $this->profile,
            'place_id' => $this->place_id,
            'alias' => $this->places[0]->alias,
            'place' => $this->places[0]->name,
            'access_token' => $this->access_token,

        ];
    }
}
