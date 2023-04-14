<?php

return [
    'base_url' => env('READYCASH_BASE_URL'),
    'wallet_url' => env('READYCASH_BASE_URL') . '/api/provider/wallets',
    'agent_url' => env('READYCASH_BASE_URL') . '/rc/rest',
    'token' => env('READYCASH_TOKEN')
];
