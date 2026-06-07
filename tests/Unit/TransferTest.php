<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Services\TransferService;
use Database\Factories\AccountFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Throwable;

class TransferTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_transfer_between_accounts(): void
    {
        $source = AccountFactory::new()->create([
            'user_id' => 1,
            'balance' => 1000
        ]);

        $receive = AccountFactory::new()->create([
            'user_id' => 2,
            'balance' => 1000
        ]);

        app(TransferService::class)->transfer(
            $source,
            $receive,
            1000
        );

        $this->assertEquals(
            0,
            $source->fresh()->balance
        );

        $this->assertEquals(
            2000,
            $receive->fresh()->balance
        );
    }

    public function test_transfer_between_accounts_fails_when_balance_is_not_enough(): void
    {
        $source = AccountFactory::new()->create([
            'user_id' => 1,
            'balance' => 1000
        ]);

        $receive = AccountFactory::new()->create([
            'user_id' => 2,
            'balance' => 1000
        ]);

        $this->expectException(Throwable::class);

        app(TransferService::class)->transfer(
            $source,
            $receive,
            1500
        );
    }
}
