<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Create 6 dummy users
        for ($i = 0; $i < 6; $i++) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->userName . '@students.apiit.lk', // Email with the required domain
                'email_verified_at' => now(),
                'password' => bcrypt('password123'), // You can change this to any default password
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
