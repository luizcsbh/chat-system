<?php

namespace App\Http\Controllers;

//use Elasticsearch\ClientBuilder;
require  '.././vendor/autoload.php'; 
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class ElasticsearchTestController extends Controller
{
    /**
     * Testa a conexão com o Elasticsearch.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        // Conecta ao Elasticsearch usando o host configurado
        $client = ClientBuilder::create()
            ->setHosts(config('services.elasticsearch.hosts'))
            ->build();

        try {
            // Testa se o Elasticsearch está respondendo
            $response = $client->ping();

            // Retorna a resposta indicando o status da conexão
            return response()->json([
                'message' => $response ? 'Connected to Elasticsearch' : 'Failed to connect',
            ]);
        } catch (\Exception $e) {
            // Retorna o erro caso a conexão falhe
            return response()->json([
                'error' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}