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
            ->text('name', __('activities-board::messages.activity.fields.name'), [
                'placeholder' => __('activities-board::messages.activity.placeholders.name'),
                'required' => true,
            ])
            ->textarea('description', __('activities-board::messages.activity.fields.description'), [
                'placeholder' => __('activities-board::messages.activity.placeholders.description'),
                'rows' => 4,
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->buildCreateForm()
            ->setMethod('PUT');
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
            'name.required' => __('activities-board::messages.activity.validation.name_required'),
            'name.max' => __('activities-board::messages.activity.validation.name_max'),
            'description.max' => __('activities-board::messages.activity.validation.description_max'),
        ];
    }
}
