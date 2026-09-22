<?php

return [
    'institution' => [
        'created' => 'Instituição criada com sucesso',
        'updated' => 'Instituição atualizada com sucesso',
        'not_found' => 'Instituição não encontrada',

        'title' => 'Instituições',
        'create_title' => 'Criar Nova Instituição',
        'create_submit' => 'Criar Instituição',
        'edit_title' => 'Editar Instituição: :name',
        'edit_submit' => 'Atualizar Instituição',
        'empty' => 'Nenhuma instituição registrada',

        'fields' => [
            'name' => 'Nome',
            'description' => 'Descrição',
            'logo_url' => 'URL do logo',
            'enabled' => 'Habilitada',
        ],

        'placeholders' => [
            'name' => 'Ex: Teatro Municipal',
            'description' => 'Breve descrição da instituição',
            'logo_url' => 'https://exemplo.com/logo.png',
        ],

        'validation' => [
            'name_required' => 'O nome é obrigatório',
            'name_unique' => 'Já existe uma instituição com esse nome',
            'logo_url_invalid' => 'A URL do logo não é válida',
        ],

        'stat_cards' => [
            'total' => 'Total de Instituições',
            'enabled' => 'Habilitadas',
        ],
    ],

    'user' => [
        'created' => 'Usuário criado com sucesso',
        'updated' => 'Usuário atualizado com sucesso',
        'deleted' => 'Usuário excluído com sucesso',
        'not_found' => 'Usuário não encontrado',

        'title' => 'Usuários',
        'create_title' => 'Criar Novo Usuário',
        'create_submit' => 'Criar Usuário',
        'edit_title' => 'Editar Usuário: :name',
        'edit_submit' => 'Atualizar Usuário',
        'empty' => 'Nenhum usuário registrado',

        'stat_cards' => [
            'total' => 'Total de Usuários',
            'active' => 'Ativos',
            'inactive' => 'Inativos',
        ],
    ],
];
