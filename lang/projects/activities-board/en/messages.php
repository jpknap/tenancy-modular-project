<?php

return [
    'activity' => [
        'created'   => 'Activity created successfully',
        'updated'   => 'Activity updated successfully',
        'deleted'   => 'Activity deleted successfully',
        'not_found' => 'Activity not found',

        'title'         => 'Activities',
        'create_title'  => 'Create New Activity',
        'create_submit' => 'Create Activity',
        'edit_title'    => 'Edit Activity: :name',
        'edit_submit'   => 'Update Activity',
        'empty'         => 'No activities registered',

        'fields' => [
            'name'        => 'Activity Name',
            'description' => 'Description',
        ],
        'placeholders' => [
            'name'        => 'E.g. Team meeting',
            'description' => 'Detailed description of the activity',
        ],

        'stat_cards' => [
            'total' => 'Total Activities',
            'today' => 'Created Today',
        ],

        'validation' => [
            'name_required'   => 'Activity name is required',
            'name_max'        => 'Name cannot exceed 255 characters',
            'description_max' => 'Description cannot exceed 2000 characters',
        ],
    ],
    'user' => [
        'created'   => 'User created successfully',
        'updated'   => 'User updated successfully',
        'deleted'   => 'User deleted successfully',
        'not_found' => 'User not found',

        'title'         => 'Users',
        'create_title'  => 'Create New User',
        'create_submit' => 'Create User',
        'edit_title'    => 'Edit User: :name',
        'edit_submit'   => 'Update User',
        'empty'         => 'No users registered',

        'stat_cards' => [
            'total'  => 'Total Users',
            'active' => 'Active Users',
        ],
    ],
];
