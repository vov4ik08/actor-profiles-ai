<?php

namespace App\Repositories;

use App\Models\Actor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ActorRepositoryInterface
{
    /**
     * @return Collection<int, Actor>
     */
    public function latestList(): Collection;

    /**
     * @return LengthAwarePaginator<Actor>
     */
    public function latestPaginated(int $perPage, int $page = 1): LengthAwarePaginator;

    public function existsByDescriptionHash(string $hash): bool;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Actor;
}

