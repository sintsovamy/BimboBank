<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends Factory<Account>
 */
class ProfileFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create('ru_RU');

        return [
            'last_name' => $faker->lastName,
            'first_name' => $faker->firstName,
            'patronymic'=> $faker->middleName,
            'gender' => fake()->boolean(),
            'birthday' => $faker->dateTimeBetween('-40 years', '-18 years'),
            'passport_series_number' => $faker->numerify('#### ######'),
            'passport_details' => $faker->sentence(),
            'address' => $faker->address(),
            'phone_number' => $faker->numerify('+7##########'),
            'email' => $faker->email()
        ];
    }
}
