<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReadUsersTest extends TestCase
{
    /** @test */
    public function unauthenticated_user_cannot_view_users_list()
    {
        $this->withExceptionHandling();

        $user = create('App\User');

        $this->get(route('users.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_users_list()
    {
        $this->signIn();

        $user = create('App\User');

        $this->get(route('users.index'))
            ->assertSee(e($user->name));
    }
}
