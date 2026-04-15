<?php

return [
    'tenant' => [
        'created'   => 'Cliente criado com sucesso',
        'updated'   => 'Cliente atualizado com sucesso',
        'deleted'   => 'Cliente excluído com sucesso',
        'not_found' => 'Cliente não encontrado',

        'title'         => 'Clientes',
        'create_title'  => 'Criar Novo Tenant',
        'create_submit' => 'Criar Tenant',
        'edit_title'    => 'Editar Cliente: :name',
        'edit_submit'   => 'Atualizar Cliente',
        'empty'         => 'Nenhum tenant registrado',

        'fields' => [
            'name'        => 'Nome do Cliente',
            'subdomain'   => 'Subdomínio',
            'email'       => 'E-mail de Contato',
            'status'      => 'Status',
            'project'     => 'Projeto',
            'timezone'    => 'Fuso Horário',
            'locale'      => 'Idioma',
            'description' => 'Descrição',
        ],
        'placeholders' => [
            'name'        => 'Ex: Minha Empresa S.A.',
            'subdomain'   => 'Ex: minhaempresa',
            'email'       => 'contato@exemplo.com',
            'description' => 'Informações adicionais sobre o cliente (opcional)',
        ],
        'help' => [
            'subdomain'    => 'Apenas letras minúsculas, números e hífens. Exemplo: minhaempresa.localhost',
            'subdomain_ro' => 'O subdomínio não pode ser alterado',
            'project'      => 'Selecione o projeto que este tenant utilizará',
            'timezone'     => 'Fuso horário padrão para todos os usuários deste tenant',
            'locale'       => 'Idioma padrão da interface para este tenant',
        ],
        'status' => [
            'active'   => 'Ativo',
            'pending'  => 'Pendente',
            'inactive' => 'Inativo',
        ],

        'stat_cards' => [
            'total'    => 'Total de Registros',
            'active'   => 'Ativos',
            'pending'  => 'Pendentes',
            'inactive' => 'Inativos',
        ],

        'columns' => [
            'subdomain' => 'Subdomínio',
            'project'   => 'Projeto',
            'status'    => 'Status',
        ],

        'delete' => [
            'id'         => 'ID',
            'name'       => 'Nome',
            'identifier' => 'Identificador',
            'email'      => 'E-mail',
            'status'     => 'Status',
            'created_at' => 'Criado Em',
        ],

        'validation' => [
            'name_required'      => 'O nome do cliente é obrigatório',
            'subdomain_required' => 'O subdomínio é obrigatório',
            'subdomain_regex'    => 'O subdomínio só pode conter letras minúsculas, números e hífens',
            'subdomain_unique'   => 'Este subdomínio já está em uso',
            'email_required'     => 'O e-mail é obrigatório',
            'email_email'        => 'O e-mail deve ser um endereço válido',
            'status_required'    => 'Por favor selecione um status',
            'status_in'          => 'O status selecionado é inválido',
            'project_required'   => 'Por favor selecione um projeto',
            'project_in'         => 'O projeto selecionado é inválido',
            'locale_required'    => 'Por favor selecione um idioma',
            'locale_in'          => 'O idioma selecionado é inválido',
        ],
    ],

    'user' => [
        'created'   => 'Usuário criado com sucesso',
        'updated'   => 'Usuário atualizado com sucesso',
        'deleted'   => 'Usuário excluído com sucesso',
        'not_found' => 'Usuário não encontrado',
    ],
];
