<?php

return [
    'title' => 'Fundables',
    'single' => 'Fundable',
    'model' => 'App\Fundable',

    'columns' => [
        'id',
        'fundable' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/fundables/$id\">$id</a>";
                }
            }
        ],
        'fundable_type',
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
        'user_id',
        'fundable_type',
        'fundable_id',
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'fundable_id',
        'fundable_type',
        'user' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
    ]
];