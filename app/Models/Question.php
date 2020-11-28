<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    const TYPE_ONE_ANSWER 	   = "oneAnswer";
    const TYPE_MULTIPLE_ANSWER = "multipleAnswers";
    const TYPE_NUMBER_ANSWER   = "numberAnswer";
    const TYPE_TEXT_ANSWER 	   = "textAnswer";

    public function getTrueAnswer(int $id)
    {
    	$trueAnswer = json_decode($this->find($id)->trueAnswer, true);
    	$type = $this->find($id)->type;
        $answer = NULL;

    	if ($type == Question::TYPE_ONE_ANSWER) {
    		$answer = json_decode($this->find($id)->answer, true)[$trueAnswer];
    	} elseif ($type == Question::TYPE_MULTIPLE_ANSWER) {
    		foreach ($trueAnswer as $stepAnswer) {
                $answer .= json_decode($this->find($id)->answer, true)[$stepAnswer] . "; ";
            }
    	} elseif ($type == Question::TYPE_NUMBER_ANSWER) {
            $answer = "от " . $trueAnswer[0] . " до " . $trueAnswer[1];
    	} elseif ($type == Question::TYPE_TEXT_ANSWER) {
            $answer = $trueAnswer;
    	}

        return $answer;
    }
}
