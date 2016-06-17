<?php

namespace App\Http\Controllers\api\v1;

use Auth;
use Validator;
use App\User;
use App\Child;
use App\Media;

use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ChildController extends Controller
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
        $children = $user->children()->with('avatar')->get();
        return response()->json(['children'=>$children]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $child_data = $request->all();
        
        $validator = Validator::make($child_data, [  
            'first_name'=> 'required|string|max:50',
            'last_name'=> 'required|string|max:50',
            'birthday'=> 'required|date_format:d-m-Y',
            'wants'=> 'string',
            'avatar_id'=> 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $parent_user = Auth::user();

            $child = new Child;
            $child->parent_id = $parent_user->id;
            $child->first_name = $request->input('first_name');
            $child->last_name = $request->input('last_name');
            $birthday = $request->input('birthday');
            $child->birthday = Carbon::createFromFormat('d-m-Y', $birthday)->format('Y-m-d');
            $child->wants = $request->input('wants');
            
            if ($request->has('avatar_id')) {
                $child->avatar_id = $request->input('avatar_id');
            }

            $child->save();

            if (!$parent_user->is_parent) {
                $parent_user->is_parent = true;
                $parent_user->accepted_kf_toc = true;
                $parent_user->accepted_kf_toc_at = Carbon::now();
                $parent_user->save();
            }

            return response()->json(['code' => '201', 'result' => 'success', 'child_id' => $child->id]);
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
        $child_data = $request->all();
        
        $validator = Validator::make($child_data, [  
            'first_name'=> 'string|max:50',
            'last_name'=> 'string|max:50',
            'birthday'=> 'date_format:d-m-Y',
            'wants'=> 'string',
            'avatar_id'=> 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $parent_user = Auth::user();

            $child = Child::findOrFail($id);

            if ($child->parent_id != $parent_user->id) {
                return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'not_your_child']);       
            }
            else {
                $child->first_name = $request->input('first_name');
                $child->last_name = $request->input('last_name');
                $birthday = $request->input('birthday');
                $child->birthday = Carbon::createFromFormat('d-m-Y', $birthday)->format('Y-m-d');
                $child->wants = $request->input('wants');
                if ($request->has('first_name')) {
                    $child->first_name = $request->input('first_name');
                }
                if ($request->has('last_name')) {
                    $child->last_name = $request->input('last_name');
                }
                if ($request->has('birthday')) {
                    $birthday = $request->input('birthday');
                    $child->birthday = Carbon::createFromFormat('d-m-Y', $birthday)->format('Y-m-d');
                }
                if ($request->has('wants')) {
                    $child->wants = $request->input('wants');
                }
                if ($request->has('avatar_id')) {
                    $child->avatar_id = $request->input('avatar_id');
                }

                $child->save();

                return response()->json(['code' => '201', 'result' => 'success', 'child_id' => $child->id]);
            }                
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
}
