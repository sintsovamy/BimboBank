<?php

namespace Tests\Unit;

use App\MoonShine\Pages\Dashboard;
use Database\Factories\AccountFactory;
use Database\Factories\MoonshineUserFactory;
use Database\Factories\ProductFactory;
use Database\Factories\ProfileFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SearchClientTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_user_found_by_phone(): void
    {
        $user = MoonshineUserFactory::new()
            ->has(ProfileFactory::new())
            ->has(
                AccountFactory::new()
                    ->has(ProductFactory::new())
            )
            ->create();

        $page = app(Dashboard::class);

        request()->merge([
            '_data' => [
                'phone' => $user->profile->phone_number
            ]
        ]);

        $response = $page->getRecipientUser();
        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['found']);
        $this->assertEquals('phone', $data['foundBy']);
        $this->assertStringContainsString(
            $user->profile->nameInTransaction,
            $data['htmlData'][0]['html']
        );
    }

    public function test_user_found_by_card(): void
    {
        $user = MoonshineUserFactory::new()
            ->has(ProfileFactory::new())
            ->has(
                AccountFactory::new()
                    ->has(ProductFactory::new())
            )
            ->create();

        $page = app(Dashboard::class);

        request()->merge([
            '_data' => [
                'card_number' => $user->accounts->first()->product->card_number,
            ]
        ]);

        $response = $page->getRecipientUser();
        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['found']);
        $this->assertEquals('card', $data['foundBy']);
        $this->assertStringContainsString(
            $user->profile->nameInTransaction,
            $data['htmlData'][0]['html']
        );
    }

    public function test_user_not_found(): void
    {
        $page = app(Dashboard::class);

        request()->merge([
            '_data' => [
                'card_number' => '0000000000000000',
            ]
        ]);

        $response = $page->getRecipientUser();
        $data = json_decode($response->getContent(), true);

        $this->assertArrayNotHasKey('found', $data);
        $this->assertStringContainsString(
            'Получатель не найден. Проверьте данные.',
            $data['htmlData'][0]['html']
        );
    }
}
