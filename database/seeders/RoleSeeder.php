<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/roles.php');
        foreach ($data as $item) {
            Role::create($item);
        }
    }
}
