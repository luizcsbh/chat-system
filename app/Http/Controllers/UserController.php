<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Documentação da API do Sistema de Chat",
 *      description="API para gerenciar usuários e mensagens do sistema de chat.",
 * )
 *  @OA\Tag(
 *     name="Usuários",
 *     description="Gerenciamento de usuários"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Utilize o esquema Bearer token para autenticação. Exemplo: 'Bearer {token}'"
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Servidor da API"
 * )
 */
class UserController extends Controller
{
    
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      operationId="getUsersList",
     *      tags={"Usuários"},
     *      summary="Listar todos os usuários",
     *      description="Retorna a lista de todos os usuários",
     *      @OA\Response(
     *          response=200,
     *          description="Operação bem-sucedida",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *       security={
     *         {"bearerAuth": {}}
     *       }
     * )
     */
    public function index()
    {
        $users = $this->user->all();

        return response()->json([
            'success' => true,
            'data' => $users
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      operationId="storeUser",
     *      tags={"Usuários"},
     *      summary="Criar um novo usuário",
     *      description="Cria um novo usuário com os dados fornecidos",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuário criado com sucesso",
     *          @OA\JsonContent(ref="#/components/schemas/User")
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
        $validated = $request->validate($this->user->rules(), $this->user->feedback());

        $user = $this->user->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso!',
            'data' => $user
        ], Response::HTTP_CREATED);
    }

     /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      operationId="getUserById",
     *      tags={"Usuários"},
     *      summary="Mostrar detalhes de um usuário",
     *      description="Retorna os detalhes de um usuário específico",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID do usuário",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Operação bem-sucedida",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuário não encontrado"
     *      ),
     *      security={
     *         {"bearerAuth": {}}
     *      }
     * )
     */
    public function show($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      operationId="updateUser",
     *      tags={"Usuários"},
     *      summary="Atualizar um usuário existente",
     *      description="Atualiza os dados de um usuário com o ID especificado",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID do usuário",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuário atualizado com sucesso",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuário não encontrado"
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
    public function update(Request $request, $id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate($this->user->rulesForUpdate(), $this->user->feedback());
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $user
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      operationId="deleteUser",
     *      tags={"Usuários"},
     *      summary="Excluir um usuário",
     *      description="Remove um usuário com o ID especificado",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID do usuário",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuário removido com sucesso"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuário não encontrado"
     *      ),
     *      security={
     *         {"bearerAuth": {}}
     *      }
     * )
     */
    public function destroy($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário removido com sucesso!'
        ], Response::HTTP_OK);
    }
    
}
