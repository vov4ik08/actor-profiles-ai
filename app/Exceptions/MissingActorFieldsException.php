<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MissingActorFieldsException extends RuntimeException
{
    public function __construct(?string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?? __('actors.errors.missing_required_fields'), $code, $previous);
    }

    public function render(Request $request): ?JsonResponse
    {
        if (!$request->expectsJson() && !$request->is('api/*')) {
            return null;
        }

        return response()->json([
            'message' => $this->getMessage(),
            'errors' => [
                // Return a validation-like structure so the frontend can show it under the field.
                'description' => [$this->getMessage()],
            ],
        ], 422);
    }
}

