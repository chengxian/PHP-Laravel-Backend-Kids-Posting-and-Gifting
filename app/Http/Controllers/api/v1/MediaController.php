<?php

namespace App\Http\Controllers\api\v1;

use App\Media;
use App\PostAttachment;
use App\CommentAttachment;
use Log;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
	public function uploadAvatar(Request $request) {
		if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $destinationPath = 'uploads/avatars';
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $mimetype = $request->file('avatar')->getMimeType();
            $fullpath = url($destinationPath . '/' . $filename);
            $request->file('avatar')->move($destinationPath, $filename);
            

            $media = new Media;
            $media->url = $fullpath;
            $media->mime_type = $mimetype;
            $media->filename = $filename;

            $media->save();

            return response()->json(['code'=>'201', 'result'=>'success', 'media_id'=>$media->id]);
        }
        else {
        	return response()->json(['code'=>'401', 'result'=>'fail', 'error'=>'undefined_file', 'request' => $request->file()]);
        }
	}
    
    public function uploadAttachment(Request $request) {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $destinationPath = 'uploads/images';
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $mimetype = $request->file('image')->getMimeType();
            $fullpath = url($destinationPath . '/' . $filename);
            $request->file('image')->move($destinationPath, $filename);
            

            $media = new Media;
            $media->url = $fullpath;
            $media->mime_type = $mimetype;
            $media->filename = $filename;
            $media->save();

            return response()->json(['code'=>'201', 'result'=>'success', 'media_id'=>$media->id]);
        }
        else {
            return response()->json(['code'=>'401', 'result'=>'fail', 'error'=>'undefined_file', 'request' => $request->file()]);
        }
    }
}
