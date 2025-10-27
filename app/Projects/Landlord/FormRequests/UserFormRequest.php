<?php

namespace App\Projects\Landlord\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;

class UserFormRequest extends BaseFormRequest
{
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', 'Nombre Completo', [
                'placeholder' => 'Ingrese el nombre completo',
                'required' => true,
            ])
            ->email('email', 'Email', [
                'placeholder' => 'correo@ejemplo.com',
                'required' => true,
            ])
            ->password('password', 'Contraseña', [
                'placeholder' => 'Mínimo 8 caracteres',
                'required' => true,
            ])
            ->password('password_confirmation', 'Confirmar Contraseña', [
                'placeholder' => 'Repita la contraseña',
                'required' => true,
            ])
            ->checkbox('enabled', 'Usuario Activo', [
                'checked' => true,
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('PUT')
            ->setAction('#')
            ->text('name', 'Nombre Completo', [
                'placeholder' => 'Ingrese el nombre completo',
                'required' => true,
            ])
            ->email('email', 'Email', [
                'placeholder' => 'correo@ejemplo.com',
                'required' => true,
            ])
            ->checkbox('enabled', 'Usuario Activo');
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $userId ? "unique:users,email,{$userId}" : 'unique:users,email'
            ],
            'enabled' => ['nullable', 'boolean'],
        ];

        if ($this->isCreating()) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'role.required' => 'Debe seleccionar un rol',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'role' => 'rol',
            'active' => 'activo',
        ];
    }
}
