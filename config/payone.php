<?php

return [
    'environment' => env('PAYONE_ENVIRONMENT','sandbox'),
    
    'channel' => 'payone',    
    
    'sandbox' => [
        'url'          => 'https://api.pay1.de/post-gateway/',
        'aid'          => '51835',
        'mid'          => '51671',
        'portalid'     => '2037865',
        'key'          => hash("md5", "1h43NsqStYwAA374"),
        'mode'         => 'test',
        'api_version'  => '3.10',
        'encoding'     => 'UTF-8',
        'successurl'   => 'http://venchy.fs.com:4000/paypal-empty?act=success',
        'errorurl'     => 'http://venchy.fs.com:4000/paypal-empty?act=error',
        'backurl'      => 'http://venchy.fs.com',
        'request'      => 'authorization',
        'clearingtype' => 'sb',
        'sofort'         => [
            'onlinebanktransfertype' => 'PNT'
        ]
    ],

    'production' => [
        'url'          => 'https://api.pay1.de/post-gateway/',
        'aid'          => '52064',
        'mid'          => '46847',
        'portalid'     => '2034133',
        'key'          => hash("md5", "N8pnfa6mRLfib7i6"),
        'mode'         => 'test',
        'api_version'  => '3.10',
        'encoding'     => 'UTF-8',
        'successurl'   => 'http://venchy.fs.com:4000/paypal-empty?act=success',
        'errorurl'     => 'http://venchy.fs.com:4000/paypal-empty?act=error',
        'backurl'      => 'http://venchy.fs.com',
        'request'      => 'authorization',
        'clearingtype' => 'sb',
        'sofort'         => [
            'onlinebanktransfertype' => 'PNT'
        ]
    ]
];
