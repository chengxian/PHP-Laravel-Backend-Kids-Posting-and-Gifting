<?php

return [
    'title' => 'Devices',
    'single' => 'Device',
    'model' => 'App\Device',

    'columns' => [
        'id',
        'device_token',
        'badge',
        'channel',
        'user' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $email = $value->email;
                    return "<a href=\"/admin/users/$id\">$email</a>";
                }
            }
        ],
    ],

    'filters' => [
        'id',
        'device_token',
        'user_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'device_token',
        'badge',
        'channel',
        'user' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ],
    ]
];