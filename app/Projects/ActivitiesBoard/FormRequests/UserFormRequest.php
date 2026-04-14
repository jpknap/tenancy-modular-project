<?php

namespace App\Projects\ActivitiesBoard\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;
use App\Enums\UserRole;

class UserFormRequest extends BaseFormRequest
{
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', 'Nombre Completo', [
                'placeholder' => 'Ej: Juan Pérez',
                'required' => true,
            ])
            ->email('email', 'Correo Electrónico', [
                'placeholder' => 'usuario@ejemplo.com',
                'required' => true,
            ])
            ->password('password', 'Contraseña', [
                'placeholder' => '********',
                'required' => true,
                'help' => 'Mínimo 8 caracteres',
            ])
            ->password('password_confirmation', 'Confirmar Contraseña', [
                'placeholder' => '********',
                'required' => true,
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('PUT')
            ->setAction('#')
            ->text('name', 'Nombre Completo', [
                'placeholder' => 'Ej: Juan Pérez',
                'required' => true,
            ])
            ->email('email', 'Correo Electrónico', [
                'placeholder' => 'usuario@ejemplo.com',
                'required' => true,
            ])
            ->password('password', 'Nueva Contraseña', [
                'placeholder' => '********',
                'required' => false,
                'help' => 'Dejar en blanco para mantener la contraseña actual',
            ])
            ->password('password_confirmation', 'Confirmar Contraseña', [
                'placeholder' => '********',
                'required' => false,
            ])
            ->select('role', 'Rol', UserRole::options(), ['required' => false, 'can' => 'roles:assign']);
    }

    public function rules(): array
    {
        $userId = $this->route('id');
        $isEditing = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $userId ? "unique:users,email,{$userId}" : 'unique:users,email'
            ],
        ];

        if ($isEditing) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
            $rules['role'] = ['nullable', 'string', 'in:' . implode(',', UserRole::values())];
        } else {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Debe ser un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }
}
