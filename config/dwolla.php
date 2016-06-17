<?php

return array(
    'subdomain' => env('DWOLLA_SUBDOMAIN', 'api-uat'),
    'key' => env('DWOLLA_KEY', null),
    'secret' => env('DWOLLA_SECRET', null),
    'token-url' => env('DWOLLA_TOKEN_URL', null),
    'token-path' => env('DWOLLA_TOKEN_PATH', null),
    'webhook-url' => env('DWOLLA_WEBHOOK_LISTENER', null),
    'webhook-secret' => env('DWOLLA_WEBHOOK_SECRET', null)
);