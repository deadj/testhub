<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;

class TestController extends Controller
{
	public function printPage(int $id)
	{
		$test = new Test();
		$queston = new Question();

		$requiredTest = $test->find($id);
		$questionsCount = $queston->where('testId', $id)->count();

		return view('test', [
			'test' => $requiredTest,
			'questionsCount' => $questionsCount
		]);
	}
}