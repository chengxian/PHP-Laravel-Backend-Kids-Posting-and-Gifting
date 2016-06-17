<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN', 'mg.kidgifting.com'),
        'secret' => env('MAILGUN_SECRET', 'key-d5d9a296996d31f63e4c046a8400ccf2'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => 'AKIAJARM54P7RIAIIQ3Q',
        'secret' => 'AhlhK2syR3GDvXmJTaEGrc4ZL8BTAMNv7qSki9+W1efi',
        'region' => 'us-west-2',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'mailtrap' => [
        'secret' => env('MAILTRAP_KEY', '5548ff7886d815b935f83510c411d1a7'),
        'default_inbox' => env('MAILTRAP_INBOX', '88813')
    ],

];
