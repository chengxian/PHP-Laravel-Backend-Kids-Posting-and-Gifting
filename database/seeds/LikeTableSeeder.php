<?php

use App\Child;
use App\Post;
use App\PostLike;
use App\User;
use Illuminate\Database\Seeder;

class LikeTableSeeder extends Seeder
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

        $i = 0;
        $j = 0;

        foreach ($posts as $post) {
            PostLike::where('post_id', $post->id)->delete();
            $i=0;

            foreach($users as $user) {
                PostLike::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id
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
