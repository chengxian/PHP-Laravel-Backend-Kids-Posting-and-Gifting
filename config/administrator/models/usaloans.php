<?php

return [
    'title' => 'Loan Applications',
    'single' => 'Loan Application',
    'model' => 'Kidgifting\USAlliance\Models\LoanApplication',

    'columns' => [
        'id',
        'loan_number',
        'pers',
        'status',
        'checked_at',
        'child' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $firstName = $value->first_name;
                    $lastName = $value->last_name;
                    return "<a href=\"/admin/users/$id\">$firstName $lastName</a>";
                }
            }
        ],
        'title'
    ],

    'filters' => [
        'id',
        'loan_number'
//        'child_id'
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'loan_number' => [
            'editable' => false
        ],
        'loan_id' => [
            'editable' => false
        ],
        'pers',
        'status',
        'checked_at' => [
            'type' => 'datetime'
        ],
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];