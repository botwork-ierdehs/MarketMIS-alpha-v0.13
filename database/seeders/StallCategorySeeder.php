<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StallsCategories;
class StallCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/stall-categories.php');
        foreach ($data as $item) {
            StallsCategories::create($item);
        }
    }
}
