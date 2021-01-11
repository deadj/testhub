<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class TestsController extends Controller
{
    public function printPage(Request $request, int $page)
    {
    	$test = new Test();
    	$tests = $test->orderBy('id', 'desc')->skip(($page - 1) * 10)->take(10)->get();

    	return view('tests', [
    		'tests' => $tests,
    		'testsCount' => $test->count(),
    		'page' => $page,
    		'pagesCount' => ceil($test->count() / 10)
    	]);
    }
}
