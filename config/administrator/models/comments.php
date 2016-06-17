<?php

return [
    'title' => 'Comments',
    'single' => 'Comment',
    'model' => 'App\Comment',

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
        'post_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'comment' => [
            'type' => 'textarea'
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
        ],
    ]
];