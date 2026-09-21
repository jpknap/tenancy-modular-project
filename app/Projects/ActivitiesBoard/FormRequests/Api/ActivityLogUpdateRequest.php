<?php

namespace App\Projects\ActivitiesBoard\FormRequests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'occurred_at' => ['sometimes', 'date'],
            'note' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
