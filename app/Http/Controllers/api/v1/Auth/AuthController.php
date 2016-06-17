<?php

namespace App\Http\Controllers\api\v1\Auth;

use Auth;
use Password;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail;

use App\User;
use App\Invite;
use App\Betacode;
use App\Following;

use Validator;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Auth\Passwords\PasswordBroker;

use App\Jobs\SendRegisterEmail;
use App\Jobs\SendRequestBetacodeEmail;

class AuthController extends Controller
{
    protected $user;

	public function authenticate(Request $request) {
		// grab credentials from the request
		$credentials = $request->only(['email', 'password']);

		try {
			// attempt to verify the credentials and create a token for the user
			if (!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_credentials']);
			}
		} catch (JWTException $e) {
			// somthing went wrong whilst attempting to encode the token
			return response()->json(['code'=>500, 'result'=>'fail', 'error'=>'could_not_create_token']);
		}

		// all good so return the token
        $response_obj = compact('token');
        $response_obj['code'] = '200';
        $response_obj['result'] = 'success';
        $user = Auth::user();
        $response_obj['user'] = User::with('avatar')->findOrFail($user->id);
		return response()->json($response_obj);
	}

    private function codeResponse($payload, $codePayload) {
        if (!empty($codePayload)) {
            $payload = array_merge($payload, $codePayload);
        }
        return response()->json($payload);
    }

    /* 
     * signup api
     */
    public function signup(Request $request) {
        $user = Auth::user();
        $codePayload = [];

        // TODO handle if beta code has been used already
    	if ($request->has('betacode')) {            
            $betacode = Betacode::where('betacode', $request->input('betacode'))->where('used', false)->first();

            // TODO still register them but prompt them to request a beta code in A-0.9
            if (!$betacode) {
                $codePayload['code_warning'] = 'invalid_betacode';
            } else {
                // TODO accept the betacode
                $betacode->used = true;
                $betacode->save();
                $codePayload['betacode_accepted'] = true;
                // TODO make the user a full user (will see beta code request screen otherwise)
                /*
                 * $user->full_user = true;
                 * $user>save();
                 */

                // TODO confirm the beta code belongs to this email?
            }
        }
        else if ($request->has('invite_code')) {

            $invite = Invite::where('invite_code', $request->input('invite_code'))->where('accepted', false)->first();

            if (!$invite) {
                if (array_key_exists('code_warning', $codePayload)) {
                    $codePayload['code_warning'] .= 'invalid_invite_code';
                } else {
                    $codePayload['code_warning'] = 'invalid_invite_code';
                }
            }

            // TODO make the user a full user (will see beta code request screen otherwise)
            /*
             * $user->full_user = true;
             * $user>save();
             */

            $invite->accepted = true;
            $invite->save();
        }

        if ($request->has('facebook_id')) {
            $data = $request->only(['facebook_id', 'email', 'first_name', 'last_name']);
            
            $validator = Validator::make($data, [
                'facebook_id' => 'required',
                'email' => 'required|email|max:255',
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
            ]);

            if ($validator->fails()) {
                $payload = ['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()];
                return $this->codeResponse($payload, $codePayload);
            }
            else {
                $exist_user = User::where('facebook_id', $request->input('facebook_id'))->first();

                if ($exist_user) {
                    $token = JWTAuth::fromUser($exist_user);
                    $user = Auth::user();
                    $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($exist_user->id)];
                    return $this->codeResponse($payload, $codePayload);
                }
                else {
                    $exist_user = User::where('email', $request->input('email'))->first();
                    if ($exist_user) {
                        $exist_user->facebook_id = $request->input('facebook_id');
                        $exist_user->first_name = $request->input('first_name');
                        $exist_user->last_name = $request->input('last_name');
                        $exist_user->save();
                        $token = JWTAuth::fromUser($exist_user);

                        // TODO check to see if email has an unused beta code, if so use it
                        $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($exist_user->id)];
                        return $this->codeResponse($payload, $codePayload);
                    }
                    else {
                        $user_data = [
                            'facebook_id' => $request->input('facebook_id'),
                            'email' => $request->input('email'),
                            'first_name' => $request->input('first_name'),
                            'last_name' => $request->input('last_name')
                        ];
                        $token = $this->createUserWithData($user_data);
                        $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($this->user->id)];
                        return $this->codeResponse($payload, $codePayload);
                    }
                }
            }
        }
        else if ($request->has('instagram_id')) {
            $data = $request->only(['instagram_id', 'email', 'first_name', 'last_name']);
            
            $validator = Validator::make($data, [
                'instagram_id' => 'required',
                // 'email' => 'required|email|max:255',
                'first_name' => 'max:50',
                'last_name' => 'max:50',
            ]);

            if ($validator->fails()) {
                $payload = ['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()];
                return $this->codeResponse($payload, $codePayload);
            }
            else {
                $exist_user = User::where('instagram_id', $request->input('instagram_id'))->first();

                if ($exist_user) {
                    $token = JWTAuth::fromUser($exist_user);
                    $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($exist_user->id)];
                    return $this->codeResponse($payload, $codePayload);
                }
                else {
                    $exist_user = User::where('email', $request->input('email'))->first();
                    if ($exist_user) {
                        $exist_user->instagram_id = $request->input('instagram_id');
                        $exist_user->first_name = $request->input('first_name');
                        $exist_user->last_name = $request->input('last_name');
                        $exist_user->save();
                        $token = JWTAuth::fromUser($exist_user);
                        $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($exist_user->id)];
                        return $this->codeResponse($payload, $codePayload);
                    }
                    else {
                        $user_data = [
                            'instagram_id' => $request->input('instagram_id'),
                            'email' => $request->input('email'),
                            'first_name' => $request->input('first_name'),
                            'last_name' => $request->input('last_name')
                        ];
                        $token = $this->createUserWithData($user_data);

                        // TODO check to see if email has an unused beta code, if so use it
                        $user = Auth::user();
                        $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($this->user->id)];
                        return $this->codeResponse($payload, $codePayload);
                    }                        
                }
            }
        }
        else {
            $data = $request->only(['email', 'password']);

            $validator = Validator::make($data, [
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|max:60'
            ]);

            if ($validator->fails()) {
                $payload = ['code'=>401, 'result'=>'fail', 'error'=>'email_exists'];
                return $this->codeResponse($payload, $codePayload);
            }
            else {
                $user_data = [
                    'email' => $request->input('email'),
                    'password' => bcrypt($request->input('password'))
                ];

                if ($request->has('avatar_id')) {
                    $user_data['avatar_id'] = $request->input('avatar_id');
                }

                $token = $this->createUserWithData($user_data);

                // Check to see if this email has an unused beta code
                $betacode = Betacode::where('email', $user_data['email'])->first();
                if ($betacode) {
                    $betacode->used = true;
                    $betacode->save();
                    $codePayload['betacode_accepted'] = true;
                    // $user->full_user = 1;
                    // $user>save();                    
                }

                $user = Auth::user();
                $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($this->user->id)];
                return $this->codeResponse($payload, $codePayload);
            }                    
        }
    }

    private function createUserWithData($user_data) {
        $user = User::create($user_data);
        $this->user = $user;
        $token = JWTAuth::fromUser($user);
        // Push SendRegisterEmail to Queue
        $this->sendRegisterEmail($user);
        return $token;
    }

    public function requestBetacode(Request $request) {
        if ($request->has('email')) {
            $data = $request->only(['email']);
            $validator = Validator::make($data, [
                //'email' => 'required|email|max:255|exists:users'
                'email' => 'required|email|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_email']);
            }
            else {
                $this->sendRequestBetacodeEmail($request->input('email'));
                return response()->json(['code'=>200, 'result'=>'success']);
            }
        }
        else {
            return response()->json(['code'=>'401', 'result'=>'fail', 'error'=>'undefined_email']);
        }
    }

    private function sendRequestBetacodeEmail($email) {
        $this->dispatch(new SendRequestBetacodeEmail($email));
    }

    private function sendRegisterEmail($user) {
        // Push SendRegisterEmail jobs to Queue
        $this->dispatch(new SendRegisterEmail($user));
    }

    /*
     * Reset password
     */
    public function sendResetPasswordRequest(Request $request) {
        if ($request->has('email')) {
            $data = $request->only(['email']);
            $validator = Validator::make($data, [
                //'email' => 'required|email|max:255|exists:users'
                'email' => 'required|email|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_email']);
            }
            else {
                $response = Password::sendResetLink($data, function($message) {
                    $message->subject('Reset Password Link');
                });

                switch ($response) {
                    case PasswordBroker::RESET_LINK_SENT:
                        return response()->json(['code' => '201', 'result' => 'success']);
                        break;
                    
                    default:
                        return response()->json(['code' => '401', 'result' => 'fail', 'error' => 'invalid_user']);
                        break;
                }
            }
        }
        else {
            return response()->json(['code'=>'401', 'result'=>'fail', 'error'=>'undefined_email']);
        }
    }    
}
