<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Department;


class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/departments.php');
        foreach ($data as $item) {
            Department::create($item);
        }
    }
}
