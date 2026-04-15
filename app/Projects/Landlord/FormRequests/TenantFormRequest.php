<?php

namespace App\Projects\Landlord\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;
use App\Common\Services\LocaleService;
use App\Contracts\ProjectInterface;
use App\ProjectManager;
use Illuminate\Validation\Rule;

class TenantFormRequest extends BaseFormRequest
{
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', __('landlord::messages.tenant.fields.name'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.name'),
                'required' => true,
                'help' => __('landlord::messages.tenant.fields.name'),
            ])
            ->text('subdomain', __('landlord::messages.tenant.fields.subdomain'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.subdomain'),
                'required' => true,
                'help' => __('landlord::messages.tenant.help.subdomain'),
                'pattern' => '[a-z0-9-]+',
            ])
            ->email('email', __('landlord::messages.tenant.fields.email'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.email'),
                'required' => true,
            ])
            ->select('status', __('landlord::messages.tenant.fields.status'), [
                'active' => __('landlord::messages.tenant.status.active'),
                'pending' => __('landlord::messages.tenant.status.pending'),
                'inactive' => __('landlord::messages.tenant.status.inactive'),
            ], [
                'required' => true,
            ])
            ->select('current_project', __('landlord::messages.tenant.fields.project'), $this->getAvailableProjects(), [
                'required' => true,
                'help' => __('landlord::messages.tenant.help.project'),
            ])
            ->select('timezone', __('landlord::messages.tenant.fields.timezone'), timezone_options(), [
                'required' => true,
                'help' => __('landlord::messages.tenant.help.timezone'),
            ])
            ->select('locale', __('landlord::messages.tenant.fields.locale'), $this->getAvailableLocales(), [
                'required' => true,
                'help' => __('landlord::messages.tenant.help.locale'),
            ])
            ->textarea('description', __('landlord::messages.tenant.fields.description'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.description'),
                'rows' => 3,
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('PUT')
            ->setAction('#')
            ->text('name', __('landlord::messages.tenant.fields.name'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.name'),
                'required' => true,
            ])
            ->text('subdomain', __('landlord::messages.tenant.fields.subdomain'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.subdomain'),
                'required' => true,
                'readonly' => true,
                'help' => __('landlord::messages.tenant.help.subdomain_ro'),
            ])
            ->email('email', __('landlord::messages.tenant.fields.email'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.email'),
                'required' => true,
            ])
            ->select('status', __('landlord::messages.tenant.fields.status'), [
                'active' => __('landlord::messages.tenant.status.active'),
                'pending' => __('landlord::messages.tenant.status.pending'),
                'inactive' => __('landlord::messages.tenant.status.inactive'),
            ], [
                'required' => true,
            ])
            ->select('current_project', __('landlord::messages.tenant.fields.project'), $this->getAvailableProjects(), [
                'required' => true,
                'help' => __('landlord::messages.tenant.help.project'),
            ])
            ->select('timezone', __('landlord::messages.tenant.fields.timezone'), timezone_options(), [
                'required' => true,
                'help' => __('landlord::messages.tenant.help.timezone'),
            ])
            ->select('locale', __('landlord::messages.tenant.fields.locale'), $this->getAvailableLocales(), [
                'required' => true,
                'help' => __('landlord::messages.tenant.help.locale'),
            ])
            ->textarea('description', __('landlord::messages.tenant.fields.description'), [
                'placeholder' => __('landlord::messages.tenant.placeholders.description'),
                'rows' => 3,
            ]);
    }

    public function rules(): array
    {
        $tenantId = $this->route('id');

        $validProjects = [];
        $projects = ProjectManager::getProjects();
        /** @var class-string<ProjectInterface> $projectClass */
        foreach ($projects as $projectClass) {
            $prefix = $projectClass::getPrefix();
            if ($prefix !== 'landlord') {
                $validProjects[] = $prefix;
            }
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9-]+$/',
                $tenantId
                    ? "unique:domains,subdomain,{$tenantId},tenant_id"
                    : 'unique:domains,subdomain',
            ],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive,pending'],
            'current_project' => ['required', 'string', 'max:255', Rule::in($validProjects)],
            'timezone' => ['required', 'string', 'timezone:all'],
            'locale' => ['required', 'string', 'in:' . implode(',', LocaleService::SUPPORTED)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('landlord::messages.tenant.validation.name_required'),
            'subdomain.required' => __('landlord::messages.tenant.validation.subdomain_required'),
            'subdomain.regex' => __('landlord::messages.tenant.validation.subdomain_regex'),
            'subdomain.unique' => __('landlord::messages.tenant.validation.subdomain_unique'),
            'email.required' => __('landlord::messages.tenant.validation.email_required'),
            'email.email' => __('landlord::messages.tenant.validation.email_email'),
            'status.required' => __('landlord::messages.tenant.validation.status_required'),
            'status.in' => __('landlord::messages.tenant.validation.status_in'),
            'current_project.required' => __('landlord::messages.tenant.validation.project_required'),
            'current_project.in' => __('landlord::messages.tenant.validation.project_in'),
            'locale.required' => __('landlord::messages.tenant.validation.locale_required'),
            'locale.in' => __('landlord::messages.tenant.validation.locale_in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('landlord::messages.tenant.fields.name'),
            'subdomain' => __('landlord::messages.tenant.fields.subdomain'),
            'email' => __('landlord::messages.tenant.fields.email'),
            'status' => __('landlord::messages.tenant.fields.status'),
            'current_project' => __('landlord::messages.tenant.fields.project'),
            'description' => __('landlord::messages.tenant.fields.description'),
            'locale' => __('landlord::messages.tenant.fields.locale'),
        ];
    }

    private function getAvailableLocales(): array
    {
        return LocaleService::options();
    }

    private function getAvailableProjects(): array
    {
        $projects = ProjectManager::getProjects();
        $projectOptions = [];

        /** @var class-string<ProjectInterface> $projectClass */
        foreach ($projects as $projectClass) {
            $prefix = $projectClass::getPrefix();

            if ($prefix !== 'landlord') {
                $projectOptions[$prefix] = $projectClass::getTitle();
            }
        }

        return $projectOptions;
    }
}
