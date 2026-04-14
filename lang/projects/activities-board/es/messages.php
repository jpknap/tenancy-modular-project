<?php

return [
    'activity' => [
        'created'   => 'Actividad creada correctamente',
        'updated'   => 'Actividad actualizada correctamente',
        'deleted'   => 'Actividad eliminada correctamente',
        'not_found' => 'Actividad no encontrada',

        'title'         => 'Actividades',
        'create_title'  => 'Crear Nueva Actividad',
        'create_submit' => 'Crear Actividad',
        'edit_title'    => 'Editar Actividad: :name',
        'edit_submit'   => 'Actualizar Actividad',
        'empty'         => 'No hay actividades registradas',

        'fields' => [
            'name'        => 'Nombre de la Actividad',
            'description' => 'Descripción',
        ],
        'placeholders' => [
            'name'        => 'Ej: Reunión de equipo',
            'description' => 'Descripción detallada de la actividad',
        ],

        'stat_cards' => [
            'total' => 'Total Actividades',
            'today' => 'Creadas Hoy',
        ],

        'validation' => [
            'name_required'    => 'El nombre de la actividad es obligatorio',
            'name_max'         => 'El nombre no puede exceder 255 caracteres',
            'description_max'  => 'La descripción no puede exceder 2000 caracteres',
        ],
    ],
    'user' => [
        'created'   => 'Usuario creado correctamente',
        'updated'   => 'Usuario actualizado correctamente',
        'deleted'   => 'Usuario eliminado correctamente',
        'not_found' => 'Usuario no encontrado',

        'title'         => 'Usuarios',
        'create_title'  => 'Crear Nuevo Usuario',
        'create_submit' => 'Crear Usuario',
        'edit_title'    => 'Editar Usuario: :name',
        'edit_submit'   => 'Actualizar Usuario',
        'empty'         => 'No hay usuarios registrados',

        'stat_cards' => [
            'total'  => 'Total Usuarios',
            'active' => 'Usuarios Activos',
        ],
    ],
];
