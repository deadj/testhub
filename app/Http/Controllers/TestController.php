<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\Result;
use App\Models\TestTime;

class TestController extends Controller
{
	public function printPrefacePage(Request $request, int $id)
	{
		if ($request->cookie('userId') && Result::where([
				['testId', $id],
				['userId', $request->cookie('userId')]
			])->count() > 0) {

			return redirect("$id/result");
		}

		$requiredTest = Test::find($id);
		$questionsCount = Question::where('testId', $id)->count();
		$maxBalls = Question::where('testId', $id)->sum('balls');

		return view('testPreface', [
			'test' => $requiredTest,
			'questionsCount' => $questionsCount,
			'maxBalls' => $maxBalls
		]);
	}

	public function printQuestionPage(Request $request, int $id)
	{
		$test = Test::find($id);
		
		$requiredQuestion = Question::where('testId', $id)->orderBy('number')->first();
		$questionsCount = Question::where('testId', $id)->count();
		$questionsList = Question::where('testId', $id)->orderBy('number')->get();

		if (!$request->cookie('userId')) {
			$user = new User();
			$user->save();

			$this->addTestTime($user->id, $id);

			return response()
				->view('testQuestion', [
					'test' => $test,
					'question' => $requiredQuestion,
					'questionsCount' => $questionsCount,
					'questionsList' => $questionsList
				])
				->cookie('userId', $user->id, 60 * 24 * 30 * 12);
		} else {
			if (Result::where([
					['testId', $id],
					['userId', $request->cookie('userId')]
				])->count() > 0) {

				return redirect("$id/result");
			} else {
				$this->addTestTime($request->cookie('userId'), $id);

				Answer::where([
					['userId', $request->cookie('userId')],
					['testId', $id]
				])->delete();

				return response()->view('testQuestion', [
					'test' => $test,
					'question' => $requiredQuestion,
					'questionsCount' => $questionsCount,
					'questionsList' => $questionsList
				]);
			}
		}
	}

	public function addAnswer(Request $request)
	{
		$answer = new Answer();

		if (!$this->checkTestTime($request)) {
			return response()->json('lastQuestion');
		}

		if (Answer::where([
			['questionId', $request->questionId],
			['userId', $request->cookie('userId')]
		])->doesntExist()) {
			$answer->userId = $request->cookie('userId');
			$answer->questionId = $request->questionId;
			$answer->testId = $request->testId;
			$answer->value = mb_strtolower($request->value);
			$answer->done = $this->checkAnswer(
				mb_strtolower($request->value), 
				Question::find($request->questionId)->trueAnswer, 
				$request->questionType
			);
			$answer->save(); 			
		} else {
			Answer::where([
				['questionId', $request->questionId],
				['userId', $request->cookie('userId')]
			])->update(['value' => $request->value]);
		}

		$questionsCount = Question::where('testId', $request->testId)->count();

		if ($request->questionNumber - 1 < $questionsCount) {
			$requiredQuestion = Question::where([
				['testId', $request->testId],
				['number', $request->questionNumber]
			])->first();

			return response($requiredQuestion);
		} else {
			$this->saveResult($request);
			TestTime::where([
				['userId', $request->cookie('userId')],
				['testId', $request->testId]
			])->delete();
			return response()->json('lastQuestion');
		}
	}

	public function printResultPage(Request $request, int $id)
	{
		if (!$request->cookie('userId') || 
			Result::where([
				['userId', $request->cookie('userId')],
				['testId', $id]
			])->count() == 0) {

			return redirect("$id/preface");
		}
		
		$responseTest = Test::find($id);
		$responseResult = Result::where([
			['testId', $id],
			['userId', $request->cookie('userId')]
		])->first();

		return response()->view('testResult', [
			'result' => $responseResult,
			'test' => $responseTest
		]);
	}

	public function setUserName(Request $request): void
	{
		User::find($request->cookie('userId'))->update(['name' => $request->userName]);
	}

	public function getQuestionForTest(Request $request)
	{
		$requestArray = [];

		if (Answer::where([
			['userId', $request->cookie('userId')],
			['questionId', $request->questionId]
		])->exists()) {
			$requestArray['answer'] = Answer::where([
				['userId', $request->cookie('userId')],
				['questionId', $request->questionId]
			])->first()->value;
		}

		$requestArray['question'] = Question::find($request->questionId);

		return response($requestArray);
	}

	public function saveResult(Request $request)
	{
		$result = new Result();
		
		$doneAnswers = Answer::where([
			['userId', $request->cookie('userId')],
			['testId', $request->testId],
			['done', 1]
		])->get();

		$balls = 0;
		foreach ($doneAnswers as $answer) {
			$balls += Question::find($answer->questionId)->balls;
		}

		$result->testId = $request->testId;
		$result->userId = $request->cookie('userId');
		$result->balls = $balls;
		$result->save();

		$test = Test::find($request->testId);
		$countOfParticipants = $test->countOfParticipants + 1;
		$countOfPassed = $test->countOfPassed + 1;

		Test::where('id', $request->testId)->update(['countOfParticipants' => $countOfParticipants]);
		if ($result->balls >= $test->minBalls) {
			Test::where('id', $request->testId)->update(['countOfPassed' => $test->countOfPassed + 1]);
		}
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

	private function addTestTime(int $userId, int $testId): void
	{
		$testTime = new TestTime();
		$testTime->userId = $userId;
		$testTime->testId = $testId;
		$testTime->save();
	}


	private function checkTestTime(Request $request)
	{ 
		if (Test::find($request->testId)->minutesLimit != NULL) {
			$startTime = TestTime::where([
				['userId', $request->cookie('userId')],
				['testId', $request->testId]
			])->first()->created_at->getTimestamp();
			$nowTime = now()->getTimestamp();
			$testTime = Test::find($request->testId)->minutesLimit * 60;
			$pastServerTime = $nowTime - $startTime;

			if ($testTime <= $pastServerTime) {
				$this->saveResult($request);

				TestTime::where([
					['userId', $request->cookie('userId')],
					['testId', $request->testId]
				])->delete();
				
				return false;
			} else {
				return true;
			}
		}
	}
}