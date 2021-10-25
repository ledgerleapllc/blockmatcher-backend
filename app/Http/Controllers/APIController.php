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

class APIController extends Controller
{
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

	// Install
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

	// Get Auth User
	public function getMe(Request $request) {
		$user = Auth::user();
		
		return [
			'success' => true,
			'me' => $user
		];		
		
	}
}
