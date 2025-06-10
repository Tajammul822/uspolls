<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = [
            ['name' => 'John Doe', 'party' => 'Democratic Party'],
            ['name' => 'Jane Smith', 'party' => 'Republican Party'],
            ['name' => 'Alice Johnson', 'party' => 'Republican Party'],
            ['name' => 'Bob Williams', 'party' => 'Green Party'],
            ['name' => 'Charlie Brown', 'party' => 'Libertarian Party'],
            ['name' => 'Dana White', 'party' => 'Green Party'],
            ['name' => 'Ethan Hunt', 'party' => 'Independent'],
            ['name' => 'Fiona Carter', 'party' => 'Socialist Party'],
            ['name' => 'George Clark', 'party' => 'Independent'],
            ['name' => 'Hannah Lee', 'party' => 'Conservative Party'],
        ];
        DB::table('candidates')->insert($candidates);
    }
}
