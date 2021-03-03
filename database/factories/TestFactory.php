<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Test;
use Faker\Generator as Faker;

$factory->define(Test::class, function (Faker $faker) {
    return [
    	'id' => 1,
        'name' => 'test',
       	'minBalls' => 10,
       	'minutesLimit' => 5,
       	'userId' => 0,
       	'showWrongAnswers' => false,
       	'publicResults' => false,
       	'tags' => json_encode(['this', 'is', 'tags', 'test']),
    ];
});
