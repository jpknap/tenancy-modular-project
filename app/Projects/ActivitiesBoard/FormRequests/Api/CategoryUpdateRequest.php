<?php

namespace App\Projects\ActivitiesBoard\FormRequests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update parcial: cualquier subconjunto de atributos editables. Ver
 * ActivityUpdateRequest para el mismo criterio con `sometimes`.
 */
class CategoryUpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'color' => ['sometimes', 'nullable', 'string', 'max:7'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'archived_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
