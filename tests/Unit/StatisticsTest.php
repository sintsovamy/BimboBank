<?php

namespace Tests\Unit;

use App\Services\StatisticsService;
use Database\Factories\AccountFactory;
use Database\Factories\MoonshineUserFactory;
use Database\Factories\ProfileFactory;
use Database\Factories\TransactionFactory;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_sum_of_sent_received_transactions_all_products(): void
    {
        $user = MoonshineUserFactory::new()
            ->has(ProfileFactory::new())
            ->has(
                AccountFactory::new()
            )
            ->create();

        $account = $user->accounts->first();

        $factory = TransactionFactory::new();

        $factory
            ->create([
                'source_account_id' => $account->id,
                'amount' => 100,
                'created_at' => now()->addDays(-1)
            ]);

        $factory
            ->create([
                'source_account_id' => $account->id,
                'amount' => 200,
                'created_at' => now()->addDays(-1)
            ]);

        $factory
            ->create([
                'source_account_id' => $account->id,
                'amount' => 300,
                'created_at' => now()->addDays(-1)
            ]);

        $factory
            ->create([
                'destination_account_id' => $account->id,
                'amount' => 100000,
                'created_at' => now()->addDays(-1)
            ]);

        $factory
            ->create([
                'destination_account_id' => $account->id,
                'amount' => 333,
                'created_at' => now()->addDays(-1)
            ]);

        $this->actingAs($user);

        $service = app(StatisticsService::class);

        $response = $service->getStats(['date_from' => now()->startOfMonth(), 'date_to' => now()]);

        $lineStat = $response['lineStat'];

        $this->assertEquals(100333, array_sum($lineStat['receivedStat']));
        $this->assertEquals(600, array_sum($lineStat['sentStat']));
    }
}
