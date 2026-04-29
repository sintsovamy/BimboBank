<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\MoonshineUser;
use App\Models\Product;
use Database\Factories\AccountFactory;
use Database\Factories\MoonshineUserFactory;
use Database\Factories\ProductFactory;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = MoonshineUser::query()
            ->create([
                'email' => 'sintsovamy',
                'password' => Hash::make('password'),
                'name' => 'sintsovamy'
            ]);

        $user->profile()->create([
            'last_name' => 'Синцова',
            'first_name' => 'Марина',
            'patronymic' => 'Юрьевна',
            'gender' => false,
            'birthday' => '1998-12-18',
            'passport_series_number' => '0000 000000',
            'passport_details' => 'dfsgergre',
            'address' => 'Томск',
            'phone_number' => '+79999999999',
            'email' => 'mail@mail.com'
        ]);

        AccountFactory::new()
            ->withTransactions()
            ->count(3)
            ->for($user, 'user')
            ->has(ProductFactory::new())
            ->create();

        MoonshineUserFactory::new()
            ->count(10)
            ->has(ProfileFactory::new())
            ->has(
                AccountFactory::new()
                    ->withTransactions()
                    ->count(3)
                    ->has(ProductFactory::new())
            )
            ->create();
    }
}
