<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(StateSeeder::class);
        $this->call(CandidateSeeder::class);
        $this->call(PollsterSeeder::class);
        $this->call(UserSeeder::class);
        // User::factory(1)->create();
        // User::factory()->create([
        //     'name'     => 'Meyer Waldman',
        //     'email'    => 'MeyerWaldman@gmail.com',
        //     'password' => Hash::make('123456789'),
        //     'role'     => 1,
        //     'status'   => 1,
        // ]);
    }
}
