<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;

class TestController extends Controller
{
	public function printPrefacePage(int $id)
	{
		$test = new Test();
		$question = new Question();

		$requiredTest = $test->find($id);
		$questionsCount = $question->where('testId', $id)->count();
		$maxBalls = $question->where('testId', $id)->sum('balls');

		return view('testPreface', [
			'test' => $requiredTest,
			'questionsCount' => $questionsCount,
			'maxBalls' => $maxBalls
		]);
	}

	public function printQuestionPage(int $id)
	{
		$test = new Test();
		$test = $test->find($id);
		
		$question = new Question();
		$requiredQuestion = $question->where('testId', $id)->orderBy('number')->first();
		
		return view('testQuestion', [
			'test' => $test,
			'question' => $requiredQuestion
		]);
	}
}