<?php

namespace App\Http\Controllers\api\v1;

use Log;
use Auth;
use Validator;

use App\User;
use App\Child;
use App\Notification;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function __construct() 
    {
        $this->middleware('jwt.auth');
    }

    public function index(Request $request) {
    	$user = Auth::user();
		$children = $user->children()->get();
		$children_ids = [];
		foreach ($children as $child) {
			Log::info('child_id: ' . $child->id);
			$children_ids[] = $child->id;
		}
		$notifications = Notification::where('receiver_id', $user->id)->whereNotIn('child_id', $children_ids)->with('sender.avatar')->get();
    	
    	return response()->json(['code'=>200, 'result'=>'success', 'notifications'=>$notifications]);
    }

    public function getByChild(Request $request, $child_id) {
    	$user = Auth::user();
		$notifications = Notification::where('receiver_id', $user->id)->where('child_id', $child_id)->with('sender.avatar')->get();	
    	return response()->json(['code'=>200, 'result'=>'success', 'notifications'=>$notifications]);
    }
}
