<?php

namespace App\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\User;
use App\Profile;

use App\Jobs\NotificationJob;

class Helper {
	// Check Individuals
	public static function checkIndividuals($user, $request) {
		if ($user && isset($user->individuals)) {
			$individuals = $user->individuals;

			$fullySubmitted = 0;
			if ($individuals && count($individuals)) {
				foreach ($individuals as $individual) {
					if ($individual->profile->status == "fully submitted") $fullySubmitted++;
				}
			}

			if ($fullySubmitted == count($individuals) && $fullySubmitted != 0) {
				$user->profile->status = "fully submitted";
				$user->profile->save();
				
				$headline = "All owners/decision makers have submitted their information!";
				$content = "<b>All users have submitted their KYC/AML info,</b><br/><br/>The admin will review the KYC details for each user. All owners and decision makers must be approved for your entity to do business. A further email will be sent once all users have been reviewed.";
				$url = $request->header('origin') . '/invitation/' . self::b_encode($user->id . '::' . $user->email . '::' . $user->confirmation_code);
				$button = '<a href="' . $url . '">Enter KYC Portal</a>';

				NotificationJob::dispatch($user, $headline, $content, $button);
			}
		}
	}

	// Check Parent User
	public static function checkParentUser($mainUser, $request) {
		if ($mainUser && $mainUser->tree) {
			$ids = explode("-", $mainUser->tree);
			if ($ids && count($ids) > 0) {
				$ids = array_reverse($ids);

				foreach ($ids as $id) {
					$id = (int) $id;
					$user = self::getUser($id, true);
					self::checkIndividuals($user, $request);
				}
			}
		}
	}

	// Get User
	public static function getUser($userId, $returnInfo = false, $fullLevel = false) {
		// Refresh User
		$user = User::with([
									'profile',
									'shuftipro',
									'shuftiproTemp'
								])
								->has('profile')
								->where('id', $userId)
								->first();

		// Get Individuals
		if ($returnInfo) {
			$invited_by = (int) $user->invited_by;
			$parent = null;

			if ($invited_by) {
				$parent = User::with([
										'profile',
										'shuftipro',
										'shuftiproTemp'
									])
									->has('profile')
									->where('id', $invited_by)
									->first();
				$user->parent = $parent;
				if ($parent->invited_by) {
					$user->parent->parent = User::find($parent->invited_by);	
				}
			}

			if ($fullLevel) {
				// Full Level
				$individuals = User::with('profile')
														->has('profile')
														->where(function ($query) use ($userId) {
															$query->where('invited_by', $userId)
																		->orWhere('tree', 'like', $userId . '-%');
														})
														->get();
				$user->individuals = $individuals;
			} else {
				// Not Full Level
				$individuals = User::with('profile')
														->has('profile')
														->where('invited_by', $userId)
														->get();
				$user->individuals = $individuals;
			}
		}

		return $user;
	}

	// Generate GUID
	public static function generateGUID() {
		$byte1 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte2 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte3 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte4 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte5 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte6 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte7 = "12";
		$byte8 = "d3";
		$byte9 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte10 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte11 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte12 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte13 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte14 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte15 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
		$byte16 = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);

		$guid = $byte1 . $byte2 . $byte3 . $byte4 . '-' . $byte5 . $byte6 . '-' . $byte7 . $byte8 . '-' . $byte9 . $byte10 . '-' . $byte11 . $byte12 . $byte13 . $byte14 . $byte15 . $byte16;

		return $guid;
	}
	
	// Encode
	public static function b_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	// Decode
	public static function b_decode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
}
?>
