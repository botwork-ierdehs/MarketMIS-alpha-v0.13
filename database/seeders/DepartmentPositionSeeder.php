<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DepartmentPosition;

class DepartmentPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/department_position.php');
        foreach ($data as $item) {
            DepartmentPosition::create($item);
        }
    }
}
