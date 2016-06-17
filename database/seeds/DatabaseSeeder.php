<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(ChildTableSeeder::class);
        $this->call(PostTableSeeder::class);
        $this->call(LikeTableSeeder::class);
        $this->call(CommentTableSeeder::class);
    }
}
