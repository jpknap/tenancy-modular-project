<?php

return [
    'name'                  => 'Full Name',
    'email'                 => 'Email',
    'password'              => 'Password',
    'password_confirmation' => 'Confirm Password',
    'timezone'              => 'Timezone',
    'locale'                => 'Language',
    'status'                => 'Status',
    'description'           => 'Description',
    'enabled'               => 'Active User',

    'placeholders' => [
        'name'                  => 'E.g. John Doe',
        'email'                 => 'user@example.com',
        'password'              => 'At least 8 characters',
        'password_confirmation' => 'Repeat your password',
        'password_new'          => 'Leave blank to keep current password',
        'description'           => 'Optional description',
    ],

    'help' => [
        'password_min'     => 'At least 8 characters',
        'timezone_inherit' => 'If not selected, inherits the tenant timezone',
        'locale'           => 'Interface language for this user',
    ],
];
