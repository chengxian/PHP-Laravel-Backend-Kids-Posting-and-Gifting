<?php

return [
    'title' => 'Dwolla Source Accounts',
    'single' => 'Dwolla Source Account',
    'model' => 'Kidgifting\DwollaWrapper\Models\DwollaSourceAccount',

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
        $query->whereType('source');
    },
];