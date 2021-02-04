<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\Result;

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
		$question = new Question();

		$answer->userId = $request->cookie('userId');
		$answer->questionId = $request->questionId;
		$answer->testId = $request->testId;
		$answer->value = $request->value;
		$answer->done = $this->checkAnswer(
			$request->value, 
			$question->find($request->questionId)->trueAnswer, 
			$request->questionType
		);
		$answer->save(); 
		
		$questionsCount = $question->where('testId', $request->testId)->count();

		if ($request->questionNumber < $questionsCount) {
			$requiredQuestion = $question->where([
				['testId', $request->testId],
				['number', $request->questionNumber + 1]
			])->first();

			return response($requiredQuestion);
		} else {
			$result = new Result();
			$answer = new Answer();
			
			$doneAnswers = $answer->where([
				['userId', $request->cookie('userId')],
				['testId', $request->testId],
				['done', 1]
			])->get();

			$balls = 0;
			foreach ($doneAnswers as $answer) {
				$balls += $question->find($answer->questionId)->balls;
			}

			$result->testId = $request->testId;
			$result->userId = $request->cookie('userId');
			$result->balls = $balls;
			$result->save();

			return response()->json('lastQuestion');
		}
	}

	public function printResultPage(Request $request, int $id)
	{
		$test = new Test();
		$result = new Result();
		
		$responseTest = $test->find($id);
		$responseResult = $result->where([
			['testId', $id],
			['userId', $request->cookie('userId')]
		])->first();

		return response()->view('testResult', [
			'result' => $responseResult,
			'test' => $responseTest
		]);
	}

	private function checkAnswer(string $userAnswer, string $trueAnswer, string $type): bool
	{
		$userAnswer = json_decode($userAnswer, true);
		$trueAnswer = json_decode($trueAnswer, true);

		if ($type == "oneAnswer" || $type == "multipleAnswers" || $type == "textAnswer") {
			if ($userAnswer == $trueAnswer) {
				return true;
			} else {
				return false;
			}
		} elseif ($type == "numberAnswer") {
			$minNum = $trueAnswer[0] - $trueAnswer[1];
			$maxNum = $trueAnswer[0] + $trueAnswer[1];

			if ($userAnswer >= $minNum && $userAnswer <= $maxNum) {
				return true;
			} else {
				return false;
			}
		}
	}
}