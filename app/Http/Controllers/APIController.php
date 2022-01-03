<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use App\Http\Helper;

use App\Models\User;
use App\Models\Offer;
use App\Models\BuyOffer;
use App\Models\Batch;
use App\Mail\Invitation;

use App\Jobs\NotificationJob;

use Laravel\Passport\Token;
use Carbon\Carbon;
use Log;

/**
 * Functions for initial publically accessible endpoints and 'getMe' for catching users with valid logged-in session tokens.
 */

class APIController extends Controller
{
	/**
	 * Email testing function
	 * @return array
	 */
	public function testEmail(Request $request) {
		NotificationJob::dispatch('zohaib055@gmail.com', '', '', '');

		$url = env('APP_URL');
		$logo = $url . 'email-logo.png';
		$headline = 'Your KYC information is needed for compliance';
		$content = '<b>Hi Name,</b><br/><br/>Your relationship with the Casper\'s associated programs requires you to submit your information in our KYC portal. This process is required before payments or further business can occur. Please click the button below to begin, and save this email for accessing the portal again later';
		$button = '<a href="' . $url . '">Enter KYC Portal</a>';

		return view('emails.notification', [
			'logo' => $logo,
			'headline' => $headline,
			'content' => $content,
			'button' => $button
		]);
	}

	/**
	 * Install path '/install'. You can turn this off by toggling INSTALL_PATH_ENABLED to 0 in .env
	 * @return string
	 */
	public function install(Request $request) {
		if(
			(int)env('INSTALL_PATH_ENABLED') == 1
		) {
			Log::info(":::: Install started ::::");
			User::where([])->delete();
			Offer::where([])->delete();
			BuyOffer::where([])->delete();
			Batch::where([])->delete();
			// Setting Roles
			$role = Role::where(['name' => 'admin'])->first();
			if (!$role) Role::create(['name' => 'admin']);

			$role = Role::where(['name' => 'user'])->first();
			if (!$role) Role::create(['name' => 'user']);

			echo "Roles created!<br/>";

			// Setting Users

			/* First Admin */
			$email = 'ledgerleapllc@gmail.com';
			$user = User::where('email', $email)->first();
			if (!$user) {
				$user = new User;
				$user->name = 'Ledger Leap';
				$user->email = $email;
				$user->telegram = '@ledgerleap';
				$user->role = 'admin';
				$user->email_verified_at = Carbon::now();
				$user->email_verified = 1;
				$new_pw = Str::random(10);
				Log::info('Email: '. $email);
				Log::info('Password: '. $new_pw);
				$user->password = Hash::make($new_pw);
				$user->confirmation_code = 'admin';
				$user->save();
			}

			if (!$user->hasRole('admin')) $user->assignRole('admin');

			echo "Users created!<br/>";
		}
	}

	/**
	 * Get user if logged in with a valid session token
	 * @return array
	 */
	public function getMe(Request $request) {
		$user = Auth::user();
		
		return [
			'success' => true,
			'me' => $user
		];		
		
	}

	/**
	 * Reset password of a user by sending reset email
	 * @param string email
	 * @return array
	 */
	public function sendResetEmail(Request $request) {
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
		]);

		if ($validator->fails()) return ['success' => false];

		$email = $request->get('email');
		$user = User::where('email', $email)->first();

		if (!$user) {
			return [
				'success' => false,
				'message' => 'Email is not valid'
			];
		}

		// Clear Tokens
		DB::table('password_resets')
			->where('email', $email)
			->delete();

		// Generate New One
		$token = Str::random(60);
		DB::table('password_resets')->insert([
			'email' => $email,
			'token' => Hash::make($token),
			'created_at' => Carbon::now()
		]);

		$resetUrl = $request->header('origin') . '/password/reset/' . $token . '?email=' . urlencode($email);
		Mail::to($user)->send(new ResetPasswordLink($resetUrl));

		return ['success' => true];
	}

	/**
	 * Reset Password
	 * @param string email
	 * @param string password
	 * @param string token
	 * @return array
	 */
	public function resetPassword(Request $request) {
		// Validator
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required',
			'token' => 'required'
		]);

		if ($validator->fails()) return ['success' => false];
		
		$email = $request->get('email');
		$password = $request->get('password');
		$token = $request->get('token');

		// Token Check
		$temp = DB::table('password_resets')
			->where('email', $email)
			->first();
		if (!$temp) return ['success' => false];
		if (!Hash::check($token, $temp->token)) return ['success' => false];

		// User Check
		$user = User::where('email', $email)->first();

		if (!$user) {
			return [
				'success' => false,
				'message' => 'Invalid user'
			];
		}

		$user->password = Hash::make($password);
		$user->save();

		// Clear Tokens
		DB::table('password_resets')
			->where('email', $email)
			->delete();

		return ['success' => true];
	}
}
