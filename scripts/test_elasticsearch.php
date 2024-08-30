<?php

// Ajuste o caminho conforme necessário. Se o script está na pasta 'scripts' e 'vendor' está na raiz do projeto:
require '../vendor/autoload.php'; 

use Elastic\Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->setHosts(['http://localhost:9200'])->build();

try {
    $response = $client->ping();
    echo $response ? "Connected to Elasticsearch" : "Failed to connect";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}