<?php

return [
    'institution' => [
        'created' => 'Institution created successfully',
        'updated' => 'Institution updated successfully',
        'not_found' => 'Institution not found',

        'title' => 'Institutions',
        'create_title' => 'Create New Institution',
        'create_submit' => 'Create Institution',
        'edit_title' => 'Edit Institution: :name',
        'edit_submit' => 'Update Institution',
        'empty' => 'No institutions registered',

        'fields' => [
            'name' => 'Name',
            'description' => 'Description',
            'logo_url' => 'Logo URL',
            'enabled' => 'Enabled',
        ],

        'placeholders' => [
            'name' => 'E.g: Municipal Theater',
            'description' => 'Brief description of the institution',
            'logo_url' => 'https://example.com/logo.png',
        ],

        'validation' => [
            'name_required' => 'The name is required',
            'name_unique' => 'An institution with that name already exists',
            'logo_url_invalid' => 'The logo URL is not valid',
        ],

        'stat_cards' => [
            'total' => 'Total Institutions',
            'enabled' => 'Enabled',
        ],
    ],

    'user' => [
        'created' => 'User created successfully',
        'updated' => 'User updated successfully',
        'deleted' => 'User deleted successfully',
        'not_found' => 'User not found',

        'title' => 'Users',
        'create_title' => 'Create New User',
        'create_submit' => 'Create User',
        'edit_title' => 'Edit User: :name',
        'edit_submit' => 'Update User',
        'empty' => 'No users registered',

        'stat_cards' => [
            'total' => 'Total Users',
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],
];
