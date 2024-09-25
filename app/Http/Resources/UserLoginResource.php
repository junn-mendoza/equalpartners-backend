<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    protected $accessToken;

    // Override the constructor to accept the access_token
    public function __construct($resource, $accessToken)
    {
        // Ensure the resource is passed to the parent constructor
        parent::__construct($resource);
        $this->accessToken = $accessToken; // Store the token
    }
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
            'access_token' => $this->accessToken,
        ];
    }
}
