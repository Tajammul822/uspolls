<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $races = [
            ['name' => 'primary', 'status' => 1],
            ['name' => 'general', 'status' => 1],
            ['name' => 'midterm', 'status' => 1],
            ['name' => 'approval', 'status' => 1],
            
        ];

        DB::table('races')->insert($races);
    }
}
