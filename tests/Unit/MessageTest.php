<?php

namespace Tests\Unit;

use Tests\TestCase;

class MessageTest extends TestCase
{
    protected $message;

    protected function setUp()
    {
        parent::setUp();

        $this->message = create('App\Message');
    }

    /** @test */
    public function a_message_has_from_user()
    {
        $this->assertInstanceOf('App\User', $this->message->fromUser);
    }

    /** @test */
    public function a_message_has_to_user()
    {
        $this->assertInstanceOf('App\User', $this->message->toUser);
    }
}
