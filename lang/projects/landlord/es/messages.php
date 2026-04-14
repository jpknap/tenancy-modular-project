<?php

return [
    'tenant' => [
        'created'   => 'Cliente creado correctamente',
        'updated'   => 'Cliente actualizado correctamente',
        'deleted'   => 'Cliente eliminado correctamente',
        'not_found' => 'Cliente no encontrado',

        'title'          => 'Clientes',
        'create_title'   => 'Crear Nuevo Tenant',
        'create_submit'  => 'Crear Tenant',
        'edit_title'     => 'Editar Cliente: :name',
        'edit_submit'    => 'Actualizar Cliente',
        'empty'          => 'No hay tenants registrados',

        'fields' => [
            'name'            => 'Nombre del Cliente',
            'subdomain'       => 'Subdominio',
            'email'           => 'Email de Contacto',
            'status'          => 'Estado',
            'project'         => 'Proyecto',
            'timezone'        => 'Zona Horaria',
            'locale'          => 'Idioma',
            'description'     => 'Descripción',
        ],
        'placeholders' => [
            'name'        => 'Ej: Mi Empresa S.A.',
            'subdomain'   => 'Ej: miempresa',
            'email'       => 'contacto@ejemplo.com',
            'description' => 'Información adicional sobre el cliente (opcional)',
        ],
        'help' => [
            'subdomain'    => 'Solo letras minúsculas, números y guiones. Ejemplo: miempresa.localhost',
            'subdomain_ro' => 'El subdominio no puede ser modificado',
            'project'      => 'Seleccione el proyecto que utilizará este tenant',
            'timezone'     => 'Zona horaria predeterminada para todos los usuarios del tenant',
            'locale'       => 'Idioma predeterminado de la interfaz para este tenant',
        ],
        'status' => [
            'active'   => 'Activo',
            'pending'  => 'Pendiente',
            'inactive' => 'Inactivo',
        ],

        'stat_cards' => [
            'total'    => 'Total Registros',
            'active'   => 'Activos',
            'pending'  => 'Pendientes',
            'inactive' => 'Inactivos',
        ],

        'columns' => [
            'subdomain' => 'Subdominio',
            'project'   => 'Proyecto',
            'status'    => 'Estado',
        ],

        'delete' => [
            'id'          => 'ID',
            'name'        => 'Nombre',
            'identifier'  => 'Identificador',
            'email'       => 'Email',
            'status'      => 'Estado',
            'created_at'  => 'Fecha de Creación',
        ],

        'validation' => [
            'name_required'        => 'El nombre del cliente es obligatorio',
            'subdomain_required'   => 'El subdominio es obligatorio',
            'subdomain_regex'      => 'El subdominio solo puede contener letras minúsculas, números y guiones',
            'subdomain_unique'     => 'Este subdominio ya está en uso',
            'email_required'       => 'El email es obligatorio',
            'email_email'          => 'El email debe ser una dirección válida',
            'status_required'      => 'Debe seleccionar un estado',
            'status_in'            => 'El estado seleccionado no es válido',
            'project_required'     => 'Debe seleccionar un proyecto',
            'project_in'           => 'El proyecto seleccionado no es válido',
            'locale_required'      => 'Debe seleccionar un idioma',
            'locale_in'            => 'El idioma seleccionado no es válido',
        ],
    ],

    'user' => [
        'created'   => 'Usuario creado correctamente',
        'updated'   => 'Usuario actualizado correctamente',
        'deleted'   => 'Usuario eliminado correctamente',
        'not_found' => 'Usuario no encontrado',
    ],
];
