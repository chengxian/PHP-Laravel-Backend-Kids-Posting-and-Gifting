<?php

return [
    'title' => 'Dwolla Customer Accounts',
    'single' => 'Dwolla Customer Account',
    'model' => 'Kidgifting\DwollaWrapper\Models\DwollaBaseCustomer',

    'columns' => [
        'id',
        'dwolla_id',
        'type',
        'status',
        'user' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $email = $value->email;
                    return "<a href=\"/admin/users/$id\">$email</a>";
                }
            }
        ]
    ],

    'filters' => [
        'id',
        'dwolla_id',
        'type',
        'status',
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'dwolla_id' => [
            'title' => 'Dwolla ID',
            'type' => 'text',
            'editable' => false
        ],
        'type',
        'status',
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ],

    'query_filter' => function ($query) {
        $query->whereType('source');
    },
];