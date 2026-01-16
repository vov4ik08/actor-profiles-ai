<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $email
 * @property string $description
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property int|null $height_cm
 * @property int|null $weight_kg
 * @property string|null $gender
 * @property int|null $age
 * @property \Illuminate\Support\Carbon|null $created_at
 */
class ActorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address' => $this->address,
            'height_cm' => $this->height_cm,
            'weight_kg' => $this->weight_kg,
            'gender' => $this->gender,
            'age' => $this->age,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

