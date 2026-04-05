<?php

namespace App\Common\Http\Requests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;

class ProfileFormRequest extends BaseFormRequest
{
    public function buildCreateForm(): FormBuilder
    {
        return $this->buildProfileForm();
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->buildProfileForm();
    }

    private function buildProfileForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('PUT')
            ->text('name', 'Nombre Completo', [
                'placeholder' => 'Tu nombre completo',
                'required'    => true,
            ])
            ->email('email', 'Correo electrónico', [
                'placeholder' => 'correo@ejemplo.com',
                'required'    => true,
            ]);
    }

    public function rules(): array
    {
        $userId = collect(array_keys(config('auth.guards')))
            ->map(fn ($guard) => auth()->guard($guard)->id())
            ->filter()
            ->first();

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$userId}"],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.unique'   => 'Este email ya está en uso por otra cuenta.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'nombre',
            'email' => 'correo electrónico',
        ];
    }
}
