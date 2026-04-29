<?php

namespace Database\Seeders;

use App\Enums\ProductTypes;
use App\Models\Account;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Factory::create('ru_RU');

        $data = [];

        $accounts = Account::factory()
            ->withTransactions()
            ->count(5)
            ->create();

        foreach ($accounts as $account) {
            $data[] = [
                'account_id' => $account->id,
                'title' => $faker->randomElement(['Зарплатный', 'Классический', 'Premium', 'Gold']),
                'type' => $faker->randomElement(ProductTypes::values()),
                'rate' => $faker->randomDigit(),
            ];
        }

        DB::table('products')->insert($data);
    }
}
