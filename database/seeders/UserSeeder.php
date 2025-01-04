<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one admin user with fake data
        User::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'password' => Hash::make('12345678'),
            'otp' => 'email_verified',
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'is_verified' => true,
        ]);

        User::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'user1@gmail.com',
            'password' => Hash::make('12345678'),
            'avatar' => null,
            'is_verified' => true,
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        User::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'user2@gmail.com',
            'password' => Hash::make('12345678'),
            'avatar' => null,
            'is_verified' => true,
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),

        ]);

        User::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'user3@gmail.com',
            'password' => Hash::make('12345678'),
            'avatar' => null,
            'is_verified' => true,
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),

        ]);

        User::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'user4@gmail.com',
            'password' => Hash::make('12345678'),
            'avatar' => null,
            'is_verified' => true,
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        // Create five normal users with fake data



    }
}

