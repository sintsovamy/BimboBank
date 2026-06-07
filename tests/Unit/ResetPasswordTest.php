<?php

namespace Tests\Unit;

use Database\Factories\MoonshineUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_reset_password(): void
    {
        $user = MoonshineUserFactory::new()->create();

        $this->actingAs($user);

        $response = $this->post('/bank/profile', [
            'password' => '123456',
            'password_repeat' => '123456'
        ]);

        $response->assertStatus(302);

        $user->fresh();

        $this->assertTrue(Hash::check('123456', $user->password));
    }
}
