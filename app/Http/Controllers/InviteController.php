<?php

namespace App\Http\Controllers;

use Log;
use Validator;
use Mail;

use App\Betacode;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\SendBetaCodeEmail;

class InviteController extends Controller
{
    public function __construct() 
    {
        $this->middleware('admin');
    }

    public function index() {
    	return view('invite', ['slug' => 'invite-user']);
    }

    public function reIndex() {
        return view('invite', ['slug' => 're-invite-user']);
    }

    public function send(Request $request) {
    	$data = $request->only(['email']);

    	$validator = Validator::make($data, [
    		'email' => 'required|email|max:255|unique:users'
    	]);

    	if ($validator->fails()) {
    		return redirect('/invite-user')->withErrors($validator)->withInput();
    	}

    	$this->sendBetaCodeEmail($data['email']);

    	return view('invited', ['email' => $data['email']]);
    }

    public function reSend(Request $request) {
        $data = $request->only(['email']);

        $validator = Validator::make($data, [
            'email' => 'required|email|max:255|unique:users'
        ]);

        if ($validator->fails()) {
            return redirect('/invite-user')->withErrors($validator)->withInput();
        }

        $email = $data['email'];

        // confirm betacode Exists
        $code = Betacode::whereEmail($email)->firstOrFail();

        $this->sendBetaCodeEmail($email);

        return view('invited', ['email' => $email]);
    }

    private function sendBetaCodeEmail($email) {
        // Push SendBetaCodeEmail jobs to Queue
        $this->dispatch(new SendBetaCodeEmail($email));
    }
}
