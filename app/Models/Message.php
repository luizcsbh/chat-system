<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;
//use Elasticsearch\ClientBuilder;

/**
 * @OA\Schema(
 *     schema="Messages",
 *     type="object",
 *     title="Mensagens",
 *     required={"user_id", "content"},
 *     properties={
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="ID da mensagem"
 *         ),
 *         @OA\Property(
 *             property="user_id",
 *             type="integer",
 *             description="ID do usuário"
 *         ),
 *         @OA\Property(
 *             property="content",
 *             type="string",
 *             format="string",
 *             description="Conteúdo da mensagem"
 *         ),
 *         @OA\Property(
 *             property="created_at",
 *             type="string",
 *             format="date-time",
 *             description="Data de criação"
 *         ),
 *         @OA\Property(
 *             property="updated_at",
 *             type="string",
 *             format="date-time",
 *             description="Data de atualização"
 *         )
 *     }
 * )
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
    ];

    public function rules()
    {
        return [
            'content'      => 'required|string|max:255',
        ];
    }

    public function rulesForUpdate($id)
    {
        return [
            'content'      => 'sometimes|string|max:255',
        ];
    }

    public function feedback()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string'   => 'O nome deve ser um texto.',
            'name.max'      => 'O nome não pode ter mais de 255 caracteres.'
        ];
    }

    public function save(array $options = [])
    {
        parent::save($options);

        // Indexa a mensagem no Elasticsearch
        $client = ClientBuilder::create()->setHosts(config('services.elastic.hosts'))->build();
        $client->index([
            'index' => 'messages',
            'id'    => $this->id,
            'body'  => [
                'id'       => $this->id,
                'content'  => $this->content,
                'user_id'  => $this->user_id,
                'created_at' => $this->created_at->format('c'),
            ],
        ]);
    }

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}