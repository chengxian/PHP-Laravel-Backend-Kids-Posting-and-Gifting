<?php

namespace App\Http\Controllers\api\v1;

use Auth;
use Validator;
use Carbon\Carbon;

use App\User;
use App\Child;
use Kidgifting\USAlliance\Models\Balance;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function __construct() 
    {
        $this->middleware('jwt.auth');
    }

    public function balaces(Request $request, $child_id)
    {
    	$child = Child::findOrFail($child_id);

        $loan_application = $child->loanApplication()->get();

        $loanApplication = $this->loanApplication()->getRelated();
        if ($loanApplication != null && in_array($loanApplication->status, LoanApplication::$positiveApprovals) {
            return $loanApplication->balances();    
        }
        else {
            return null;
        }        
    	$balances = $child->balances();
    	return response()->json(['code'=>200, 'result'=>'success', 'balances'=>$balances]);
    }
}
