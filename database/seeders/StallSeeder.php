<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stall;

class StallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/stalls.php');
        foreach ($data as $item) {
            Stall::create($item);
        }

    }
}
