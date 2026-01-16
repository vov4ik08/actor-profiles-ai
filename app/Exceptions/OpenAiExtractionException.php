<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenAiExtractionException extends RuntimeException
{
    public function report(): void
    {
        Log::warning(__('openai.logs.extraction_failed'), [
            'error' => $this->getMessage(),
        ]);
    }

    public function render(Request $request): ?JsonResponse
    {
        if (!$request->expectsJson() && !$request->is('api/*')) {
            return null;
        }

        return response()->json([
            'message' => __('openai.errors.request_failed'),
        ], 502);
    }
}

