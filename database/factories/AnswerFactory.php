<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Answer;
use Faker\Generator as Faker;

$factory->define(Answer::class, function (Faker $faker) {
    return [
        'userId' => 1,
        'questionId' => 1,
        'testId' => 1,
        'value' => json_encode(1),
        'done' => 1,
    ];
});
