<?php

namespace App\Common\Http\Controller\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest plano para el login de la API (NO extiende BaseFormRequest,
 * que está acoplado al builder de formularios del Admin/CRUD).
 */
class LoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
