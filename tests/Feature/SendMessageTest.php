<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Events\ChatSent;

class SendMessageTest extends TestCase
{
    /** @test */
    public function unauthenticated_user_cannot_send_message()
    {
        $this->withExceptionHandling();

        $toUser = create('App\User');

        $this->post(route('api.users.chat-user', $toUser))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_cannnot_send_message_to_himself()
    {
        $this->signIn();

        $toUser = auth()->user();

        $this->post(route('api.users.chat-user', $toUser),
                [
                    'text' => 'This is a sample message.'
                ]
            )
            ->assertJson(['status' => false]);
    }

    /** @test */
    public function a_message_requires_a_text()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $toUser = create('App\User');

        $this->post(route('api.users.chat-user', $toUser))
            ->assertSessionHasErrors('text');
    }

    /** @test */
    public function authenticated_user_can_send_message()
    {
        $this->expectsEvents(ChatSent::class);

        $this->signIn();

        $toUser = create('App\User');
        $text = 'This is a sample message.';

        $this->post(route('api.users.chat-user', $toUser), ['text' => $text]);

        $this->assertDatabaseHas('messages', ['text' => $text]);
    }
}
