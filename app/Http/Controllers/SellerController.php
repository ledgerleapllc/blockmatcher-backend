<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\User;
use App\Models\Offer;

class SellerController extends Controller
{
    //Get CSPR Offers List
    public function getOffersList(Request $request) {
        $total = 0;

		$perPage = 10;
		$sort_key = 'id';
		$sort_direction = 'desc';
		$page_id = 1;

        $user = Auth::user();
       
		$data = $request->all();
		extract($data);

		$page_id = (int) $page_id;
		$perPage = (int) $perPage;
		$sort_key = trim($sort_key);
		$sort_direction = trim($sort_direction);
		
		if ($page_id < 1) $page_id = 1;
			$start = ($page_id - 1) * $perPage;
        
        $total = Offer::where('user_id', $user->id)->count();

        $offer_list = Offer::where('user_id', $user->id)
                        ->orderBy($sort_key, $sort_direction)
                        ->offset($start)
                        ->limit($perPage)
                        ->get();
        
        $total_cspr = Offer::where('user_id', $user->id)->sum('amount');
        $total_revenue = Offer::where('user_id', $user->id)->sum(\DB::raw('amount * desired_price')); 

        return [
			'success' => true,
			'offer_list' => $offer_list,
			'total' => $total,
            'total_cspr' => $total_cspr,
            'total_revenue' => $total_revenue
		];
    }

    // Register CSPR Batch for sale
    public function createOffer(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'unlocked' => 'required',
            'where_held' => 'required',
            'desired_price' => 'required',
        ]);
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Provide all the necessary information'
            ];
        }

        $user = Auth::user();

        $offer = new Offer;
        $offer->user_id = $user->id;
        $offer->amount = $request->amount;
        $offer->unlocked = $request->unlocked;
        $offer->where_held = $request->where_held;
        $offer->desired_price = $request->desired_price;
        $offer->save();

        return ['success' => true];
    }

    public function removeOffer(Request $request) {
        $id = $request->id;
        $user = Auth::user();
        $offer = Offer::find($id);
        
        if ($offer->user_id == $user->id)
            $offer->delete();

        return ['success' => true];
    }
}
