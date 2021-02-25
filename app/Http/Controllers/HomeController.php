<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class HomeController extends Controller
{
    public function print()
    {
    	$tests = Test::where('done', true)->orderBy('id', 'desc')->take(10)->get();
    	return view('home', ['tests' => $tests]);
    }
}
