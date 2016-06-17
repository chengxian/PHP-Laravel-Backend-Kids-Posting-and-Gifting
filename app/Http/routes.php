<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Register Children resource controller
Route::resource('children', 'ChildrenController');

// Register Post resource controller
Route::resource('post', 'PostController');

// Register Comment resource controller
Route::resource('comment', 'PostController');

// Register Comment resource controller
Route::resource('gift', 'GiftController');


/*
 |-------------------------------------------------------------------------
 | Application API Routes
 |-------------------------------------------------------------------------
 */

Route::group(['prefix' => 'api', 'as' => 'api.', 'namespace' => 'api'], function() {
	/* Landiing page */
	Route::get('/', [
		'as' 	=> 'index',
		'uses' 	=> 'WelcomeController@index'
	]);

	Route::group(['prefix' => 'v1', 'namespace' => 'v1'], function() {
		/* Landing Page */
		Route::get('/', [
			'as' 	=> 'v1.index',
			'uses' 	=> 'WelcomeController@index'
		]);


		/******************* User Auth Controllers ********************/
		// Login Api
		Route::post('signin', [
			'as' 	=> 'v1.signin',
			'uses'	=> 'Auth\AuthController@authenticate',
		]);
		
		// Signup Api
		Route::post('signup', [
			'as' 	=> 'v1.signup',
			'uses'	=> 'Auth\AuthController@signup',
		]);
		// Send request betacode
		Route::post('send-request-betacode', 'Auth\AuthController@requestBetacode');
		// Send password rest request
		Route::post('send-reset-password-request', 'Auth\AuthController@sendResetPasswordRequest');


		/******************* Child Controllers ********************/
		Route::get('children', 'ChildController@index');
		Route::resource('child', 'ChildController');

		/******************* Following Children Controllers ********************/
		Route::get('following', 'FollowingController@index');
		Route::get('followings', 'FollowingController@followings');
		Route::post('follow', 'FollowingController@follow');
		Route::post('unfollow', 'FollowingController@unfollow');


		/******************* Post and Comment Controllers ********************/
		// Post Resource controller
		Route::get('posts', 'PostController@index');
		Route::resource('post', 'PostController');
		Route::resource('child.post', 'ChildPostController');
		Route::resource('post.comment', 'PostCommentController');
		// Like Post Controller
		Route::post('like', 'LikeController@like');


		/******************* Dwolla Account Controllers ********************/
		Route::post('verified-account', 'UserController@createDwollaVerifiedAccount');
		Route::post('unverified-account', 'UserController@createDwollaUnverifiedAccount');
		Route::post('source-account', 'UserController@setDwollaSourceAccount');

		Route::get('funding-sources', 'UserController@getFundingSourceAccount');


		/******************* Gift and Contributions Controllers ********************/
		Route::get('gifts', 'ContributionController@gifts');
		Route::get('gifts/{child_id}', 'ContributionController@giftsByChild');
		Route::post('give-gift', 'ContributionController@giveGift');
		
		/******************* Transaction Controllers ********************/
		Route::get('balances/{child_id}', 'TransactionController@balances');

		/******************* Invite Controllers ********************/
		Route::resource('invite', 'InviteController');


		/******************* Notification Controllers ********************/
		Route::get('notifications', 'NotificationController@index');
		Route::get('notifications/{child_id}', 'NotificationController@getByChild');


		/******************* Media Controllers ********************/
		// Upload avatar
		Route::post('upload-avatar', 'MediaController@uploadAvatar');
		// Upload ID card
		// Route::post('upload-id', 'MediaController@uploadID');
		// Upload Post attachment
		Route::post('upload-attachment', 'MediaController@uploadAttachment');



		/******************* Uesr Settings Controllers ********************/
		// Set donation percent
		Route::post('profile', 'UserController@updateProfile');
		// Set donation percent
		Route::post('set-donation-percent', 'UserController@setDonationPercent');
		// Set device token
		Route::post('set-device-token', 'UserController@setDeviceToken');
	});
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');

    Route::get('/invite-user', ['as' => 'invite-user', 'uses' => 'InviteController@index']);

    Route::post('/invite-user', 'InviteController@send');

	Route::get('/re-invite-user', ['as' => 're-invite-user', 'uses' => 'InviteController@reIndex']);

	Route::post('/re-invite-user', 'InviteController@reSend');

	/*
	|--------------------------------------------------------------------------
	| Native App Redirects
	|--------------------------------------------------------------------------
	|
	| redirects from web (to account for gmail issues) to Kidgifting://kidgifting/
	|
	*/
	Route::group(['prefix' => 'app', 'as' => 'app.', 'namespace' => 'app'], function() {
		Route::group(['prefix' => 'redirect', 'as' => 'redirect.'], function() {
			Route::get('betacode/{betacode}', [
				'as'	=> 'betacode',
				'uses' 	=> 'RedirectController@betacode'
			]);
		});

		/**
		 * may not need, penciling in for now
		 */
		Route::get('invitedby/{invited_by_uuid}', [
			'as'	=> 'invitedby',
			'uses' 	=> 'InvitedByController@fbinvite'
		]);
	});

});
