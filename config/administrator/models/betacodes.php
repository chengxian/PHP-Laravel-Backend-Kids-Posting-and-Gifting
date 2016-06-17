<?php

return [
    'title' => 'Beta Codes',
    'single' => 'beta code',
    'model' => 'App\Betacode',

    'columns' => [
        'id',
        'email',
        'betacode',
        'used',
        'created_at',
    ],

    'filters' => [
        'email'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'email',
        'betacode' => [
            'type' => 'bool'
        ],
        'used' => [
            'type' => 'bool'
        ],
        'created_at' => [
            'editable' => false
        ]
    ]
];