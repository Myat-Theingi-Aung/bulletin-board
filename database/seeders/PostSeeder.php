<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::create([
            'title' => 'Post One',
            'description' => 'Description for post one',
            'status' => 1,
            'created_user_id' => 1,
            'updated_user_id' => 1
        ]);

        Post::create([
            'title' => 'Post Two',
            'description' => 'Description for post two',
            'status' => 0,
            'created_user_id' => 1,
            'updated_user_id' => 1
        ]);
    }
}
