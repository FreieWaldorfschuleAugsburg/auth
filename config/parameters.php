<?php

return [
    'authorization_request' => [
        'client_id',
        'redirect_uri',
        'response_type',
        'state'
    ],
    'token_request' => [
        'grant_type',
        'client_id',
        'client_secret',
        'redirect_uri',
        'code'
    ]
];
