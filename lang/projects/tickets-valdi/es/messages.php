<?php

return [
    'institution' => [
        'created' => 'Institución creada correctamente',
        'updated' => 'Institución actualizada correctamente',
        'not_found' => 'Institución no encontrada',

        'title' => 'Instituciones',
        'create_title' => 'Crear Nueva Institución',
        'create_submit' => 'Crear Institución',
        'edit_title' => 'Editar Institución: :name',
        'edit_submit' => 'Actualizar Institución',
        'empty' => 'No hay instituciones registradas',

        'fields' => [
            'name' => 'Nombre',
            'description' => 'Descripción',
            'logo_url' => 'URL del logo',
            'enabled' => 'Habilitada',
        ],

        'placeholders' => [
            'name' => 'Ej: Teatro Municipal',
            'description' => 'Breve descripción de la institución',
            'logo_url' => 'https://ejemplo.com/logo.png',
        ],

        'validation' => [
            'name_required' => 'El nombre es obligatorio',
            'name_unique' => 'Ya existe una institución con ese nombre',
            'logo_url_invalid' => 'La URL del logo no es válida',
        ],

        'stat_cards' => [
            'total' => 'Total Instituciones',
            'enabled' => 'Habilitadas',
        ],
    ],

    'user' => [
        'created' => 'Usuario creado correctamente',
        'updated' => 'Usuario actualizado correctamente',
        'deleted' => 'Usuario eliminado correctamente',
        'not_found' => 'Usuario no encontrado',

        'title' => 'Usuarios',
        'create_title' => 'Crear Nuevo Usuario',
        'create_submit' => 'Crear Usuario',
        'edit_title' => 'Editar Usuario: :name',
        'edit_submit' => 'Actualizar Usuario',
        'empty' => 'No hay usuarios registrados',

        'stat_cards' => [
            'total' => 'Total Usuarios',
            'active' => 'Activos',
            'inactive' => 'Inactivos',
        ],
    ],
];
