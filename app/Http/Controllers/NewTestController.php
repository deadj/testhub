<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use Validator;

class NewTestController extends Controller
{
    private $question;
    private $test;

    public function __construct()
    {
        $this->question = new Question();
        $this->test = new Test();
    }

    public function print()
    {
    	return view('new');
    }

    public function addTest(Request $request)
    {
        $validator = $this->getTestValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            $test = new Test();

            $this->test->name              = $request->input('testName');
            $this->test->tags              = $request->input('tags');
            $this->test->foreword          = $request->input('testForeword');
            $this->test->minBalls          = $request->input('minBalls');
            $this->test->maxBalls          = 0;
            $this->test->minutesLimit      = $request->input('timeLimit');
            $this->test->showWrongAnswers  = $request->input('showWrongAnswers');
            $this->test->publicResults     = $request->input('publicResults');

            if ($request->input('showWrongAnswers')) {
                $this->test->showWrongAnswers = 1;
            } else {
                $this->test->showWrongAnswers = 0;
            }

            if ($request->input('publicResults')) {
                $this->test->publicResults = 1;
            } else {
                $this->test->publicResults = 0;
            }

            $this->test->save();      

            return $this->test->id;     
        }
    }

    public function addQuestion(Request $request)
    {
        $validator = $this->getQuestionValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            $testId = $request->input('testId');

            $this->question->testId     = $testId;
            $this->question->questions  = $request->input('question');
            $this->question->balls      = $request->input('balls');
            $this->question->type       = $request->input('type');
            $this->question->answer     = $request->input('answer');
            $this->question->trueAnswer = $request->input('trueAnswer');
            $this->question->number     = $this->question->where('testId', $testId)->count() + 1;

            $this->question->save();

            $questionCount = $this->question->where('testId', $testId)->count();
            $balls = $this->question->where('testId', $testId)->get()->sum('balls');
            $time = Test::find($testId)->minutesLimit;

            if ($time == NULL) {
                $time = "&#8734;";
            } else {
                $time /= $questionCount;
            }

            $trueAnswer = $this->getTrueAnswer($this->question->id);

            return array(
                'questionId'       => $this->question->id,
                'questionCount'    => $questionCount,
                'balls'            => $balls,
                'time'             => $time,
                'cutQuestion'      => mb_substr($this->question->questions, 0, 20) . "...",
                'fullQuestion'     => $this->question->questions,
                'cutAnswer'        => mb_substr($trueAnswer, 0, 20) . "...",
                'fullAnswer'       => $trueAnswer,
                'ballsForQuestion' => $this->question->balls,
                'questionNumber'   => $this->question->number
            );
        }
    }

    public function changeOrderOfQuestionNumbers(Request $request)
    {
        foreach (json_decode($request->numbersArray, true) as $number) {
            $this->question->where('id', $number[0])->update(['number' => $number[1]]);
        }    
    }

    private function getTrueAnswer(int $id)
    {
        $trueAnswer = json_decode($this->question->find($id)->trueAnswer, true);
        $type = $this->question->find($id)->type;
        $answer = NULL;

        if ($type == Question::TYPE_ONE_ANSWER) {
            $answer = json_decode($this->question->find($id)->answer, true)[$trueAnswer];
        } elseif ($type == Question::TYPE_MULTIPLE_ANSWER) {
            foreach ($trueAnswer as $stepAnswer) {
                $answer .= json_decode($this->question->find($id)->answer, true)[$stepAnswer] . "; ";
            }
        } elseif ($type == Question::TYPE_NUMBER_ANSWER) {
            $answer = "от " . $trueAnswer[0] . " до " . $trueAnswer[1];
        } elseif ($type == Question::TYPE_TEXT_ANSWER) {
            $answer = $trueAnswer;
        }

        return $answer;        
    }

    private function getTestValidator(object $request): object
    {
        return Validator::make($request->all(), [
            'testName' => 'required',
            'minBalls' => 'required|numeric|min:1',
        ],[
            'testName.required' => 'Введите название',
            'minBalls.required' => 'Введите минимальный балл',  
            'minBalls.min' => 'Минимальный балл должен быть не меньше 1',         
        ]);
    }

    private function getQuestionValidator(object $request): object
    {
        return Validator::make($request->all(), [
            'testId'   => 'required',
            'question' => 'required',
            'balls' => 'required|numeric',
            'type' => 'required',
            'trueAnswer' => 'required'
        ],[
            'question.required' => 'Введите вопрос',
            'balls.required' => 'Введите количество баллов',
            'trueAnswer' => 'Правильный ответ не выбран'
        ]); 
    }
}
