<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReadMessagesTest extends TestCase
{
    /** @test */
    public function unauthenticated_user_cannot_read_messages()
    {
        $this->withExceptionHandling();

        $user = create('App\User');

        $this->get("api/messages/{$user->id}")
            ->assertRedirect('/login');
    }
}
