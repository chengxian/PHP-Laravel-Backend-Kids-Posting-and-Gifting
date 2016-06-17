<?php

return [
    'title' => 'Settings',
    'single' => 'setting',
    'model' => 'App\Setting',

    'columns' => [
        'id',
        'notification_frequency',
        'user' => [
            'title' => 'User',
            'relationship' => 'user',
            'select' => "(:table).email"
        ]
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'user' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'allow_notification' => [
            'type' => 'bool'
        ],
        'notification_frequency',
        'donation_percent',
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];