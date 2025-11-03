<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/genders.php');
        foreach ($data as $item) {
            Gender::create($item);
        }
    }
}
