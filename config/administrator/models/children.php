<?php

return [
    'title' => 'Children',
    'single' => 'child',
    'model' => 'App\Child',

    'columns' => [
        'id',
        'first_name',
        'last_name',
        'loanApplication' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/usaloanapplications/$id\">$id</a>";
                }
            }
        ],
        'savingsAccount' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/dwolladestinationaccounts/$id\">$id</a>";
                }
            }
        ],
        'fundable',
        'updated_at'
    ],

    'filters' => [
        'id',
        'first_name',
        'last_name'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'parent'  => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'first_name',
        'last_name',
//        'birthday' => [
//            'type' => 'date',
//            'date_format' => 'yyyy-m-d'
//        ],
        'wants' => [
            'type' => 'textarea'
        ],
//        'age',
        'fundable' => [
            'type' => 'bool'
        ],
        'loanApplication'  => [
            'type' => 'relationship',
            'name_field' => 'loan_number'
        ],
        'savingsAccount' => [
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