<?php

namespace App\Repositories;

use App\Models\Actor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActorRepository implements ActorRepositoryInterface
{
    public function latestList(): Collection
    {
        return Actor::query()
            ->latest()
            ->get([
                'id',
                'first_name',
                'last_name',
                'address',
                'gender',
                'height_cm',
                'weight_kg',
                'age',
                'created_at',
            ]);
    }

    public function latestPaginated(int $perPage, int $page = 1): LengthAwarePaginator
    {
        return Actor::query()
            ->latest()
            ->paginate($perPage, [
                'id',
                'email',
                'first_name',
                'last_name',
                'address',
                'gender',
                'height_cm',
                'weight_kg',
                'age',
                'created_at',
            ], 'page', $page); // Explicit page keeps pagination consistent with validated input.
    }

    public function existsByDescriptionHash(string $hash): bool
    {
        return Actor::query()->where('description_hash', $hash)->exists();
    }

    public function create(array $attributes): Actor
    {
        return Actor::create($attributes);
    }
}

