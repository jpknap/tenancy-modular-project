<?php

namespace App\Projects\Landlord\Requests;

use App\Common\Form\BaseFormRequest;
use App\Common\Form\FormBuilder;

class UserFormRequest extends BaseFormRequest
{
    public function buildForm(): FormBuilder
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
            ->select('role', 'Rol', [
                'admin' => 'Administrador',
                'user' => 'Usuario',
                'guest' => 'Invitado',
            ], [
                'required' => true,
            ])
            ->checkbox('active', 'Usuario Activo', [
                'checked' => true,
            ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,user,guest'],
            'active' => ['nullable', 'boolean'],
        ];
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
