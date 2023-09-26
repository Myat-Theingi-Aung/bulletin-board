<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'profile' => 'default.jpg',
            'type' => '0',
            'phone' => '09987654321',
            'address' => 'yangon',
            'dob' => Carbon::now(),
            'created_user_id' => 1,
            'updated_user_id' => 1
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'profile' => 'default.jpg',
            'type' => '0',
            'phone' => '09987987987',
            'address' => 'yangon',
            'dob' => Carbon::now(),
            'created_user_id' => 1,
            'updated_user_id' => 1
        ]);
    }
}
