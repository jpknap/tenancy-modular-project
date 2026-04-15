<?php

return [
    'name'                  => 'Nome Completo',
    'email'                 => 'E-mail',
    'password'              => 'Senha',
    'password_confirmation' => 'Confirmar Senha',
    'timezone'              => 'Fuso Horário',
    'locale'                => 'Idioma',
    'status'                => 'Status',
    'description'           => 'Descrição',
    'enabled'               => 'Usuário Ativo',

    'placeholders' => [
        'name'                  => 'Ex: João Silva',
        'email'                 => 'usuario@exemplo.com',
        'password'              => 'No mínimo 8 caracteres',
        'password_confirmation' => 'Repita a senha',
        'password_new'          => 'Deixe em branco para manter a senha atual',
        'description'           => 'Descrição opcional',
    ],

    'help' => [
        'password_min'     => 'No mínimo 8 caracteres',
        'timezone_inherit' => 'Se não selecionado, herda o fuso horário do tenant',
        'locale'           => 'Idioma da interface para este usuário',
    ],
];
