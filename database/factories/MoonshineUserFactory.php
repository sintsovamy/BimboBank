<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\MoonshineUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Account>
 */
class MoonshineUserFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = MoonshineUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();

        return [
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
