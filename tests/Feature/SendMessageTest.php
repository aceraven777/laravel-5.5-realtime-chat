<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendMessageTest extends TestCase
{
    /** @test */
    public function unauthenticated_user_cannot_send_message()
    {
        $this->withExceptionHandling();

        $toUser = create('App\User');

        $this->post("api/users/{$toUser->id}/messages")
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_cannnot_send_message_to_himself()
    {
        $this->signIn();

        $toUser = auth()->user();

        $this->post("api/users/{$toUser->id}/messages", ['text' => 'This is a sample message.'])
            ->assertJson(['status' => false]);
    }

    /** @test */
    public function a_message_requires_a_text()
    {
        $this->withExceptionHandling();
        $this->signIn();

        $toUser = create('App\User');

        $this->post("api/users/{$toUser->id}/messages")
            ->assertSessionHasErrors('text');
    }
}