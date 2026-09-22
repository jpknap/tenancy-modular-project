<?php

namespace App\Projects\TicketsValdi\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;
use Illuminate\Validation\Rule;

class InstitutionFormRequest extends BaseFormRequest
{
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', __('tickets-valdi::messages.institution.fields.name'), [
                'placeholder' => __('tickets-valdi::messages.institution.placeholders.name'),
                'required' => true,
            ])
            ->textarea('description', __('tickets-valdi::messages.institution.fields.description'), [
                'placeholder' => __('tickets-valdi::messages.institution.placeholders.description'),
                'rows' => 4,
            ])
            ->text('logo_url', __('tickets-valdi::messages.institution.fields.logo_url'), [
                'placeholder' => __('tickets-valdi::messages.institution.placeholders.logo_url'),
            ])
            ->checkbox('enabled', __('tickets-valdi::messages.institution.fields.enabled'), [
                'checked' => true,
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->buildCreateForm()
            ->setMethod('PUT');
    }

    public function rules(): array
    {
        $institutionId = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institutions', 'name')->ignore($institutionId),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo_url' => ['nullable', 'url', 'max:2048'],
            'enabled' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('tickets-valdi::messages.institution.validation.name_required'),
            'name.unique' => __('tickets-valdi::messages.institution.validation.name_unique'),
            'logo_url.url' => __('tickets-valdi::messages.institution.validation.logo_url_invalid'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('tickets-valdi::messages.institution.fields.name'),
            'description' => __('tickets-valdi::messages.institution.fields.description'),
            'logo_url' => __('tickets-valdi::messages.institution.fields.logo_url'),
            'enabled' => __('tickets-valdi::messages.institution.fields.enabled'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'enabled' => $this->boolean('enabled'),
        ]);
    }
}
