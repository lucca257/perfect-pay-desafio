<?php

return [
    'asaas' => [
        'url' => env('PAGAMENTO_ASAAS_URL', 'https://api-sandbox.asaas.com/'),
        'wallet_id' => env('PAGAMENTO_ASAAS_API_WALLET_ID'),
        'access_token' => env('PAGAMENTO_ASAAS_API_KEY')
    ]
];
