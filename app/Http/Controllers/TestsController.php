<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class TestsController extends Controller
{
    public function printPage(Request $request)
    {
    	$test = new Test();

        if ($request->has('page')) {            
            $page = $request->page;
        } else {
            $page = 1;
        }

        if ($request->has('search')) {
            $search = $request->search;

            $namesSearch = $test->orderBy('id', 'desc')->where('name', 'like', '%' . $search . '%')->get();
            $tagsSearch = $test->orderBy('id', 'desc')->whereJsonContains('tags', $search)->get();
            $tests = $namesSearch->merge($tagsSearch);
            $testsCount = $tests->count();
            $page = $this->getRealPage($testsCount, $page);
            $tests = $tests->skip(($page - 1) * 10)->take(10);
        } else {
            $search = "";
            $testsCount = $test->count();
            $page = $$this->getRealPage($testsCount, $page);
            $tests = $test->orderBy('id', 'desc')->skip(($page - 1) * 10)->take(10)->get();
        }

    	return view('tests', [
    		'tests' => $tests,
    		'testsCount' => $testsCount,
    		'page' => $page,
    		'pagesCount' => ceil($testsCount / 10),
            'search' => $search
    	]);
    }

    private function getRealPage(int $testsCount, int $userPage): int
    {
        if ($userPage > ceil($testsCount / 10)) {
            return ceil($testsCount / 10);
        } else {
            return $userPage;
        }
    }
}
