<?php

use Illuminate\Database\Seeder;
use App\Media;
use App\Child;
use App\User;

class ChildTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parent = User::where('email', 'kidgiftingtim@mailinator.com')->firstOrFail();

        $query = Media::where('url', 'http://ec2-52-53-212-137.us-west-1.compute.amazonaws.com/uploads/images/1456095389.jpg');

        if ($query->count() <= 0) {
            $media = Media::create([
                'url' => 'http://ec2-52-53-212-137.us-west-1.compute.amazonaws.com/uploads/images/1456095389.jpg',
                'filename' => '1456095389.jpg',
                'mime_type' => 'image/jpeg',

            ]);
        } else {
            $media = $query->first();
        }

        $children = [
            [
                'parent_id' => $parent->id,
                'first_name' => 'TestCharlie',
                'last_name' => 'Broder',
                'birthday' => '2016-07-02',
                'wants' => 'developer',
                'avatar_id' => $media->id
            ],
            [
                'parent_id' => $parent->id,
                'first_name' => 'DarkTestCharlie',
                'last_name' => 'Broder',
                'birthday' => '2016-07-02',
                'wants' => 'developer',
            ]
        ];

        Child::where('first_name', 'like', '%TestCharlie%')->delete();

        foreach ($children as $child) {
            Child::create($child);
        }

        $volumeTesting = env("VOLUME_TESTING", 0);

        if ($volumeTesting > 0) {
            $volumeUsers = factory(App\Child::class, $volumeTesting)->create();
        }
    }
}
