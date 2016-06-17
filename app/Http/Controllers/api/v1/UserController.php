<?php

namespace App\Http\Controllers\api\v1;

use Auth;
use Kidgifting\LaraVault\LaraVaultHasher;
use Validator;
use Carbon\Carbon;

use App\User;
use App\Child;
use App\Media;
use App\Setting;
use App\Device;

use DwollaSwagger\ApiException;
use Illuminate\Console\Command;
use Imagick;
use Kidgifting\DwollaWrapper\DwollaWrapperCustomerClient;
use Kidgifting\DwollaWrapper\Models\DwollaVerifiedCustomer;
use Kidgifting\DwollaWrapper\Models\DwollaUnerifiedCustomer;
use Kidgifting\DwollaWrapper\Models\DwollaSourceAccount;
use Kidgifting\USAlliance\Models\LoanApplication;
use Kidgifting\USAlliance\USAClient;
use Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct() 
    {
        $this->middleware('jwt.auth');
    }

    /* 
     *  create parent's dwolla verified account
     */
    public function createDwollaVerifiedAccount(Request $request, USAClient $usaClient, DwollaWrapperCustomerClient $dwollaClient, LaraVaultHasher $hasher)
    {
        $data = $request->all();
        
        $validator = Validator::make($data, [  
            'first_name'=> 'required|string|max:50',
            'last_name'=> 'required|string|max:50',
            'dob'=> 'required|date_format:d-m-Y',
            'phone'=> 'required|string',// 'phone'=> 'required|string|regex:/^((\\+)|(00))[0-9]{6,14}$/',
            'ssn'=> 'required|integer',
            'us_citizen'=> 'required|boolean',
            'street'=> 'required|string',
            'street1'=> 'string',
            'city'=> 'required|string',
            'state'=> 'required|string',
            'postcode'=> 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $user = Auth::user();
        
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->dob = Carbon::createFromFormat('d-m-Y', $request->input('dob'))->format('Y-m-d');
            $user->phone = $request->input('phone');
            $user->street = $request->input('street');
            if ($request->has('street1') && $request->input('street1') != '') {
                $user->street1 = $request->input('street1');
            }
            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->country = 'USA';
            $user->postcode = $request->input('postcode');

            $ssn = $request->input('ssn');

            $user->save();

            /*
            * Create Verified Dwolla user
            * https://docs.google.com/document/d/1udXoBJiWx0fCBDqvTAsG7sZMuwQ_eu6m9_paDDfAkPg/edit#heading=h.9m4iulxxqpf1
            * THROWS
            */

            $client_ip = $request->ip();
            $dwollaCustomerId = null;
            try {
                $dwollaCustomerId = $dwollaClient->createOrUpdateVerifiedCustomer(
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->street,
                    $user->street1,
                    $user->city,
                    $user->state,
                    $user->postcode,
                    $user->dob,
                    $user->phone,
                    $ssn,
                    $client_ip,
                    null
                );
            } catch (ApiException $e) {
                // something else went wrong besides customer already existing
                if (!$dwollaCustomerId) {
                    throw $e;
                }
            }

            // create verified dwolla customer for the parent
            // TODO check if customer actually verified in Dwolla. Webhook?
            $dwollaCustomer = DwollaVerifiedCustomer::firstOrNew([
                'dwolla_id_hashed' => bcrypt($dwollaCustomerId)
            ]);
            $dwollaCustomer->dwolla_id = $dwollaCustomerId;
            $dwollaCustomer->save();
            // associate the verified dwolla account with the parent
            $user->dwollaCustomer()->associate($dwollaCustomer);
            $user->save();

            /*
             * Create US Alliance Account Application for each Child the user has
             * https://docs.google.com/document/d/1udXoBJiWx0fCBDqvTAsG7sZMuwQ_eu6m9_paDDfAkPg/edit#heading=h.9m4iulxxqpf1
             */
            $children = $user->children()->get();
            foreach ($children as $child) {
                $citizen = "NO";
                if ($user->country == 'USA') {
                    $citizen = "USCITIZEN";
                }

                /*
                 * Send payload to apply for loan ("Create savings account")
                 * THROWS. need to catch
                 */
                // TODO catch exceptions
                $response = $usaClient->applyForLoan(
                    $user->email,
                    $user->first_name,
                    $user->last_name,
                    $user->dob,
                    $user->phone,
                    $ssn,
                    $citizen,
                    $user->street,
                    $user->street1,
                    $user->city,
                    $user->state,
                    $user->postcode
                );
                // we have a loan application with US.A
                // lets save it
                $loanApplication = new LoanApplication();
                $loanApplication->loan_number = $response['loan_number'];
                $loanApplication->loan_number_hashed = $hasher->hash($loanApplication, 'loan_number_hashed', $response['loan_number']);
                $loanApplication->loan_id = $response['loan_id'];
                $loanApplication->loan_id_hashed = $hasher->hash($loanApplication, 'loan_id_hashed', $response['loan_id']);
                $loanApplication->save();

                if ($request->hasFile('id_image') && $request->file('id_image')->isValid()) {
                    Log::info('temp file path : ' . $_FILES['id_image']['tmp_name']);

                    /*
                     * Send drivers license
                     */
                    $this->uploadID($usaClient, $loanApplication->loan_number, $_FILES['id_image']['tmp_name']);                        
                }
                else {
                    return response()->json(['code'=>'401', 'result'=>'fail', 'error'=>'undefined_file', 'request' => $request->file()]);
                }
                
                // accociate the loan application with the child
                $loanApplication->child()->save($child);
            }

            return response()->json(['code'=>201, 'result'=>'success', 'dwolla_id'=>$dwollaCustomerId, 'user'=>User::with('avatar')->findOrFail($user->id)]);
        }
    }

    /* 
     *  create ff's dwolla unverified account
     */
    public function createDwollaUnverifiedAccount(Request $request, USAClient $usaClient, DwollaWrapperCustomerClient $dwollaClient, LaraVaultHasher $hasher) {
        $data = $request->all();
        
        $validator = Validator::make($data, [  
            'first_name'=> 'required|string|max:50',
            'last_name'=> 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $user = Auth::user();
        
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            
            $user->save();

            /*
            * Create Verified Dwolla user
            * https://docs.google.com/document/d/1udXoBJiWx0fCBDqvTAsG7sZMuwQ_eu6m9_paDDfAkPg/edit#heading=h.9m4iulxxqpf1
            * THROWS
            */

            $client_ip = $request->ip();
            $dwollaCustomerId = null;
            try {
                $dwollaCustomerId = $dwollaClient->createUnverifiedCustomer(
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $client_ip,
                    null
                );
            } catch (ApiException $e) {
                // Customer already exists (checks email). this shouldn't happen outside of testing.... If it does, bad..
                $isEmailDupe = $dwollaClient::hasErrorCodes($e, 'ValidationError', 'Duplicate', "/email");
                if ($isEmailDupe) {
                    $list = $dwollaClient->lizt($user->email, 100);
                    foreach ($list->_embedded->customers as $c) {
                        if ($c->email == $user->email) {
                            $updatedCustomer = $dwollaClient->updateCustomer(
                                $c->id,
                                $user->email,
                                $client_ip);
                            $dwollaCustomerId = $updatedCustomer->_links['self']->href;
                        }
                    }
                }
                // something else went wrong besides customer already existing
                if (!$dwollaCustomerId) {
                    throw $e;
                }
            }

            // create verified dwolla customer for the parent
            // TODO check if customer actually verified in Dwolla. Webhook?
            $dwollaCustomer = DwollaUnerifiedCustomer::firstOrNew([
                'dwolla_id_hashed' => bcrypt($dwollaCustomerId)
            ]);
            $dwollaCustomer->dwolla_id = $dwollaCustomerId;
            $dwollaCustomer->save();
            // associate the verified dwolla account with the parent
            $user->dwollaCustomer()->associate($dwollaCustomer);
            $user->save();

            return response()->json(['code'=>201, 'result'=>'success', 'dwolla_id'=>$dwollaCustomerId, 'user'=>User::with('avatar')->findOrFail($user->id)]);
        }
    }

    public function setDwollaSourceAccount(Request $request) {
        $data = $request->only(['account']);
        $validator = Validator::make($data, [
            'account' => 'required|string'
        ]);

        // TODO why is this reporting on a failed email?
        if ($validator->fails()) {
            $payload = ['code'=>401, 'result'=>'fail', 'error'=>'email_exists'];
            return response()->json($payload);
        }
        else {
            $user = Auth::user();

            $hasher = new LaraVaultHasher();
            $tempAccount = new DwollaSourceAccount();
            $dwollaSourceAccount = DwollaSourceAccount::firstOrNew([
                'dwolla_id_hashed' => $hasher->hash($tempAccount, 'dwolla_id_hashed', $request->input('account'))
            ]);
            $dwollaSourceAccount->dwolla_id = $request->input('account');
            $dwollaSourceAccount->save();
            // associate the source account with the user
            $user->fundingAccounts()->save($dwollaSourceAccount);
            return response()->json(['code'=>201, 'result'=>'success']);
        }        
    }

    public function getFundingSourceAccount() {
        $user = Auth::user();
        $fundingAccounts = $user->fundingAccounts();
        return response()->json(['code'=>201, 'result'=>'success', 'accounts'=>$fundingAccounts]);
    }

    /*
     * update users's profile
     */
    public function updateProfile(Request $request) {
        $user = Auth::user();

        $data = $request->all();
        
        $validator = Validator::make($data, [  
            'first_name'=> 'string|max:50',
            'last_name'=> 'string|max:50',
            'password' => 'string|min:3',
            'avatar_id' => 'integer'
        ]);

        if ($validator->fails()) {
            $payload = ['code'=>401, 'result'=>'fail', 'error'=>'email_exists'];
            return response()->json($payload);
        }
        else {
            if ($request->has('first_name')) {
                $user->first_name = $request->input('first_name');
            }
            if ($request->has('last_name')) {
                $user->last_name = $request->input('last_name');
            }
            if ($request->has('password')) {
                $user->password = bcrypt($request->input('password'));
            }
            if ($request->has('avatar_id')) {
                $user->avatar_id = $request->input('avatar_id');
            }

            $user->save();

            $token = JWTAuth::fromUser($user);

            $payload = ['code' => '201', 'result' => 'success', 'token' => $token, 'user'=>User::with('avatar')->findOrFail($user->id)];
            return response()->json($payload);
        }
    }

    /*
     * set donation Percent
     */
    public function setDonationPercent(Request $request) {
    	$user = Auth::user();
    	$donation_percent = $request->input('donation_percent');
    	
    	$setting = Setting::where('user_id', $user->id)->first();
    	if ($setting) {
    		$setting->donation_percent = $donation_percent;
    	}
    	else {
    		$setting = new Setting;
    		$setting->user_id = $user->id;
    		$setting->donation_percent = $donation_percent;
    	}
    	
		$setting->save();

		return response()->json(['code'=>'200', 'result'=>'success', 'percent' => $donation_percent]);
    }

    /* 
     * Set notification
     */
    public function setNotification(Request $request) {

    }


    /**
     * Set device token of user
     */
    public function setDeviceToken(Request $request) {
        $input_data = $request->only(['device_token']);

        $validator = Validator::make($input_data, [
            'device_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>401, 'result'=>'fail', 'error'=>'invalid_parameters', 'validation_errors'=>$validator->errors()->all()]);
        }
        else {
            $device_token = $request->input('device_token');
            $user = Auth::user();
            $user_id = $user->id;

            if (Device::where(['user_id'=>$user_id, 'device_token'=>$device_token])->count() == 0) {
                $new_device = new Device;
                $new_device->device_token = $device_token;
                $new_device->user_id = $user_id;
                $new_device->badge = 0;
                $new_device->save();

                return response()->json(['code'=>201, 'result'=>'success']);
            }
            else {
                return response()->json(['code'=>200, 'result'=>'success', 'message'=>'already_']);
            }
        }
    }

    private function uploadID(USAClient $usaClient, $loan_number, $file_path)
    {
        // TODO test for filetypes
        /*
         * Convert Image to PDF
         */
        $uploadedImage = imagecreatefromjpeg($file_path);
        $pdf = new Imagick();
        ob_start();
        imagepng($uploadedImage);
        $image_data = ob_get_contents();
        ob_end_clean();
        $pdf->readimageblob($image_data);
        $pdf->setImageFormat('pdf');
        // need to give the application time to get into the system
        sleep(3);        
        //encode pdf and upload
        Log::info("Sending Image to US.A for loan number: $loan_number");
        Log::info(base64_encode($pdf->getImageBlob()));
        $response = $usaClient->attachDocument($loan_number, "TimsID", base64_encode($pdf->getImageBlob()));        
        //dd($response);
    }

}
