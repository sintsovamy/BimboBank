<?php

namespace Database\Factories;

use App\Enums\AccountTypes;
use App\Enums\TransactionTypes;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'currency_id' => 1,
            'account_number' => $this->faker->numerify('####################'),
            'type' => $this->faker->randomElement(AccountTypes::values()),
            'balance' => $this->faker->randomFloat(2, 1, 1000000),
            'status' => 'active',
            'opened_at' => $this->faker->dateTimeBetween('-7 week', now()),
        ];
    }

    /**
     * @return $this
     */
    public function withTransactions(): static
    {
        return $this->afterCreating(function (Account $account) {
            for ($i = 0; $i < 5; $i++) {
                $this->createTransactionForAccount($account);
            }
        });
    }

    /**
     * @param Account $account
     * @return void
     */
    private function createTransactionForAccount(Account $account): void
    {
        $type = $this->faker->numberBetween(0, 2);

        switch($type) {
            case 0:
                $this->createOutTransaction($account);
                break;
            case 1:
                $this->createInTransaction($account);
                break;
            case 2:
                $this->createPurchase($account);
                break;
        }
    }

    private function createPurchase(Account $account): void
    {
        $type = 'purchase';

        $amount = $this->faker->randomFloat(2,100, 50000);

        Transaction::factory()
            ->purchase()
            ->withAmount($amount)
            ->withSourceAccount($account->id)
            ->create();
    }

    /**
     * @param Account $account
     * @return void
     */
    private function createOutTransaction(Account $account): void
    {
        $type = $this->faker->randomElement(['internal', 'external']);

        $amount = $this->faker->randomFloat(2, 100, 50000);

        switch ($type) {
            case 'internal':
                $destinationAccount = Account::query()
                    ->where('id', '!=', $account->id)
                    ->inRandomOrder()
                    ->first();

                Transaction::factory()
                    ->internalTransfer(TransactionTypes::OUTGOING->value)
                    ->withSourceAccount($account->id)
                    ->withDestinationAccount($destinationAccount->id)
                    ->withAmount($amount)
                    ->create();
                break;

            case 'external':
                Transaction::factory()
                    ->externalTransfer(TransactionTypes::OUTGOING->value)
                    ->withSourceAccount($account->id)
                    ->withAmount($amount)
                    ->create();
                break;
        }
    }

    /**
     * @param Account $account
     * @return void
     */
    private function createInTransaction(Account $account): void
    {
        $type = $this->faker->randomElement(['internal', 'deposit', 'external']);

        $amount = $this->faker->randomFloat(2, 100, 100000);

        switch ($type) {
            case 'internal':
                $sourceAccount = Account::query()
                    ->where('id', '!=', $account->id)
                    ->inRandomOrder()
                    ->first();

                Transaction::factory()
                    ->internalTransfer(TransactionTypes::INCOMING->value)
                    ->withSourceAccount($sourceAccount->id)
                    ->withDestinationAccount($account->id)
                    ->withAmount($amount)
                    ->create();
                break;

            case 'external':
                Transaction::factory()
                    ->externalTransfer(TransactionTypes::INCOMING->value)
                    ->withDestinationAccount($account->id)
                    ->withAmount($amount)
                    ->create();
                break;
        }
    }
}
