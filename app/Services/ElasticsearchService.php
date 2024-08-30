<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $config = config('elasticsearch');

        $this->client = ClientBuilder::create()
            ->setHosts($config['hosts'])
            ->setRetries($config['retries'])
            ->setSSLVerification($config['ssl']['verify'])
            ->setSSLKey($config['ssl']['key'])
            ->setSSLCert($config['ssl']['cert'])
            ->setConnectionParams([
                'timeout' => $config['connection']['timeout'],
            ])
            ->build();
    }

    public function getClient()
    {
        return $this->client;
    }
}