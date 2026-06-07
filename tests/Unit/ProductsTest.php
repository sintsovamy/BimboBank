<?php

namespace Tests\Unit;


use App\MoonShine\Pages\Dashboard;
use Database\Factories\AccountFactory;
use Database\Factories\MoonshineUserFactory;
use Database\Factories\ProfileFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ReflectionClass;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_user_sees_only_his_products(): void
    {
        $user = MoonshineUserFactory::new()
            ->has(ProfileFactory::new())
            ->has(
                AccountFactory::new()
            )
            ->create();

        $this->actingAs($user);

        $page = app(Dashboard::class);

        $reflection = new ReflectionClass(get_class($page));

        $method = $reflection->getMethod('getAccounts');
        $method->setAccessible(true);

        $accounts = $method->invoke($page);

        $this->assertEquals($user->accounts->count(), $accounts->count());
        $this->assertTrue(
            $accounts->every(fn ($account) => $account->user_id === $user->id)
        );
    }
}
