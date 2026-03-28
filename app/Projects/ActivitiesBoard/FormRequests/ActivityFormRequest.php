<?php

namespace App\Projects\ActivitiesBoard\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;

class ActivityFormRequest extends BaseFormRequest
{
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', 'Nombre de la Actividad', [
                'placeholder' => 'Ej: Reunión de equipo',
                'required' => true,
            ])
            ->textarea('description', 'Descripción', [
                'placeholder' => 'Descripción detallada de la actividad',
                'rows' => 4,
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->buildCreateForm()->setMethod('PUT');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la actividad es obligatorio',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'description.max' => 'La descripción no puede exceder 2000 caracteres',
        ];
    }
}
