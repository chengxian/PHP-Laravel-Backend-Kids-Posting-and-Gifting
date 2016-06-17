<?php

namespace App\Http\Controllers\api\v1;

use Auth;
use Validator;

use App\Child;
use App\Post;
use App\PostAttachment;
use App\PostLike;
use App\Comment;
use App\Notification;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Jobs\SendPushNotification;

class PostCommentController extends Controller
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
    public function index($post_id)
    {
        return Comment::where('post_id', $post_id)->with('user.avatar')->orderBy('updated_at', 'desc')->paginate(5);
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
    public function store(Request $request, $post_id)
    {
        $data = $request->only(['comment']);

        $validator = Validator::make($data, [
            'comment' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $user = Auth::user();
            $comment = new Comment;
            $comment->user_id = $user->id;
            $comment->post_id = $post_id;
            $comment->comment = $request->input('comment');
            $comment->save();

            $this->sendPostCommentedPushNotification($post_id, $user);
            return response()->json(['code'=>201, 'result'=>'success', 'comment_id'=>$comment->id]);
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
        //
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

    private function sendPostCommentedPushNotification($post_id, $from_user) {
        $post = Post::findOrFail($post_id);
        $post_user = $post->user()->firstOrFail();

        $message = "";
        if (isset($from_user->first_name) && $from_user->first_name != '') {
            $message = "Your post '".$post->title."' was commented by " . $from_user->first_name;
        }
        else {
            $message = "Your post '".$post->title."' was commented by " . $from_user->email;
        }
        $notification = new Notification;
        $notification->user_id = $post_user->id;
        $notification->type = 'post_commented';
        $notification->text = $message;
        $notification->save();

        $this->dispatch(new SendPushNotification($post_user->id, 'post_commented', $message));
    }

}
