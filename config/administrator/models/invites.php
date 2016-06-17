<?php

return [
    'title' => 'Invites',
    'single' => 'Invite',
    'model' => 'App\Invite',

    'columns' => [
        'id',
        'toUser' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $email = $value->email;
                    return "<a href=\"/admin/users/$id\">$email</a>";
                }
            }
        ],
        'email',
        'fromUser' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $email = $value->email;
                    return "<a href=\"/admin/users/$id\">$email</a>";
                }
            }
        ],
        'invite_code',
        'accepted'
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
        'toUser' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'email',
        'fromUser' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'invite_code',
        'accepted' => [
            'type' => 'bool'
        ],
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];