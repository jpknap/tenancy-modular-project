<?php

namespace App\Projects\ActivitiesBoard\FormRequests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update parcial: cualquier subconjunto de atributos editables. Cada campo
 * es `sometimes` (se valida solo si viene en el payload) para soportar
 * PATCH sobre un único atributo sin exigir el resto. `category_ids`, cuando
 * viene, reemplaza el set completo de categorías asignadas (sync).
 */
class ActivityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\ValidationRule>>
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
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['integer', Rule::exists('categories', 'id')->where('user_id', $this->user()->id)],
        ];
    }
}
