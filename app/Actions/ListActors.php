<?php

namespace App\Actions;

use App\Models\Actor;
use App\Repositories\ActorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class ListActors
{
    public function __construct(
        private readonly ActorRepositoryInterface $actors,
    ) {
    }

    /**
     * @return Collection<int, Actor>
     */
    public function execute(): Collection
    {
        return $this->actors->latestList();
    }

    /**
     * @return LengthAwarePaginator<Actor>
     */
    public function executePaginated(int $perPage, int $page = 1): LengthAwarePaginator
    {
        return $this->actors->latestPaginated($perPage, $page);
    }
}

