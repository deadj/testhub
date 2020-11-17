<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use Validator;
// use App\Http\Requests\NewTestRequest;

class NewTestController extends Controller
{
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

            $test->name              = $request->input('testName');
            $test->tags              = $request->input('tags');
            $test->foreword          = $request->input('testForeword');
            $test->minBalls          = $request->input('minBalls');
            $test->maxBalls          = 0;
            $test->minutesLimit      = $request->input('timeLimit');
            $test->showWrongAnswers  = $request->input('showWrongAnswers');
            $test->publicResults     = $request->input('publicResults');

            if ($request->input('showWrongAnswers')) {
                $test->showWrongAnswers = 1;
            } else {
                $test->showWrongAnswers = 0;
            }

            if ($request->input('publicResults')) {
                $test->publicResults = 1;
            } else {
                $test->publicResults = 0;
            }

            $test->save();      

            return $test->id;     
        }
    }

    public function addQuestion(Request $request)
    {
        $validator = $this->getQuestionValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            $question = new Question();

            $testId = $request->input('testId');

            $question->testId = $testId;
            $question->questions = $request->input('question');
            $question->balls = $request->input('balls');
            $question->type = $request->input('type');
            $question->answer = $request->input('answer');
            $question->trueAnswer = $request->input('trueAnswer');

            $question->save();

            $questionCount = $question->where('testId', $testId)->count();
            $balls = $question->where('testId', $testId)->get()->sum('balls');
            $time = Test::find($testId)->minutesLimit / $questionCount;

            return array(
                'questionCount' => $questionCount,
                'balls'         => $balls,
                'time'          => $time
            );            
        }
    }

    private function getTestValidator(object $request): object
    {
        return Validator::make($request->all(), [
            'testName' => 'required',
            'minBalls' => 'required|numeric|min:1'
        ],[
            'testName.required' => 'Название является обязательным полем',
            'minBalls.required' => 'Должен быть указан минимальный балл',  
            'minBalls.min' => 'Минимальный балл должен быть не меньше 1',            
        ]);
    }

    private function getQuestionValidator(object $request): object
    {
        return Validator($request->all(), [
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
