<?php

return [
    'environment' => env('PAYONE_ENVIRONMENT','sandbox'),
    
    'channel' => 'payone',    
    
    'sandbox' => [
        'url'          => env('PAYONE_SANDBOX_URL',''),
        'aid'          => env('PAYONE_SANDBOX_AID',''),
        'mid'          => env('PAYONE_SANDBOX_MID',''),
        'portalid'     => env('PAYONE_SANDBOX_PORTALID',''),
        'key'          => env('PAYONE_SANDBOX_KEY',''),
        'mode'         => env('PAYONE_SANDBOX_MODE','test'),
        'api_version'  => env('PAYONE_SANDBOX_API_VERSION',''),
        'encoding'     => env('PAYONE_SANDBOX_ENCODING',''),
        'successurl'   => env('PAYONE_SANDBOX_SUCCESSURL',''),
        'errorurl'     => env('PAYONE_SANDBOX_ERRORURL',''),
        'backurl'      => env('PAYONE_SANDBOX_BACKURL',''),
        'request'      => env('PAYONE_SANDBOX_REQUEST','authorization') ,
        'clearingtype' => env('PAYONE_SANDBOX_CLEARINGTYPE','sb'),
        'sofort'         => [
            'onlinebanktransfertype' => 'PNT'
        ]
    ],

    'production' => [
        'url'          => env('PAYONE_PRODUCTION_URL',''),
        'aid'          => env('PAYONE_PRODUCTION_AID',''),
        'mid'          => env('PAYONE_PRODUCTION_MID',''),
        'portalid'     => env('PAYONE_PRODUCTION_PORTALID',''),
        'key'          => env('PAYONE_PRODUCTION_KEY',''),
        'mode'         => env('PAYONE_PRODUCTION_MODE','live'),
        'api_version'  => env('PAYONE_PRODUCTION_API_VERSION',''),
        'encoding'     => env('PAYONE_PRODUCTION_ENCODING',''),
        'successurl'   => env('PAYONE_PRODUCTION_SUCCESSURL',''),
        'errorurl'     => env('PAYONE_PRODUCTION_ERRORURL',''),
        'backurl'      => env('PAYONE_PRODUCTION_BACKURL',''),
        'request'      => env('PAYONE_PRODUCTION_REQUEST','authorization') ,
        'clearingtype' => env('PAYONE_PRODUCTION_CLEARINGTYPE','sb'),
        'sofort'         => [
            'onlinebanktransfertype' => 'PNT'
        ]
    ]
];
