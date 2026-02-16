<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['code' => 'RUB', 'numeric_code' => '810', 'title' => 'Рубль'],
            ['code' => 'USD', 'numeric_code' => '840', 'title' => 'Доллар США'],
            ['code' => 'EUR', 'numeric_code' => '978', 'title' => 'Евро']
        ];

        DB::table('currencies')->insert($data);
    }
}
