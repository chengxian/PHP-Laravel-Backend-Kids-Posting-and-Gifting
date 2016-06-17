<?php

namespace App\Http\Controllers\api\v1;

use Auth;
use Validator;

use App\User;
use App\Child;
use App\FundingContribution;
use App\Post;
use App\Comment;
use App\Notification;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\SendGiveGiftEmail;
use App\Jobs\SendReceiveGiftEmail;
use App\Jobs\SendPushNotification;

class ContributionController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function gifts(Request $request) {
    	$user = Auth::user();
    	$gifts = FundingContribution::where('is_gift', true)->where('to_user_id', $user->id)->with('user')->with('child')->get();
    	return response()->json(['code'=>'200', 'result'=>'success', 'gifts'=>$gifts]);
    }

    public function giftsByChild(Request $request, $child_id) {
    	$user = Auth::user();
    	$gifts = FundingContribution::where('is_gift', true)->where('to_user_id', $user->id)->where('child_id', $child_id)->with('user')->with('child')->get();
    	return response()->json(['code'=>'200', 'result'=>'success', 'gifts'=>$gifts]);	
    }

    public function giveGift(Request $request) {
        $request_data = $request->all();
        
        $validator = Validator::make($request_data, [
        	'child_id' => 'required|integer',
        	'funding_account_id' => 'required|integer',
            'amount' => 'required|numeric',
            'text' => 'string',
            // 'is_recurring' => 'boolean',
            // 'recurring_type' => 'require_if:is_recurring,true|string|in:daily,weekly,monthly,yearly',
            // 'post_id' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
        	$user = Auth::user();
        	$gift = new FundingContribution;
        	$gift->user_id = $user->id;
        	$child = Child::findOrFail($request->input('child_id'));
        	$child_parent = $child->parent()->firstOrFail();
        	$gift->child_id = $request->input('child_id');
        	$gift->to_user_id = $child_parent->id;
        	// $gift->funding_account_id = $request->input('funding_account_id');
        	$gift->is_gift = true;
        	$gift->amount = $request->input('amount');
        	if ($request->has('text')) {
        		$gift->gift_message = $request->input('text');
        	}
        	if ($request->has('is_recurring') && $request->input('is_recurring') == true) {
            	$gift->is_recurring = true;
            	$gift->recurring_type = $request->input('recurring_type');
            }

            $gift->save();

            if ($request->has('post_id') && !$request->input('post_id')) {
	            $comment = new Comment;
	            $comment->user_id = $user->id;
	            $comment->post_id = $request->input('post_id');
	            $comment->is_gift = true;	            
	            $comment->comment = $request->input('text');
	            $comment->save();            	
            }

            if ($gift->user_id != $gift->to_user_id) {
            	$this->sendGiveGiftEmail($gift);
	            $this->sendReceiveGiftEmail($gift);
	            $this->sendGiveGiftPushNotification($gift);
	            $this->sendReceiveGiftPushNotification($gift);
            }	            

            return response()->json(['code' => '201', 'result' => 'success', 'gift_id' => $gift->id]);
        }
    }

    private function sendGiveGiftEmail($gift) {
    	$from_user = $gift->user()->firstOrFail();
    	$child = $gift->child()->firstOrFail();
    	$to_user = $child->parent()->firstOrFail();
    	$amount = $gift->amount;
    	$this->dispatch(new SendGiveGiftEmail($from_user, $to_user, $child, $amount));
    }

    private function sendReceiveGiftEmail($gift) {
    	$from_user = $gift->user()->firstOrFail();
    	$child = $gift->child()->firstOrFail();
    	$to_user = $child->parent()->firstOrFail();
    	$amount = $gift->amount;
    	$this->dispatch(new SendReceiveGiftEmail($from_user, $to_user, $child, $amount));
    }

    private function sendGiveGiftPushNotification($gift) {
    	$from_user = $gift->user()->firstOrFail();
    	$child = $gift->child()->firstOrFail();
    	$to_user = $child->parent()->firstOrFail();
    	$amount = $gift->amount;

        $message = "";
        if (isset($child->first_name) && $child->first_name != '') {
            $message = "You sent the gift($".$amount.") to ".$child->first_name;
        }
        else {
            $message = "You sent the gift($".$amount.") to ".$child->first_name;
        }

        $notification = new Notification;
        $notification->sender_id = $from_user->id;
        $notification->receiver_id = $from_user->id;
        $notification->child_id = $gift->child_id;
        $notification->type = 'gift_sent';
        $notification->text = $message;
        $notification->save();

    	$this->dispatch(new SendPushNotification($from_user->id, 'gift_sent', $message));
    }

    private function sendReceiveGiftPushNotification($gift) {
    	$from_user = $gift->user()->firstOrFail();
    	$child = $gift->child()->firstOrFail();
    	$to_user = $child->parent()->firstOrFail();
    	$amount = $gift->amount;

        $message = "";
        if (isset($from_user->first_name) && $from_user->first_name != '') {
            $message = "Your child(".$child->first_name.") received the gift($".$amount.") to ".$from_user->first_name;
        }
        else {
            $message = "Your child(".$child->first_name.") received the gift($".$amount.") to ".$from_user->email;
        }

        $notification = new Notification;
        $notification->sender_id = $from_user->id;
        $notification->receiver_id = $to_user->id;
        $notification->child_id = $gift->child_id;
        $notification->type = 'gift_received';
        $notification->text = $message;
        $notification->save();

    	$this->dispatch(new SendPushNotification($from_user->id, 'gift_received', $message));
    }
}
