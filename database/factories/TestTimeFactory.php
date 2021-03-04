<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TestTime;
use Faker\Generator as Faker;

$factory->define(TestTime::class, function (Faker $faker) {
    return [
        'userId' => 1,
        'testId' => 1,
        'created_at' => now(),
    ];
});
