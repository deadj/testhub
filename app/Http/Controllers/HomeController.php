<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class HomeController extends Controller
{
    public function print()
    {
    	$test = new Test();
    	$tests = $test->orderBy('id', 'desc')->take(10)->get();

    	return view('home', ['tests' => $tests]);
    }
}
