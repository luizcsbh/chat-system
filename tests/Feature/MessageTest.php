<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_message()
    {
        // Fake o evento para não executar de verdade durante o teste
        Event::fake([MessageSent::class]);

        // Crie um usuário para autenticação
        $user = User::factory()->create();

        // Faça a requisição para criar uma mensagem como o usuário autenticado
        $response = $this->actingAs($user)->postJson('/api/v1/messages', [
            'content' => 'Hello, this is a test message!',
        ]);

        // Verifique se a resposta é bem-sucedida
        $response->assertStatus(201);

        // Verifique se a mensagem foi salva no banco de dados
        $this->assertDatabaseHas('messages', [
            'content' => 'Hello, this is a test message!',
            'user_id' => $user->id,
        ]);

        // Verifique se o evento foi disparado
        Event::assertDispatched(MessageSent::class);
    }

    /** @test */
    public function a_user_can_fetch_messages()
    {
        // Crie um usuário e mensagens para teste
        $user = User::factory()->create();
        Message::factory()->count(10)->create(['user_id' => $user->id]);

        // Faça a requisição para listar as mensagens como o usuário autenticado
        $response = $this->actingAs($user)->getJson('/api/v1/messages');

        // Verifique se a resposta é bem-sucedida e contém mensagens
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
    }
}