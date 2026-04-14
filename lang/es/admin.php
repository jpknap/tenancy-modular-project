<?php

return [
    'columns' => [
        'id'            => 'ID',
        'name'          => 'Nombre',
        'email'         => 'Email',
        'status'        => 'Estado',
        'active'        => 'Activo',
        'created_at'    => 'Fecha Creación',
        'registered_at' => 'Fecha Registro',
        'subdomain'     => 'Subdominio',
        'project'       => 'Proyecto',
        'description'   => 'Descripción',
    ],
    'actions' => [
        'edit'   => 'Editar',
        'delete' => 'Eliminar',
    ],
    'feedback' => [
        'created'       => 'El registro ha sido creado correctamente',
        'created_title' => '¡Registro creado!',
        'updated'       => 'Los cambios han sido guardados correctamente',
        'updated_title' => '¡Registro actualizado!',
        'deleted'       => 'El registro ha sido eliminado permanentemente',
        'deleted_title' => '¡Registro eliminado!',
    ],
    'stat_cards' => [
        'total'        => 'Total Registros',
        'total_users'  => 'Total Usuarios',
        'active'       => 'Activos',
        'active_users' => 'Usuarios Activos',
        'inactive'     => 'Inactivos',
        'pending'      => 'Pendientes',
        'admins'       => 'Administradores',
        'today'        => 'Creadas Hoy',
    ],
    'empty' => 'No hay registros',
];
