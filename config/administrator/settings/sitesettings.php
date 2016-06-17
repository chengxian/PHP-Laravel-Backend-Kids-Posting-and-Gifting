<?php
/**
 * @author: chengxian
 * Date: 5/1/16
 * @copyright Cheng Xian Lim
 */

use Kidgifting\FrozenSettings\FrozenSetting;

return [
    'title' => 'Site Settings',

    'edit_fields' => [
        'kf_loan_approval_required' => [
            'title' => 'KF Manual USA Loan Approval Required',
            'type' => 'bool'
        ],
        'kf_transaction_approval_required' => [
            'title' => 'KF Manual Dwolla Transfer Approval Required',
            'type' => 'bool'
        ],
        'throttle_beta_codes' => [
            'title' => 'Throttle Beta Codes',
            'type' => 'bool'
        ],
        'total_kidgifting_donation_amount' => [
            'title' => 'Total Kidgifting Donation Amount',
            'type' => 'number'
        ]
    ],

    'rules' => [
        'kf_loan_approval_required' => 'boolean',
        'kf_transaction_approval_required' => 'boolean',
        'throttle_beta_codes' => 'boolean',
        'total_kidgifting_donation_amount' => 'numeric'
    ],

    'before_save' => function (&$data) {
        foreach ($data as $key => $value) {
            $setting = FrozenSetting::firstOrNew(['key' => $key]);
            $setting->value = $value;
            $setting->save();
        }
    },

    'permission' => function () {
        return true;//Auth::user()->is_admin;
    }
];