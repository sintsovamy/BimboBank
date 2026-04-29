<?php

namespace Database\Factories;

use App\Models\MCC;
use App\Models\Transaction;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'currency_id' => 1,
            'status' => 'completed',
            'created_at' => now(),
            'completed_at' => now(),
        ];
    }

    /**
     * @param int $accountId
     * @return $this
     */
    public function withSourceAccount(int $accountId): static
    {
        return $this->state(fn (array $attributes) => [
            'source_account_id' => $accountId,
        ]);
    }

    /**
     * @param int $accountId
     * @return $this
     */
    public function withDestinationAccount(int $accountId): static
    {
        return $this->state(fn (array $attributes) => [
            'destination_account_id' => $accountId,
        ]);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * @return $this
     */
    public function externalTransfer(string $transactionType): static
    {
        $faker = Faker::create('ru_RU');

        return $this->state(function (array $attributes) use ($transactionType, $faker) {

            return [
                'transaction_type' => $transactionType,
                'destination_account_id' => null,
                'external_destination_bank' => $faker->bank(),
            ];
        });
    }

    /**
     * @return $this
     */
    public function internalTransfer(string $transactionType): static
    {
        return $this->state(function (array $attributes) use ($transactionType) {
            return [
                'transaction_type' => $transactionType,
                'destination_account_id' => null,
                'external_destination_value' => null,
            ];
        });
    }

    public function purchase(): static
    {
        return $this->state(function (array $attributes) {
            $merchantName = $this->faker->company();
            $mcc_id = MCC::query()->inRandomOrder()->first()->id;

            return [
                'transaction_type' => 'purchase',
                'merchant_name' => $merchantName,
                'mcc_id' => $mcc_id,
                'destination_account_id' => null,
                'source_account_id' => null,
                'external_destination_value' => null,
                'external_destination_bank' => null,
            ];
        });
    }
}
