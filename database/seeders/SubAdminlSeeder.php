<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubAdminType as SubAdminType;

class SubAdminlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/sub-admin-type.php');
        foreach ($data as $item) {
            SubAdminType::create($item);
        }
    }
}
