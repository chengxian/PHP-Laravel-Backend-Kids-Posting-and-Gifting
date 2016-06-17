<?php

return [
    'title' => 'Users',
    'single' => 'user',
    'model' => 'App\User',

    'columns' => [
        'id',
        'first_name' => [
            'title' => 'First Name'
        ],
        'last_name' => [
            'title' => 'Last Name'
        ],
        'email',
//        'fundingAccounts' => [
//            'title' => 'Funding Account',
//            'output' => function($value)
//            {
//                dd($value);
//                return $value[0];//->first()->id;
//            },
//            'relationship' => 'fundingAccounts',
//            'select' => "(:table).id"
//        ],
        'fundable' => [
            'output' => function ($value) {
                if ($value && count($value) > 0) {
                    $id = $value[0]->id;
                    return "<a href=\"/admin/dwollasourceaccounts/$id\">1</a>";
                }
            }
        ],
        'setting' => [
            'output' => function ($value) {
                if ($value) {
                    return "<a href=\"/admin/settingz/$value->id\">1</a>";
                }

            }
        ]
    ],

    'edit_fields' => [
        'id' => [
            'type' => 'key'
        ],
        'facebook_id',
        'twitter_id',
        'instagram_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'street',
        'street1',
        'city',
        'state',
        'country',
        'postcode',
        'dob' => [
            'type' => 'date'
        ],
        'is_parent' => [
            'type' => 'bool'
        ],
        'is_admin' => [
            'type' => 'bool'
        ],
        'email_verified' => [
            'type' => 'bool'
        ],
        'accepted_kf_toc' => [
            'type' => 'bool'
        ],
        'accepted_kf_toc_at' => [
            'type' => 'datetime'
        ],
        'full_user' => [
            'type' => 'bool'
        ],
        'status',
        'emailage_validated' => [
            'type' => 'bool'
        ],
        'emailage_score',
        'emailage_band',
        'dwollaCustomer' => [
            'type' => 'relationship',
            'name_field' => 'id'
        ],
        'created_at' => [
            'editable' => false
        ],
        'updated_at' => [
            'editable' => false
        ]
    ]
];