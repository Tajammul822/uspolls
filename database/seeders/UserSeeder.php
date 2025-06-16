<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $users = [
            ['name' => 'Meyer Waldman', 'email' => 'MeyerWaldman@gmail.com', 'role' => 1, 'password' => Hash::make('123456789'), 'status' => 1],
        ];

        DB::table('users')->insert($users);
    }
}
