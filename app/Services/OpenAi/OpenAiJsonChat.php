<?php

namespace App\Services\OpenAi;

use App\Exceptions\OpenAiExtractionException;
use OpenAI\Contracts\ClientContract;
use Throwable;

final readonly class OpenAiJsonChat
{
    public function __construct(
        private ClientContract $openai,
    ) {
    }

    public function chatJsonObject(string $model, string $systemPrompt, string $userMessage): string
    {
        if (trim((string) config('openai.api_key')) === '') {
            throw new OpenAiExtractionException(__('openai.errors.api_key_missing'));
        }

        try {
            $response = $this->openai->chat()->create([
                'model' => $model,
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            return (string) ($response->choices[0]->message->content ?? '');
        } catch (Throwable $e) {
            throw new OpenAiExtractionException(__('openai.errors.call_failed'), previous: $e);
        }
    }
}

