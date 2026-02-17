<?php

namespace Database\Seeders;

use App\Models\MoonshineUser;
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
                'name' => 'Марина Синцова'
            ]);

        $user->profile()->create([
            'last_name' => 'Синцова',
            'first_name' => 'Марина',
            'middle_name' => 'Юрьевна',
            'gender' => false,
            'birthday' => '1998-12-18',
            'passport_series_number' => '0000 000000',
            'passport_details' => 'dfsgergre',
            'address' => 'Томск',
            'phone_number' => '+79999999999',
            'email' => 'mail@mail.com'
        ]);
    }
}
