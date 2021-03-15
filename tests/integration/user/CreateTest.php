<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testValidUser()
    {
        $customer = User::factory()->make();

        $this
            ->post('user', $customer->toArray())
            ->seeInDatabase('users', [
                'name' => $customer->name,
                'email' => $customer->email,
                'document' => $customer->document
            ])
            ->assertResponseOk();
    }
}
