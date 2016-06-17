<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;

use Auth;
use Validator;

use App\User;
use App\Child;
use App\Post;
use App\PostAttachment;
use App\PostLike;
use App\Comment;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ChildPostController extends Controller
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
    public function index($child_id)
    {
        return Post::where('child_id', $child_id)->with('child.avatar')->with('attachments.media')->orderBy('created_at', 'desc')->paginate(5);
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
    public function store(Request $request, $child_id)
    {
        $post_data = $request->only(['title', 'text', 'uuid']);
        
        $validator = Validator::make($post_data, [
            'title' => 'required|max:100',
            'text' => 'string',
            'uuid' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $parent_user = Auth::user();

            $post = new Post;
            $post->user_id = $parent_user->id;
            $post->child_id = $child_id;
            $post->uuid = $request->input('uuid');
            $post->title = $request->input('title');
            if ($request->has('text')) {
                $post->text = $request->input('text');
            }
            $post->save();

            if ($request->has('attachment_id')) {
                $postAttachment = new PostAttachment;
                $postAttachment->post_id = $post->id;
                $postAttachment->attachment_id = $request->input('attachment_id');
                $postAttachment->save();
            }
            
            return response()->json(['code' => '201', 'result' => 'success', 'post_id' => $post->id]);
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
}
