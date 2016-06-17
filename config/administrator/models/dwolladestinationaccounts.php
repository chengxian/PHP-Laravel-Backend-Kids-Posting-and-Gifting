<?php

return [
    'title' => 'Dwolla Destination Accounts',
    'single' => 'Dwolla Destination Account',
    'model' => 'Kidgifting\DwollaWrapper\Models\DwollaDestinationAccount',

    'columns' => [
        'id',
        'dwolla_id',
        'type',
        'users' => [
            'output' => function ($value) {
                if ($value && count($value) > 0) {
                    $id = $value[0]->id;
                    $email = $value[0]->email;
                    return "<a href=\"/admin/users/$id\">$email</a>";
                }
            }
        ],
    ],

    'filters' => [
        'id',
        'dwolla_id',
        'type',
        'user_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'dwolla_id' => [
            'title' => 'Dwolla ID',
            'type' => 'text'
        ],
        'type' => [
            'editable' => false
        ],
        'status',
        'child' => [
            'type' => 'relationship',
            'name_field' => 'full_name'
        ],
        'users' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ],
    ],

    'query_filter' => function ($query) {
        $query->whereType('destination');
    },
];