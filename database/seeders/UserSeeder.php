<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Interviewer Users
        User::create([
            'name' => 'John Interviewer',
            'email' => 'interviewer@test.com',
            'password' => Hash::make('password'),
            'role' => 'interviewer',
        ]);

        User::create([
            'name' => 'Sarah Interviewer',
            'email' => 'sarah@test.com',
            'password' => Hash::make('password'),
            'role' => 'interviewer',
        ]);

        // Create Taxpayer Users
        User::create([
            'name' => 'Ahmed Taxpayer',
            'email' => 'ahmed@test.com',
            'password' => Hash::make('password'),
            'role' => 'taxpayer',
        ]);

        User::create([
            'name' => 'Fatima Taxpayer',
            'email' => 'fatima@test.com',
            'password' => Hash::make('password'),
            'role' => 'taxpayer',
        ]);

        User::create([
            'name' => 'Mohammed Taxpayer',
            'email' => 'mohammed@test.com',
            'password' => Hash::make('password'),
            'role' => 'taxpayer',
        ]);

        User::create([
            'name' => 'Aisha Taxpayer',
            'email' => 'aisha@test.com',
            'password' => Hash::make('password'),
            'role' => 'taxpayer',
        ]);

        User::create([
            'name' => 'Hassan Taxpayer',
            'email' => 'hassan@test.com',
            'password' => Hash::make('password'),
            'role' => 'taxpayer',
        ]);
    }
}


