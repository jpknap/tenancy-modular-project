<?php

namespace App\Projects\ActivitiesBoard\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;

class UserFormRequest extends BaseFormRequest
{
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', __('fields.name'), [
                'placeholder' => __('fields.placeholders.name'),
                'required'    => true,
            ])
            ->email('email', __('fields.email'), [
                'placeholder' => __('fields.placeholders.email'),
                'required'    => true,
            ])
            ->password('password', __('fields.password'), [
                'placeholder' => __('fields.placeholders.password'),
                'required'    => true,
                'help'        => __('fields.help.password_min'),
            ])
            ->password('password_confirmation', __('fields.password_confirmation'), [
                'placeholder' => __('fields.placeholders.password_confirmation'),
                'required'    => true,
            ])
            ->select('timezone', __('fields.timezone'), timezone_options(withBlank: true), [
                'help' => __('fields.help.timezone_inherit'),
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('PUT')
            ->setAction('#')
            ->text('name', __('fields.name'), [
                'placeholder' => __('fields.placeholders.name'),
                'required'    => true,
            ])
            ->email('email', __('fields.email'), [
                'placeholder' => __('fields.placeholders.email'),
                'required'    => true,
            ])
            ->password('password', __('fields.password'), [
                'placeholder' => __('fields.placeholders.password_new'),
                'required'    => false,
                'help'        => __('fields.placeholders.password_new'),
            ])
            ->password('password_confirmation', __('fields.password_confirmation'), [
                'placeholder' => __('fields.placeholders.password_confirmation'),
                'required'    => false,
            ])
            ->select('timezone', __('fields.timezone'), timezone_options(withBlank: true), [
                'help' => __('fields.help.timezone_inherit'),
            ]);
    }

    public function rules(): array
    {
        $userId    = $this->route('id');
        $isEditing = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'email',
                'max:255',
                $userId ? "unique:users,email,{$userId}" : 'unique:users,email',
            ],
            'timezone' => ['nullable', 'string', 'timezone:all'],
        ];

        $rules['password'] = $isEditing
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'      => __('fields.name') . ' es obligatorio',
            'email.required'     => __('fields.email') . ' es obligatorio',
            'email.email'        => __('fields.email') . ' debe ser válido',
            'email.unique'       => __('fields.email') . ' ya está registrado',
            'password.required'  => __('fields.password') . ' es obligatoria',
            'password.min'       => __('fields.help.password_min'),
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => __('fields.name'),
            'email'    => __('fields.email'),
            'password' => __('fields.password'),
            'timezone' => __('fields.timezone'),
        ];
    }
}
