<?php

return [
    'tenant' => [
        'created'   => 'Client created successfully',
        'updated'   => 'Client updated successfully',
        'deleted'   => 'Client deleted successfully',
        'not_found' => 'Client not found',

        'title'         => 'Clients',
        'create_title'  => 'Create New Tenant',
        'create_submit' => 'Create Tenant',
        'edit_title'    => 'Edit Client: :name',
        'edit_submit'   => 'Update Client',
        'empty'         => 'No tenants registered',

        'fields' => [
            'name'        => 'Client Name',
            'subdomain'   => 'Subdomain',
            'email'       => 'Contact Email',
            'status'      => 'Status',
            'project'     => 'Project',
            'timezone'    => 'Timezone',
            'locale'      => 'Language',
            'description' => 'Description',
        ],
        'placeholders' => [
            'name'        => 'E.g. My Company Inc.',
            'subdomain'   => 'E.g. mycompany',
            'email'       => 'contact@example.com',
            'description' => 'Additional information about the client (optional)',
        ],
        'help' => [
            'subdomain'    => 'Lowercase letters, numbers and hyphens only. Example: mycompany.localhost',
            'subdomain_ro' => 'The subdomain cannot be changed',
            'project'      => 'Select the project this tenant will use',
            'timezone'     => 'Default timezone for all users in this tenant',
            'locale'       => 'Default interface language for this tenant',
        ],
        'status' => [
            'active'   => 'Active',
            'pending'  => 'Pending',
            'inactive' => 'Inactive',
        ],

        'stat_cards' => [
            'total'    => 'Total Records',
            'active'   => 'Active',
            'pending'  => 'Pending',
            'inactive' => 'Inactive',
        ],

        'columns' => [
            'subdomain' => 'Subdomain',
            'project'   => 'Project',
            'status'    => 'Status',
        ],

        'delete' => [
            'id'         => 'ID',
            'name'       => 'Name',
            'identifier' => 'Identifier',
            'email'      => 'Email',
            'status'     => 'Status',
            'created_at' => 'Created At',
        ],

        'validation' => [
            'name_required'      => 'Client name is required',
            'subdomain_required' => 'Subdomain is required',
            'subdomain_regex'    => 'Subdomain may only contain lowercase letters, numbers and hyphens',
            'subdomain_unique'   => 'This subdomain is already in use',
            'email_required'     => 'Email is required',
            'email_email'        => 'Email must be a valid address',
            'status_required'    => 'Please select a status',
            'status_in'          => 'The selected status is invalid',
            'project_required'   => 'Please select a project',
            'project_in'         => 'The selected project is invalid',
            'locale_required'    => 'Please select a language',
            'locale_in'          => 'The selected language is invalid',
        ],
    ],

    'user' => [
        'created'   => 'User created successfully',
        'updated'   => 'User updated successfully',
        'deleted'   => 'User deleted successfully',
        'not_found' => 'User not found',
    ],
];
