<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    const TYPE_ONE_ANSWER 	   = "oneAnswer";
    const TYPE_MULTIPLE_ANSWER = "multipleAnswers";
    const TYPE_NUMBER_ANSWER   = "numberAnswer";
    const TYPE_TEXT_ANSWER 	   = "textAnswer";
}
