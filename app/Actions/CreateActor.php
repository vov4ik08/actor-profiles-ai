<?php

namespace App\Actions;

use App\Contracts\ActorExtractorInterface;
use App\Models\Actor;
use App\Repositories\ActorRepositoryInterface;

final class CreateActor
{
    public function __construct(
        private readonly ActorRepositoryInterface $actors,
        private readonly ActorExtractorInterface $extractor,
    ) {
    }

    /**
     * @param array{email:string,description:string,description_hash:string} $validated
     */
    public function execute(array $validated): Actor
    {
        $extracted = $this->extractor->extractRequired($validated['description']);

        return $this->actors->create([
            'email' => $validated['email'],
            'description' => $validated['description'],
            'description_hash' => $validated['description_hash'],
            'first_name' => $extracted['first_name'],
            'last_name' => $extracted['last_name'],
            'address' => $extracted['address'],
            'height_cm' => $extracted['height_cm'],
            'weight_kg' => $extracted['weight_kg'],
            'gender' => $extracted['gender'],
            'age' => $extracted['age'],
        ]);
    }
}

