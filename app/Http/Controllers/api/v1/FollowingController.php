<?php

namespace App\Http\Controllers\api\v1;

use Auth;
use Validator;
use App\User;
use App\Child;
use App\Following;
use App\Media;

use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FollowingController extends Controller
{
    public function __construct() 
    {
        $this->middleware('jwt.auth');
    }

    public function index()
    {
    	$user = Auth::user();
    	$followings = $user->followings()->get();
    	$following_ids = [];

		// TODO change to iterator+reduce
    	foreach ($followings as $following) {
    		$following_ids[] = $following->child_id;
    	}

    	$invites = $user->invitesFrom()->get();
    	$children = [];

		// TODO change to iterator
    	foreach ($invites as $invite) {
    		$from_user = User::find($invite->from_user_id);
    		$user_children = $from_user->children()->with('avatar')->get();
    		$user_children = $user_children->toArray();

    		foreach ($user_children as $idx=>$child) {
    			if (count($following_ids) > 0 && in_array($child["id"], $following_ids)) {
    				$user_children[$idx]["isFollowing"] = true;
    			}
    			else {
    				$user_children[$idx]["isFollowing"] = false;
    			}
    		}
    		$children = array_merge($children, $user_children);
    	}

    	// $followings = $user->followings()->with('child.avatar')->get();
    	return response()->json(['code'=>200, 'result'=>'success', 'followings'=>$children]);
    }

    public function followings()
    {
    	$user = Auth::user();

		// TODO don't send whole child object back each time
    	$followings = $user->followings()->with('child.avatar')->get();
    	return response()->json(['code'=>200, 'result'=>'success', 'followings'=>$followings]);	
    }

    public function follow(Request $request)
    {
    	$data = $request->all();
            
	    $validator = Validator::make($data, [
	        'child_id' => 'required'
	    ]);

	    if ($validator->fails()) {
	        return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);	        
	    }
	    else {
	    	$user = Auth::user();
	    	if (Following::where('user_id', $user->id)->where('child_id', $request->input('child_id'))->count() > 0) {
	    		return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'already_followed']);		
	    	}
	    	else {
	    		$following = new Following;
	    		$following->user_id = $user->id;
	    		$following->child_id = $request->input('child_id');

				// TODO let timestamps handle this automatically
	    		$following->created_at = Carbon::now();
	    		$following->save();
	    		return response()->json(['code'=>200, 'result'=>'success']);
	    	}
	    }
    }

    public function unfollow(Request $request)
    {
    	$data = $request->only(['child_id']);
            
	    $validator = Validator::make($data, [
	        'child_id' => 'required'
	    ]);

	    if ($validator->fails()) {
	        return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);	        
	    }
	    else {
	    	$user = Auth::user();
	    	if (Following::where('user_id', $user->id)->where('child_id', $request->input('child_id'))->count() > 0) {
	    		$following = Following::where('user_id', $user->id)->where('child_id', $request->input('child_id'))->firstOrFail();
	    		$following->delete();
	    		return response()->json(['code'=>200, 'result'=>'success']);
	    	}
	    	else {
	    		return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'not_followed']);
	    	}
	    }
    }
}
