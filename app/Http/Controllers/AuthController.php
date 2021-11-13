<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Laravel\Passport\Token;

use App\Models\User;

/**
 * Functions for publically available endpoints for authentication
 */

class AuthController extends Controller
{
    /**
	 * User Login
	 * @param string email
	 * @param string password
	 * @return array
	 */
	public function login(Request $request) {
		// Validator
		$validator = Validator::make($request->all(), [
			'email' => 'required',
			'password' => 'required'
		]);
		if ($validator->fails()) {
			return [
				'success' => false,
				'message' => 'Login info is not correct'
			];
		}

		$email = $request->get('email');
		$password = $request->get('password');

		$user = User::where('email', $email)->first();
		if (!$user) {
			return [
				'success' => false,
				'message' => 'Email does not exist'
			];
		}

		if (!Hash::check($password, $user->password)) {
			return [
				'success' => false,
				'message' => 'Password is not correct'
			];
		}

		Token::where([
			'user_id' => $user->id,
			'name' => 'API Access Token'
		])->delete();
		$tokenResult = $user->createToken('API Access Token');
		
		$user->accessTokenAPI = $tokenResult->accessToken;

		return [
			'success' => true,
			'user' => $user
		];
	}

	/**
	 * User Registration
	 * @param string name
	 * @param string email
	 * @param enum(buyer,seller) type
	 * @param string password
	 * @return array
	 */
	public function register(Request $request) {
		// Validator
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'type' => 'required',
			'password' => 'required'
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'message' => 'Provide all the necessary information'
			];
		}

		$user= User::where('email', $request->email)->first();
		if ($user) {
			return [
				'success' => false,
				'message' => 'The email is already in use'
			];
		}

		$code = Str::random(6);
		$type = $request->type;
		if ($type == 'buyer')
			$type = 'buyer';
		// User
		$user = new User;
		$user->name = $request->name;
		$user->email = $request->email;
        $user->telegram = $request->telegram;
		$user->role = 'user';
		$user->type = $type;
		$user->confirmation_code = $code;
		$user->password = Hash::make($request->password);
		$user->save();
		$user->assignRole($user->role);

        Auth::login($user);
        $tokenResult = $user->createToken('API Access Token');		
		$user->accessTokenAPI = $tokenResult->accessToken;

		$link = $request->header('origin') . '/invitation/' . Helper::b_encode($user->id . '::' . $email . '::' . $code);
		Mail::to($user)->send(new Invitation($link, $email));

		return ['success' => true, 'user' => $user];
	}
}
