<?php

return [
    'title' => 'Funding Contributions',
    'single' => 'Funding Contribution',
    'model' => 'App\FundingContribution',

    'columns' => [
        'id',
        'user' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    $email = $value->email;
                    return "<a href=\"/admin/users/$id\">$email</a>";
                }
            }
        ],
        'child' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/children/$id\">$id</a>";
                }
            }
        ],
        'amount',
        'fee_percent',
        'fee_amount',
        'contribution_percent',
        'contribution_amount',
        'status',
        'transfer' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/transfers/$id\">$id</a>";
                }
            }
        ],
        'transfer_type'


    ],

    'filters' => [
        'id',
        'user_id',
        'child_id',
        'transfer_id',
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'user' => [
            'type' => 'relationship',
            'name_field' => 'email'
        ],
        'child' => [
            'type' => 'relationship',
            'name_field' => 'full_name'
        ],
        'amount',
        'fee_percent',
        'fee_amount',
        'contribution_percent',
        'contribution_amount',
        'status',
        'is_gift' => [
            'type' => 'bool'
        ],
        'gift_message',
        'fundable_id',
        'transfers_id',
        'transfers_type',
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];