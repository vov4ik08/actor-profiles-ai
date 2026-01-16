<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexActorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Keep UI/API pagination options in config (`config/pager.php`), not in controllers.
        $options = (array) config('pager.pagination.per_page_options', [10, 25, 50, 100]);

        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', Rule::in($options)],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', (int) config('pager.pagination.default_per_page', 10));
    }

    public function page(): int
    {
        return (int) $this->validated('page', 1);
    }
}

