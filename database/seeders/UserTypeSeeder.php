<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;
class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/user-type.php');
        foreach ($data as $item) {
            UserType::create($item);
        }
    }
}
