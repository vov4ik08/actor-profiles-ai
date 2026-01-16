<?php

namespace App\Services;

use App\Contracts\ActorExtractorInterface;
use App\Exceptions\MissingActorFieldsException;
use App\Exceptions\OpenAiExtractionException;
use App\Services\ActorExtraction\ActorExtractionPrompt;
use App\Services\ActorExtraction\ActorExtractionResponseParser;
use App\Services\OpenAi\OpenAiJsonChat;

final readonly class OpenAiActorExtractor implements ActorExtractorInterface
{
    /**
     * @return void:?string,last_name:?string,address:?string,height_cm:?int,weight_kg:?int,gender:?string,age:?int}
     */
    public function __construct(
        private OpenAiJsonChat $client,
        private ActorExtractionPrompt $prompt,
        private ActorExtractionResponseParser $parser,
    ) {
    }

    /**
     * @return array{first_name:?string,last_name:?string,address:?string,height_cm:?int,weight_kg:?int,gender:?string,age:?int}
     */
    public function extract(string $description): array
    {
        $model = (string) config('services.openai.model', 'gpt-4o-mini');

        $content = $this->client->chatJsonObject(
            model: $model,
            systemPrompt: $this->prompt->text(),
            userMessage: $description,
        );

        return $this->parser->parse($content);
    }

    /**
     * @return array{first_name:?string,last_name:?string,address:?string,height_cm:?int,weight_kg:?int,gender:?string,age:?int}
     */
    public function extractRequired(string $description): array
    {
        $extracted = $this->extract($description);

        if (
            empty($extracted['first_name']) ||
            empty($extracted['last_name']) ||
            empty($extracted['address'])
        ) {
            throw new MissingActorFieldsException();
        }

        return $extracted;
    }
}

