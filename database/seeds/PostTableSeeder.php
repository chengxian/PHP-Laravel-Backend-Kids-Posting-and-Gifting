<?php

use App\Child;
use App\Post;
use App\User;
use App\Media;
use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $child = Child::where('first_name', 'TestCharlie')->firstOrFail();
        $user = User::where('email', 'kidgiftingtim@mailinator.com')->firstOrFail();

        Post::where('child_id', $child->id)->delete();

        $textPosts = [
            [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'title' => 'My first post!!',
                'text' => 'Hello World! Feb, 24 (Wednesday) 2016-02-24 02:48 PM'
            ],
            [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'title' => 'My second post!!',
                'text' => 'Hello World! Feb, 24 (Wednesday) 2016-02-24 02:49 PM'
            ],
            [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'title' => 'My third post!!',
                'text' => 'Hello World! Feb, 24 (Wednesday) 2016-02-24 02:49 PM'
            ]
        ];

        $photoPosts = [
            [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'title' => 'My first photo post'
            ],
            [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'title' => 'My second photo post'
            ]
        ];

        $images = [
            [
                'filename' => '1456068921.jpg',
                'url' => 'http://ec2-52-53-212-137.us-west-1.compute.amazonaws.com/uploads/images/1456068921.jpg'
            ],
            [
                'filename' => '1456095389.jpg',
                'url' => 'http://ec2-52-53-212-137.us-west-1.compute.amazonaws.com/uploads/images/1456095389.jpg'
            ]
        ];


        foreach ($textPosts as $post) {
            Post::create($post);
        }

        foreach ($images as $i => $image) {
            $query = Media::where('filename', $image['filename']);

            if ($query->count() <= 0) {
                $media = Media::create([
                    'url' => 'http://ec2-52-53-212-137.us-west-1.compute.amazonaws.com/uploads/images/1456095389.jpg',
                    'filename' => '1456095389.jpg',
                    'mime_type' => 'image/jpeg',

                ]);
            } else {
                $media = $query->first();
            }

            $post = Post::create($photoPosts[$i])->first();

            $postAttachement = \App\PostAttachment::create([
                'post_id' => $post->id,
                'attachment_id' => $media->id
            ]);
        }
    }
}
