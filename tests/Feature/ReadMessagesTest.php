<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReadMessagesTest extends TestCase
{
    /** @test */
    public function unauthenticated_user_cannot_view_user_chat_page()
    {
        $this->withExceptionHandling();

        $user = create('App\User');

        $this->get(route('users.chat-messages', ['to_user' => $user->id]))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_cannot_view_his_own_chat_page()
    {
        $user = create('App\User');
        $this->signIn($user);

        $this->get(route('users.chat-messages', ['to_user' => $user->id]))
            ->assertRedirect('/');
    }

    /** @test */
    public function authenticated_user_can_view_chat_page()
    {
        $this->signIn();

        $user = create('App\User');

        $this->get(route('users.chat-messages', ['to_user' => $user->id]))
            ->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_read_messages()
    {
        $this->withExceptionHandling();

        $user = create('App\User');

        $this->get(route('api.users.chat-messages', ['to_user' => $user->id]))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_cannot_read_his_own_messages()
    {
        $user = create('App\User');
        $this->signIn($user);

        $this->get(route('api.users.chat-messages', ['to_user' => $user->id]))
            ->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_cannot_read_messages()
    {
        $fromUser = create('App\User');
        $toUser = create('App\User');
        
        $this->signIn($fromUser);

        create('App\Message',
            [
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
            ],
            5);
        
        create('App\Message',
            [
                'from_user_id' => $toUser->id,
                'to_user_id' => $fromUser->id,
            ],
            5);

        $messages = $this->getJson(route('api.users.chat-messages', ['to_user' => $toUser->id]))->json();
        $this->assertCount(10, $messages);
    }
}
