<?php

return [
    'errors' => [
        'request_failed' => 'OpenAI request failed.',

        // Internal / diagnostic error messages (e.g. logs).
        'api_key_missing' => 'OpenAI API key is not configured.',
        'call_failed' => 'Failed to call OpenAI.',
        'response_empty' => 'OpenAI response is empty.',
        'response_not_json' => 'OpenAI response is not valid JSON.',
        'response_not_object' => 'OpenAI response is not a JSON object.',
    ],
    'logs' => [
        'extraction_failed' => 'OpenAI extraction failed',
    ],
];

