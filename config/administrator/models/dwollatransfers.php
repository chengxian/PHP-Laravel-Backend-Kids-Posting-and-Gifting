<?php

return [
    'title' => 'Dwolla Transfers',
    'single' => 'Dwolla Transfer',
    'model' => 'Kidgifting\DwollaWrapper\Models\DwollaTransfer',

    'columns' => [
        'id',
        'dwolla_id',
        'amount',
        'status',
        'bank_status',
        'customer_bank_status',
        'created_at',
        'fundingContribution' => [
            'output' => function ($value) {
                if ($value) {
                    $id = $value->id;
                    return "<a href=\"/admin/fundingcontributions/$id\">$id</a>";
                }
            }
        ],
    ],

    'filters' => [
        'id',
        'dwolla_id',
        'amount',
        'status',
        'bank_status',
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'dwolla_id' => [
            'title' => 'Dwolla ID',
            'type' => 'text'
        ],
        'status',
        'bank_status',
        'customer_bank_status',
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ],
        'facilitator_fee_amount',
        'facilitator_fee_transfer_id',
        'charity_amount',
        'charity_amount_transfer_id',
        'amount'
    ]
];