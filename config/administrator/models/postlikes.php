<?php

return [
    'title' => 'Likes',
    'single' => 'Like',
    'model' => 'App\PostLike',

    'columns' => [
        'id',
        'user' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/users/$id\">$id</a>";
                }
            }
        ],
        'post' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/posts/$id\">$id</a>";
                }
            }
        ],
    ],

    'filters' => [
        'id',
        'user_id',
        'post_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'user' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'post' => [
            'type' => 'relationship',
            'name_field' => 'id'
        ],
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];