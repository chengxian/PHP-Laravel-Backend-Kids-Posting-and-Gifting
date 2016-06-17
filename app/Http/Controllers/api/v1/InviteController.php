<?php

namespace App\Http\Controllers\api\v1;

use Log;
use Auth;
use Validator;
use App\User;
use App\Invite;
use Mail;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\SendBetaCodeEmail;
use App\Jobs\SendRegisterEmail;
use App\Jobs\SendInviteCodeEmail;
use App\Jobs\SendPushNotification;

class InviteController extends Controller
{
    public function __construct() 
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $invites = Invite::where('from_user_id', $user->id)->get();
        return response()->json(['invites' => $invites]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input_data = $request->only(['emails']);

        $validator = Validator::make($input_data, [
            'emails' => 'required|array',
            'emails.*.0' => 'email|max:255|unique:invites'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $from_user = Auth::user();
            $emails = $request->input('emails');
            foreach ($emails as $email) {
                if (Invite::where(['from_user_id' => $from_user->id, 'email' => $email])->count() == 0) {
                //     return response()->json(['code'=>402, 'result'=>'fail', 'error'=>'already_invited']);
                // }
                // else {

                    // $this->sendInviteEmail()
                    // $new_invite = new Invite;
                    // $invite_code = $new_invite->generateCode();

                    // $new_invite->invite_code = $invite_code;
                    // $new_invite->from_user_id = $from_user->id;
                    // $new_invite->email = $email;
                    // if (User::where('email', $email)->count() > 0) {
                    //     $to_user = User::where('email', $email)->first();
                    //     $new_invite->to_user_id = $to_user->id;
                    //     $new_invite->save();
                    //     $this->sendInviteEmailToUser($email, $invite_code);
                    //     // $this->sendInvitePushNotification($to_user, $from_user);
                    // }
                    // else {
                    //     $new_invite->save();
                    //     $this->sendInviteEmail($invite_code, $email, $from_user);
                    // }

                    $this->sendInviteEmail($from_user->id, $email);
                }
            }
            return response()->json(['code'=>200, 'result'=>'success']);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input_data = $request->only(['accepted']);
        $validator = Validator::make($input_data, [
            'accepted' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $invite = Invite::findOrFail($id);
            $invite->accepted = $input_data['accepted'];
            $invite->save();
            return response()->json(['code'=>200, 'result'=>'success']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function sendInviteEmail($user_id, $email) {
        // Push SendInviteEmail jobs to Queue
        $this->dispatch(new SendInviteCodeEmail($user_id, $email));
    }

    private function sendInvitePushNotification($to_user, $from_user) {
        $type = 'invite';
        $to_user_id = $to_user->id;
        $message = "";
        if (isset($this->from_user->first_name) && $from_user->first_name != '') {
            $message = "Your post was liked by " . $from_user->first_name;
        }
        else {
            $message = "Your post was liked by " . $from_user->email;
        }
        $this->dispatch(new SendPushNotification($to_user_id, $type, $message));
    }
}
