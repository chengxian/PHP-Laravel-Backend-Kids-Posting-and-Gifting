<?php

return [
    'title' => 'Posts',
    'single' => 'Post',
    'model' => 'App\Post',

    'columns' => [
        'id',
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
                    return "<a href=\"/admin/users/$id\">$firstName $lastName</a>";
                }
            }
        ],
        'title',
    ],

    'filters' => [
        'id',
        'user_id',
        'child_id',
        'title'
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
        'title',
        'text' => [
            'type' => 'textarea'
        ],
        'uuis',
        'likes' => [
            'editable' => false
        ],
        'comment_count' => [
            'editable' => false
        ],
        'comments' => [
            'type' => 'relationship',
            'name_field' => 'id'
        ],
//        'attachements' => [
//            'type' => 'relationship',
//            'name_field' => 'id'
//        ],
//        'gifts' => [
//            'type' => 'relationship',
//            'name_field' => 'id'
//        ]
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];