<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeeMasterlist;

class FeeMasterlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = require database_path('seed-data/fee-masterlist.php');
        foreach ($data as $item) {
            FeeMasterlist::create($item);
        }
    }
}
