<?php

namespace Database\Factories;

use App\Enums\ProductTypes;
use App\Models\Account;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends Factory<Account>
 */
class ProductFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create('ru_RU');

        return [
            'title' => $faker->randomElement(['Зарплатный', 'Классический', 'Premium', 'Gold']),
            'type' => $faker->randomElement(ProductTypes::values()),
            'rate' => $faker->randomDigit(),
        ];
    }
}
