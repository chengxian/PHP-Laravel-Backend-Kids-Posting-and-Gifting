<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Media;

class UserTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = bcrypt("Testing123!@#");

        $query = Media::where('url', 'http://ec2-52-53-212-137.us-west-1.compute.amazonaws.com/uploads/images/1456161021.jpg');

        if ($query->count() <= 0) {
            $media = Media::create([
                'url' => 'http://ec2-52-53-212-137.us-west-1.compute.amazonaws.com/uploads/images/1456161021.jpg',
                'filename' => '1456161021.jpg',
                'mime_type' => 'image/jpeg',

            ]);
        } else {
            $media = $query->first();
        }

        $users = [
            [
                'email' => 'kidgiftingtim@mailinator.com',
                'first_name' => 'TestTim',
                'last_name' => 'Broder',
                'password' => $password,
                'avatar_id' => $media->id
            ],
            [
                'email' => 'kidgiftinglaura@mailinator.com',
                'first_name' => 'TestLaura',
                'last_name' => 'Bailyn',
                'password' => $password
            ],
            [
                'email' => 'kidgiftingcindy@mailinator.com',
                'first_name' => 'TestCindy',
                'last_name' => 'McLaughlin',
                'password' => $password
            ],
        ];

        $volumeTesting = env("VOLUME_TESTING", 0);

        if ($volumeTesting > 0) {
            $volumeUsers = factory(App\User::class, $volumeTesting)->create();
        }

        $emails = array_reduce($users,
            function($a, $b) {
                $a[] = $b['email'];
                return $a;
            },
            array()
        );

        $existing = User::whereIn('email', $emails)->delete();

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
