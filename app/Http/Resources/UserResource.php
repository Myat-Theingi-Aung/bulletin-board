<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $this->profile,
            'type' => $this->type,
            'phone' => $this->phone,
            'address' => $this->address,
            'dob' => $this->dob,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'deleted_user_id' => $this->deleted_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
