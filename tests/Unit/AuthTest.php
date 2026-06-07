<?php

namespace Tests\Unit;

use App\Models\MoonshineUser;
use Database\Factories\MoonshineUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_user_can_login(): void
    {
        $user = MoonshineUserFactory::new()->create();

        $response = $this->post('/bank/authenticate', [
            'username' => $user->name,
            'password' => 'password'
        ]);

        $response->assertRedirect('/bank');
        $this->assertAuthenticatedAs($user, 'moonshine');
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $user = MoonshineUserFactory::new()->create();

        $response = $this->post('/bank/authenticate', [
            'username' => $user->email,
            'password' => 'password!!!'
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    public function test_user_cannot_login_with_empty_login()
    {
        $response = $this->post('/bank/authenticate', [
            'username' => '',
            'password' => 'password'
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }
    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('bank/');

        $response->assertRedirect('bank/login');
    }
}
