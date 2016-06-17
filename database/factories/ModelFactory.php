<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\User;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'street' => $faker->streetAddress,
        'street1' => '',
        'city' => $faker->city,
        'state' => 'NY',
        'postcode' => '10010',
        'phone' => '6464707019',
        'ssn' => rand(100000000,999999999),
        'ip' => $faker->ipv4,
        'dob' => $faker->date('Y-m-d')
    ];
});

$factory->define(App\Media::class, function (Faker\Generator $faker) {

    return [
        'url' => $faker->imageUrl(640, 480, 'people'),
        //'filename' => $faker->image("", 640, 480, 'people'),
        'mime_type' => 'image/jpeg'
    ];
});

$factory->define(App\Child::class, function (Faker\Generator $faker) {
    return [
        'parent_id' => User::orderByRaw("RAND()")->first()->id,
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'birthday' => $faker->date('Y-m-d'),
        'wants' => 'developer',
        'avatar_id' => factory(App\Media::class, 1)->create()->first()->id
    ];
});
