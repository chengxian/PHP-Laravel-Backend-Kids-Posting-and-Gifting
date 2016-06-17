<?php

return [
    'title' => 'Post Attachments',
    'single' => 'Post Attachment',
    'model' => 'App\PostAttachment',

    'columns' => [
        'id',
        'post' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/posts/$id\">$id</a>";
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
        'post_id',
        'media_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'post' => [
            'type' => 'relationship',
            'name_field' => 'id'
        ],
        'media' => [
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