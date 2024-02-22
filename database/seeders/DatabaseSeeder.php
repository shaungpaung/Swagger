<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Township;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Township::create([
            'name' => 'Yangon',
        ]);
        Branch::create([
            'name' => 'North Dagon',
            'township_id' => '1'
        ]);
        User::create([
            'user_name' => 'admin',
            'branch_id' => 1,
            'password' => Hash::make('123456'),
        ]);
    }
}