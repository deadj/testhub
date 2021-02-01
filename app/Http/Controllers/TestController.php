<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;

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

	public function printQuestionPage(Request $request, int $id)
	{
		$test = new Test();
		$test = $test->find($id);
		
		$question = new Question();
		$requiredQuestion = $question->where('testId', $id)->orderBy('number')->first();

		if (!$request->cookie('userId')) {
			$user = new User();
			$user->save();

			return response()
				->view('testQuestion', [
					'test' => $test,
					'question' => $requiredQuestion
				])
				->cookie('userId', $user->id, 60 * 24 * 30 * 12);
		} else {
			return response()->view('testQuestion', [
				'test' => $test,
				'question' => $requiredQuestion
			]);
		}
	}

	public function addAnswer(Request $request)
	{
		$answer = new Answer();
		$answer->userId = $request->cookie('userId');
		$answer->questionId = $request->questionId;
		$answer->testId = $request->testId;
		$answer->value = $request->value;
		$answer->save();

		$question = new Question();
		$questionsCount = $question->where('testId', $request->testId)->count();

		if ($request->questionNumber < $questionsCount) {
			$requiredQuestion = $question->where([
				['testId', $request->testId],
				['number', $request->questionNumber + 1]
			])->get();

			return response($requiredQuestion);
		} else {
			//вывод финальной страницы
		}
	}
}