<?php

namespace App\Services\ActorExtraction;

final class ActorExtractionPrompt
{
    public function text(): string
    {
        return <<<PROMPT
You are an information extraction system.

Extract the following fields from the user's actor description:
- first_name (string)
- last_name (string)
- address (string)
- height_cm (integer, centimeters, null if unknown; if the user provides inches/feet, convert to centimeters)
- weight_kg (integer, kilograms, null if unknown; if the user provides pounds, convert to kilograms)
- gender (string, null if unknown)
- age (integer, null if unknown)

Return ONLY valid JSON (no markdown, no code fences, no additional text).
The JSON must contain exactly these keys:
first_name, last_name, address, height_cm, weight_kg, gender, age

If a field is not present, return null for that field (except first_name/last_name/address which should be null if missing).

IMPORTANT:
- height_cm and weight_kg must be numbers (integers), not strings and not "180 cm"/"75 kg".
PROMPT;
    }
}

