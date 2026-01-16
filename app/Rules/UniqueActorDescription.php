<?php

namespace App\Rules;

use App\Repositories\ActorRepositoryInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

readonly class UniqueActorDescription implements ValidationRule
{
    public function __construct(
        private ActorRepositoryInterface $actors,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        $normalized = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        if ($normalized === '') {
            return;
        }

        $hash = hash('sha256', $normalized);
        if ($this->actors->existsByDescriptionHash($hash)) {
            $fail($attribute, 'The description has already been taken.');
        }
    }
}

