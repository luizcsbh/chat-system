<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
require '../vendor/autoload.php'; 
use Elastic\Elasticsearch\ClientBuilder;

class MessageController extends Controller
{
    
    public $message;
    
    public function __construct(Message $message)
    {
        $this->message = $message;
    }
    
    /**
     * @OA\Get(
     *      path="/api/v1/messages",
     *      operationId="getMessagesList",
     *      tags={"Mensagens"},
     *      summary="Listar todas as mensagens paginadaa",
     *      description="Retorna a lista de todas as mensagenss",
     *      @OA\Response(
     *          response=200,
     *          description="Operação bem-sucedida",
     *          @OA\JsonContent(ref="#/components/schemas/Messages")
     *       ),
     *       security={
     *         {"bearerAuth": {}}
     *       }
     * )
     */
    public function index(Request $request)
    {
        // Defina uma chave única para o cache, por exemplo, "messages_page_1"
        $cacheKey = 'messages_page_' . $request->get('page', 1);

        // Defina o tempo de expiração do cache em minutos (ex: 5 minutos)
        $cacheExpiration = 5;

        // Utilize o cache para armazenar e recuperar as mensagens
        $messages = Cache::remember($cacheKey, $cacheExpiration, function () {
            // A consulta ao banco é realizada apenas quando o cache está vazio ou expira
            return $this->message->with('user')->latest()->paginate(20);
        });

        // Retorne as mensagens do cache ou do banco de dados
        return response()->json([
            'sucess' => true,
            'data' => $messages
        ],Response::HTTP_OK);
    }


    /* 
    public function index()
    {
        $messages = $this->message->with('user')->latest()->paginate(20); // Lista as últimas 20 mensagens
        
        return response()->json([
            'sucess' => true,
            'data' => $messages
        ],Response::HTTP_OK);
    }
    */

    /**
     * @OA\Post(
     *      path="/api/v1/messages",
     *      operationId="storeMessages",
     *      tags={"Mensagens"},
     *      summary="Criar uma nova mensagem",
     *      description="Cria uma nova mensagem com os dados fornecidos",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/Messages")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Mensagem criada com sucesso",
     *          @OA\JsonContent(ref="#/components/schemas/Messages")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Dados inválidos fornecidos"
     *      ),
     *      security={
     *         {"bearerAuth": {}}
     *      }
     * )
     */
    public function store(Request $request)
    {
        $request->validate($this->message->rules(), $this->message->feedback());

        // Cria uma nova mensagem associada ao usuário autenticado
        $message = $this->message::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Emite o evento de mensagem enviada
        broadcast(new MessageSent($message->load('user')))->toOthers();

        return response()->json([
            'success' => true,
            'data' => $message
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/messages/search",
     *      operationId="searchMessages",
     *      tags={"Mensagens"},
     *      summary="Buscar mensagens com Elasticsearch",
     *      description="Realiza uma busca avançada nas mensagens usando Elasticsearch",
     *      @OA\Parameter(
     *          name="query",
     *          description="Texto de busca nas mensagens",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Operação bem-sucedida",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Messages")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Parâmetros inválidos",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Parâmetros de busca inválidos."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erro no servidor",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Erro ao realizar a busca."
     *              )
     *          )
     *      ),
     *      security={
     *         {"bearerAuth": {}}
     *      }
     * )
     */
    public function search(Request $request)
    {
        // Conecta ao Elasticsearch
        $client = ClientBuilder::create()->setHosts(config('services.elastic.hosts'))->build();

        // Recebe a query do usuário
        $query = $request->input('query');

        // Realiza a busca no Elasticsearch
        $params = [
            'index' => 'messages',
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['content'],
                    ],
                ],
            ],
        ];

        $results = $client->search($params);

        // Extrai as mensagens dos resultados
        $messages = collect($results['hits']['hits'])->map(function ($hit) {
            return $hit['_source'];
        });

        return response()->json($messages);
    }
}