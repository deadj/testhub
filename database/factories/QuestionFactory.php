<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Question;
use Faker\Generator as Faker;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'id' => 1,
        'testId' => 1,
        'questions' => '???',
        'balls' => 10,
        'type' => 'textAnswer',
        'answer' => json_encode('null'),
        'trueAnswer' => json_encode('done'),
        'number' => 1
    ];
});
