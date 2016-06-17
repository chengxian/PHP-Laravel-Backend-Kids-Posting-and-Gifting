<?php

return [
    'title' => 'Comment Attachements',
    'single' => 'Comment Attachement',
    'model' => 'App\CommentAttachment',

    'columns' => [
        'id',
        'comment' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/comments/$id\">$id</a>";
                }
            }
        ],
        'media' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/media/$id\">$id</a>";
                }
            }
        ]
    ],

    'filters' => [
        'id',
        'comment_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'comment' => [
            'type' => 'relationship',
            'name_field' => 'id'
        ],
        'media' => [
            'type' => 'relationship',
            'name_field' => 'id'
        ],

    ]
];