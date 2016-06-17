<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;

use Auth;
use Validator;

use App\User;
use App\Post;
use App\PostLike;
use App\Notification;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\SendPushNotification;

class LikeController extends Controller
{
    public function __construct() 
    {
        $this->middleware('jwt.auth');
    }

    public function like(Request $request)
    {
    	$data = $request->only(['post_id']);

    	$validator = Validator::make($data, [
            'post_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
        	$user = Auth::user();
        	$user_id = $user->id;
        	$post_id = $request->input('post_id');
        	if (PostLike::where('user_id', $user_id)->where('post_id', $post_id)->count() > 0) {
        		return response()->json(['code'=>200, 'result'=>'fail', 'error'=>'already_like']);
        	}
        	else {
        		$postLike = new PostLike;
	        	$postLike->user_id = $user_id;
	        	$postLike->post_id = $post_id;
	        	$postLike->save();

                $this->sendPostLikePushNotification($post_id, $user);

	        	return response()->json(['code'=>201, 'result'=>'success']);
        	}
        }
    }

    private function sendPostLikePushNotification($post_id, $from_user) {
        $post = Post::findOrFail($post_id);
        $post_user = $post->user()->firstOrFail();
        $message = "";
        if (isset($from_user->first_name) && $from_user->first_name != '') {
            $message = "Your post '".$post->title."' was liked by " . $from_user->first_name;
        }
        else {
            $message = "Your post '".$post->title."' was liked by " . $from_user->email;
        }

        $notification = new Notification;
        $notification->sender_id = $from_user->id;
        $notification->receiver_id = $post_user->id;
        $notification->type = 'post_liked';
        $notification->text = $message;
        $notification->save();

        $this->dispatch(new SendPushNotification($post_user->id, 'post_liked', $message));
    }
}
