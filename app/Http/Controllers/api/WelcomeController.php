<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    public function index() {
    	return response()->json([
    		'name' 		=> 'kidgifting Api',
    		'message'	=> 'Welcome to kidgifting Api. This is a base endpoint',
    		'version'	=> 'n/a',
    		'links'		=> [
    			[
    				'rel'	=> 'self',
    				'href' 	=> route(\Route::currentRouteName())
    			],
    			[
    				'rel'	=> 'api.index',
    				'href' 	=> route('api.index')
    			]
    		]
    	]);
    }
}
