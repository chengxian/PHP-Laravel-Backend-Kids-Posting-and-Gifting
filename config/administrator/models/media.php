<?php

return [
    'title' => 'Media',
    'single' => 'Media',
    'model' => 'App\Media',

    'columns' => [
        'id',
        'url',
        'filename',
        'mime_type'
    ],

    'filters' => [
        'id',
        'url',
        'filename',
        'mime_type'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'url',
        'filename',
        'mime_type',
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];