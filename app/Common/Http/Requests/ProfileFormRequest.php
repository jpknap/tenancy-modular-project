<?php

namespace App\Common\Http\Requests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;
use App\Common\Services\LocaleService;

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
            ->text('name', __('profile.fields.name'), [
                'placeholder' => __('fields.placeholders.name'),
                'required'    => true,
            ])
            ->email('email', __('profile.fields.email'), [
                'placeholder' => __('fields.placeholders.email'),
                'required'    => true,
            ])
            ->select('locale', __('profile.fields.locale'), LocaleService::options(), [
                'help' => __('profile.fields.locale_help'),
            ]);
    }

    public function rules(): array
    {
        $userId = collect(array_keys(config('auth.guards')))
            ->map(fn ($guard) => auth()->guard($guard)->id())
            ->filter()
            ->first();

        return [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', "unique:users,email,{$userId}"],
            'locale' => ['nullable', 'string', 'in:' . implode(',', LocaleService::SUPPORTED)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => __('fields.name') . ' es obligatorio.',
            'email.required' => __('fields.email') . ' es obligatorio.',
            'email.unique'   => __('fields.email') . ' ya está en uso por otra cuenta.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'   => __('fields.name'),
            'email'  => __('fields.email'),
            'locale' => __('fields.locale'),
        ];
    }
}
