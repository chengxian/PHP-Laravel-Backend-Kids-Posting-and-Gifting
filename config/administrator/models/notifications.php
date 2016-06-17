<?php

return [
    'title' => 'Notifications',
    'single' => 'Notification',
    'model' => 'App\Notification',

    'columns' => [
        'id',
        'sender' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $email = $value->email;
                    return "<a href=\"/admin/users/$id\">$email</a>";
                }
            }
        ],
        'receiver' => [
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
        'type',
    ],

    'filters' => [
        'from_user_id',
        'to_user_id',
        'email',
        'invite_code',
        'accepted'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'sender' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'receiver' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'child' => [
            'type' => 'relationship',
            'name_field' => 'full_name'
        ],
        'type',
        'text' => [
            'type' => 'textarea'
        ],
        'custom_data' => [
            'type' => 'textarea'
        ],
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];