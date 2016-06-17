<?php

return [
    'title' => 'Dwolla Events',
    'single' => 'Dwolla Event',
    'model' => 'Kidgifting\DwollaWrapper\Models\DwollaEvent',

    'columns' => [
        'id',
        'dId',
        'resourceId',
        'topic',
        'timestamp',
        'verified',
        'processed',
        'created_at'
    ],

    'filters' => [
        'id',
        'dId',
        'resourceId',
        'topic'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'dId',
        'resourceId',
        'topic',
        'timestamp' => [
            'type' => 'datetime'
        ],
        'verified',
        'processed',
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ],
        'raw' => [
            'type' => 'textarea'
        ]
    ]
];