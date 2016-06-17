<?php

return [
    'title' => 'Following',
    'single' => 'Follow',
    'model' => 'App\Following',

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
        'child' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $firstName = $value->first_name;
                    $lastName = $value->last_name;
                    return "<a href=\"/admin/children/$id\">$firstName $lastName</a>";
                }
            }
        ],
    ],

    'filters' => [
        'id',
        'user_id',
        'child_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'user' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'child' => [
            'type' => 'relationship',
            'name_field' => 'full_name'
        ],
        'created_at' => [
            'editable' => false
        ]
    ]
];