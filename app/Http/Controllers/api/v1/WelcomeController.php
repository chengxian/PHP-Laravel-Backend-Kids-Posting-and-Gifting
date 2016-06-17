<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    public function index() {
    	return response()->json([
    		'name' 		=> 'kidgifting Api',
    		'message'	=> 'Welcome to kidgifting Api. This is a base endpoint of version 1.0',
    		'version'	=> '1.0',
    		'links'		=> [
    			[
    				'rel'	=> 'self',
    				'href' 	=> route(\Route::currentRouteName())
    			],
    			[
    				'rel'	=> 'api.v1.index',
    				'href' 	=> route('api.v1.index')
    			]
    		]
    	]);
    }
}
