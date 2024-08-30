<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class SetupElasticsearch extends Command
{
    protected $signature = 'elastic:setup';
    protected $description = 'Set up the Elasticsearch index for messages';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Configura o cliente do Elasticsearch
        $client = ClientBuilder::create()
            ->setHosts(config('services.elastic.hosts'))
            ->build();

        // Define o índice e mapeamento
        $params = [
            'index' => 'messages',
            'body'  => [
                'mappings' => [
                    'properties' => [
                        'id'       => ['type' => 'integer'],
                        'content'  => ['type' => 'text'],
                        'user_id'  => ['type' => 'integer'],
                        'created_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ];

        // Cria o índice
        if (!$client->indices()->exists(['index' => 'messages'])) {
            $client->indices()->create($params);
            $this->info('Elasticsearch index "messages" created successfully.');
        } else {
            $this->info('Elasticsearch index "messages" already exists.');
        }
    }
}