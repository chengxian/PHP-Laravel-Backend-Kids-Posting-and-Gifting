<?php

use App\Child;
use App\Comment;
use App\Post;
use App\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emails = [
            "kidgiftingtim@mailinator.com",
            "kidgiftinglaura@mailinator.com",
            "kidgiftingcindy@mailinator.com"
        ];

        $child = Child::where('first_name', 'TestCharlie')->firstOrFail();
        $users = User::whereIn('email', $emails)->get();
        $posts = Post::where('child_id', $child->id)->get();
        $faker = Factory::create();

        $i = 0;
        $j = 0;

        foreach ($posts as $post) {
            Comment::where('post_id', $post->id)->delete();
            $i=0;

            foreach($users as $user) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'comment' => $faker->text
                ]);

                if ($i == $j) {
                    $j++;
                    break;
                }

                $i++;
            }
        }
    }
}
