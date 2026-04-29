<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class MCCSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $csvPath = database_path('data/mcc_codes.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);

        $records = [];
        foreach ($csv->getRecords() as $record) {
            $records[] = [
                'code' => $record['MCC'],
                'title' => $record['Название'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('mcc')->truncate();

        foreach (array_chunk($records, 1000) as $chunk) {
            DB::table('mcc')->insert($chunk);
        }

        $this->command->info('Imported ' . count($records) . ' MCC codes');
    }
}
