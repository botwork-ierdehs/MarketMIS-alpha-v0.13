<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \Database\Factories\UserFactory::seedDefaultUsers();

        $this->call([
            DepartmentSeeder::class,
            RoleSeeder::class,
            GenderSeeder::class,
            UserTypeSeeder::class,
            SubAdminlSeeder::class,
            FeeMasterlistSeeder::class,
            StallCategorySeeder::class,
            StallSeeder::class,
            RequirementChecklistCategorySeeder::class,
            DepartmentPositionSeeder::class,
        ]);
    }
}
