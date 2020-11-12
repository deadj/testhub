<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;

class NewTestController extends Controller
{
    public function print()
    {
    	return view('new');
    }

    public function addTest(Request $request): int
    {
    	$test = new Test();

    	$test->name 			= $request->input('testName');
    	$test->tags 			= $request->input('tags');
    	$test->foreword 		= $request->input('testForeword');
    	$test->minBalls 		= $request->input('passingScore');
        $test->maxBalls         = 0;
    	$test->minutesLimit 	= $request->input('timeLimit');
    	$test->showWrongAnswers = $request->input('showWrongAnswers');
    	$test->publicResults 	= $request->input('publicResults');

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

    public function addQuestion(Request $request): array
    {
        $testId = $request->input('testId');
        
        $question = new Question();

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
