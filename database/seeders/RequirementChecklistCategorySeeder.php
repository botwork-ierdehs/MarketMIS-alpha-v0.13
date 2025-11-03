<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\RequirementChecklist;

class RequirementChecklistCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/requirement-checklist.php');
        foreach ($data as $item) {
            RequirementChecklist::create($item);
        }
    }
}
