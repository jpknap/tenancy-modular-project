<?php

return [
    'name'                  => 'Nombre Completo',
    'email'                 => 'Correo Electrónico',
    'password'              => 'Contraseña',
    'password_confirmation' => 'Confirmar Contraseña',
    'timezone'              => 'Zona Horaria',
    'locale'                => 'Idioma',
    'status'                => 'Estado',
    'description'           => 'Descripción',
    'enabled'               => 'Usuario Activo',

    'placeholders' => [
        'name'                  => 'Ej: Juan Pérez',
        'email'                 => 'usuario@ejemplo.com',
        'password'              => 'Mínimo 8 caracteres',
        'password_confirmation' => 'Repita la contraseña',
        'password_new'          => 'Dejar en blanco para mantener la contraseña actual',
        'description'           => 'Descripción opcional',
    ],

    'help' => [
        'password_min'     => 'Mínimo 8 caracteres',
        'timezone_inherit' => 'Si no se selecciona, hereda la zona horaria del tenant',
        'locale'           => 'Idioma de la interfaz para este usuario',
    ],
];
