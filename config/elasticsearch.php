<?php

return [

    'hosts' => [
        env('ELASTICSEARCH_HOST', 'localhost:9200'),
    ],

    'retries' => env('ELASTICSEARCH_RETRIES', 3),

    'ssl' => [
        'verify' => env('ELASTICSEARCH_SSL_VERIFY', true),
        'cert' => env('ELASTICSEARCH_SSL_CERT', null),
        'key' => env('ELASTICSEARCH_SSL_KEY', null),
    ],

    'connection' => [
        'timeout' => env('ELASTICSEARCH_TIMEOUT', 10),
    ],

];