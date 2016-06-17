<?php

return array(
    'enabled' => env('VAULT_ENABLED', false),
    'addr' => env('VAULT_ADDR', 'http://192.168.20.20:8200'),
    'token' => env('VAULT_TOKEN', null)
);