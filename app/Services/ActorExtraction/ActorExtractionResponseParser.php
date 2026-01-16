<?php

namespace App\Services\ActorExtraction;

use App\Exceptions\OpenAiExtractionException;
use JsonException;

final class ActorExtractionResponseParser
{
    /**
     * @return array{first_name:?string,last_name:?string,address:?string,height_cm:?int,weight_kg:?int,gender:?string,age:?int}
     */
    public function parse(string $content): array
    {
        $content = $this->sanitizeJsonContent($content);

        if ($content === '') {
            throw new OpenAiExtractionException(__('openai.errors.response_empty'));
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new OpenAiExtractionException(__('openai.errors.response_not_json'), previous: $e);
        }

        if (!is_array($decoded)) {
            throw new OpenAiExtractionException(__('openai.errors.response_not_object'));
        }

        return $this->normalizeExtracted($decoded);
    }

    private function sanitizeJsonContent(string $content): string
    {
        $content = trim($content);

        // Be resilient if the model returns fenced JSON despite instructions.
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```[a-zA-Z]*\s*/', '', $content) ?? $content;
            $content = preg_replace('/\s*```$/', '', $content) ?? $content;
            $content = trim($content);
        }

        return $content;
    }

    /**
     * @param array<string, mixed> $decoded
     * @return array{first_name:?string,last_name:?string,address:?string,height_cm:?int,weight_kg:?int,gender:?string,age:?int}
     */
    private function normalizeExtracted(array $decoded): array
    {
        return [
            'first_name' => isset($decoded['first_name']) && is_string($decoded['first_name']) ? trim($decoded['first_name']) : null,
            'last_name' => isset($decoded['last_name']) && is_string($decoded['last_name']) ? trim($decoded['last_name']) : null,
            'address' => isset($decoded['address']) && is_string($decoded['address']) ? trim($decoded['address']) : null,
            'height_cm' => $this->parseIntOrNull($decoded['height_cm'] ?? null),
            'weight_kg' => $this->parseIntOrNull($decoded['weight_kg'] ?? null),
            'gender' => isset($decoded['gender']) && is_string($decoded['gender']) && trim($decoded['gender']) !== '' ? trim($decoded['gender']) : null,
            'age' => $this->parseIntOrNull($decoded['age'] ?? null),
        ];
    }

    private function parseIntOrNull(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) round($value);
        }

        if (is_string($value)) {
            $v = trim($value);
            if ($v === '') {
                return null;
            }

            if (is_numeric($v)) {
                return (int) round((float) $v);
            }

            // Try to extract a number from strings like "180 cm" or "75kg".
            if (preg_match('/-?\d+(\.\d+)?/', $v, $m) === 1) {
                return (int) round((float) $m[0]);
            }
        }

        return null;
    }
}

