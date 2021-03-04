<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Result;
use Faker\Generator as Faker;

$factory->define(Result::class, function (Faker $faker) {
    return [
        'testId' => 1,
        'userId' => 1,
        'balls' => 10
    ];
});
