<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Test extends Controller
{
    public function test(Request $request)
    {
    	dd(app('url'));
        return 'test';
    }
}
