<?php

namespace App\Projects\Landlord\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;
use App\ProjectManager;
use App\Contracts\ProjectInterface;
use Illuminate\Validation\Rule;

class TenantFormRequest extends BaseFormRequest
{
    /**
     * Obtener proyectos disponibles (excluyendo landlord)
     */
    private function getAvailableProjects(): array
    {
        $projects = ProjectManager::getProjects();
        $projectOptions = [];

        /** @var class-string<ProjectInterface> $projectClass */
        foreach ($projects as $projectClass) {
            $prefix = $projectClass::getPrefix();
            
            // Excluir el proyecto landlord
            if ($prefix !== 'landlord') {
                $projectOptions[$prefix] = $projectClass::getTitle();
            }
        }

        return $projectOptions;
    }

    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction('#')
            ->text('name', 'Nombre del Cliente', [
                'placeholder' => 'Ej: Mi Empresa S.A.',
                'required' => true,
                'help' => 'Nombre completo de la organización',
            ])
            ->text('subdomain', 'Subdominio', [
                'placeholder' => 'Ej: miempresa',
                'required' => true,
                'help' => 'Solo letras minúsculas, números y guiones. Ejemplo: miempresa.localhost',
                'pattern' => '[a-z0-9-]+',
            ])
            ->email('email', 'Email de Contacto', [
                'placeholder' => 'contacto@ejemplo.com',
                'required' => true,
            ])
            ->select('status', 'Estado', [
                'active' => 'Activo',
                'pending' => 'Pendiente',
                'inactive' => 'Inactivo',
            ], [
                'required' => true,
            ])
            ->select('current_project', 'Proyecto', $this->getAvailableProjects(), [
                'required' => true,
                'help' => 'Seleccione el proyecto que utilizará este tenant',
            ])
            ->textarea('description', 'Descripción', [
                'placeholder' => 'Información adicional sobre el cliente (opcional)',
                'rows' => 3,
            ]);
    }

    public function buildEditForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('PUT')
            ->setAction('#')
            ->text('name', 'Nombre del Cliente', [
                'placeholder' => 'Ej: Mi Empresa S.A.',
                'required' => true,
            ])
            ->text('subdomain', 'Subdominio', [
                'placeholder' => 'Ej: miempresa',
                'required' => true,
                'readonly' => true,
                'help' => 'El subdominio no puede ser modificado',
            ])
            ->email('email', 'Email de Contacto', [
                'placeholder' => 'contacto@ejemplo.com',
                'required' => true,
            ])
            ->select('status', 'Estado', [
                'active' => 'Activo',
                'pending' => 'Pendiente',
                'inactive' => 'Inactivo',
            ], [
                'required' => true,
            ])
            ->select('current_project', 'Proyecto', $this->getAvailableProjects(), [
                'required' => true,
                'help' => 'Seleccione el proyecto que utilizará este tenant',
            ])
            ->textarea('description', 'Descripción', [
                'placeholder' => 'Información adicional sobre el cliente (opcional)',
                'rows' => 3,
            ]);
    }

    public function rules(): array
    {
        $tenantId = $this->route('id');
        
        // Obtener prefixes válidos (excluyendo landlord)
        $validProjects = [];
        $projects = ProjectManager::getProjects();
        /** @var class-string<ProjectInterface> $projectClass */
        foreach ($projects as $projectClass) {
            $prefix = $projectClass::getPrefix();
            if ($prefix !== 'landlord') {
                $validProjects[] = $prefix;
            }
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9-]+$/',
                $tenantId 
                    ? "unique:domains,subdomain,{$tenantId},tenant_id" 
                    : 'unique:domains,subdomain'
            ],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive,pending'],
            'current_project' => ['required', 'string', 'max:255', Rule::in($validProjects)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del cliente es obligatorio',
            'subdomain.required' => 'El subdominio es obligatorio',
            'subdomain.regex' => 'El subdominio solo puede contener letras minúsculas, números y guiones',
            'subdomain.unique' => 'Este subdominio ya está en uso',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser una dirección válida',
            'status.required' => 'Debe seleccionar un estado',
            'status.in' => 'El estado seleccionado no es válido',
            'current_project.required' => 'Debe seleccionar un proyecto',
            'current_project.in' => 'El proyecto seleccionado no es válido',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'subdomain' => 'subdominio',
            'email' => 'correo electrónico',
            'status' => 'estado',
            'current_project' => 'proyecto',
            'description' => 'descripción',
        ];
    }
}
