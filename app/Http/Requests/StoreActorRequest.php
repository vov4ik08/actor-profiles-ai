<?php

namespace App\Http\Requests;

use App\Rules\UniqueActorDescription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $description = (string) $this->input('description', '');
        $normalized = trim(preg_replace('/\s+/', ' ', $description) ?? $description);

        $this->merge([
            'description' => $normalized,
            'description_hash' => hash('sha256', $normalized),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::unique('actors', 'email')],
            'description' => ['required', app(UniqueActorDescription::class)],
            'description_hash' => ['required', 'string', 'size:64'],
        ];
    }
}

