<?php

namespace App\Projects\Landlord\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;

class TenantFormRequest extends BaseFormRequest
{
    public function buildForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', 'Nombre del Tenant', [
                'placeholder' => 'Ingrese el nombre del tenant',
                'required' => true,
            ])
            ->email('email', 'Email', [
                'placeholder' => 'correo@ejemplo.com',
                'required' => true,
            ])
            ->select('status', 'Estado', [
                'active' => 'Activo',
                'inactive' => 'Inactivo',
                'pending' => 'Pendiente',
            ], [
                'required' => true,
            ])
            ->textarea('description', 'Descripción', [
                'placeholder' => 'Descripción opcional del tenant',
                'rows' => 4,
            ])
            ->date('start_date', 'Fecha de Inicio', [
                'required' => true,
            ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive,pending'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del tenant es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser una dirección válida',
            'status.required' => 'Debe seleccionar un estado',
            'status.in' => 'El estado seleccionado no es válido',
            'start_date.required' => 'La fecha de inicio es obligatoria',
            'start_date.after_or_equal' => 'La fecha debe ser hoy o posterior',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'status' => 'estado',
            'description' => 'descripción',
            'start_date' => 'fecha de inicio',
        ];
    }
}
