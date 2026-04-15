<?php

return [
    'activity' => [
        'created'   => 'Atividade criada com sucesso',
        'updated'   => 'Atividade atualizada com sucesso',
        'deleted'   => 'Atividade excluída com sucesso',
        'not_found' => 'Atividade não encontrada',

        'title'         => 'Atividades',
        'create_title'  => 'Criar Nova Atividade',
        'create_submit' => 'Criar Atividade',
        'edit_title'    => 'Editar Atividade: :name',
        'edit_submit'   => 'Atualizar Atividade',
        'empty'         => 'Nenhuma atividade registrada',

        'fields' => [
            'name'        => 'Nome da Atividade',
            'description' => 'Descrição',
        ],
        'placeholders' => [
            'name'        => 'Ex: Reunião de equipe',
            'description' => 'Descrição detalhada da atividade',
        ],

        'stat_cards' => [
            'total' => 'Total de Atividades',
            'today' => 'Criadas Hoje',
        ],

        'validation' => [
            'name_required'   => 'O nome da atividade é obrigatório',
            'name_max'        => 'O nome não pode exceder 255 caracteres',
            'description_max' => 'A descrição não pode exceder 2000 caracteres',
        ],
    ],
    'user' => [
        'created'   => 'Usuário criado com sucesso',
        'updated'   => 'Usuário atualizado com sucesso',
        'deleted'   => 'Usuário excluído com sucesso',
        'not_found' => 'Usuário não encontrado',

        'title'         => 'Usuários',
        'create_title'  => 'Criar Novo Usuário',
        'create_submit' => 'Criar Usuário',
        'edit_title'    => 'Editar Usuário: :name',
        'edit_submit'   => 'Atualizar Usuário',
        'empty'         => 'Nenhum usuário registrado',

        'stat_cards' => [
            'total'  => 'Total de Usuários',
            'active' => 'Usuários Ativos',
        ],
    ],
];
