<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Log;
use App\Models\User;
use App\Models\BuyOffer;

class BuyerController extends Controller
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
        
        $total = BuyOffer::where('user_id', $user->id)->count();

        $offer_list = BuyOffer::where('user_id', $user->id)
                        ->orderBy($sort_key, $sort_direction)
                        ->offset($start)
                        ->limit($perPage)
                        ->get();
        
        $total_cspr = BuyOffer::where('user_id', $user->id)->sum('amount');
        $total_revenue = BuyOffer::where('user_id', $user->id)->sum(\DB::raw('amount * desired_price')); 

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
            'type' => 'required',
        ]);
        if (
            $validator->fails() ||
            (
                $request->type == 0 && (
                    !isset($request->desired_price) || 
                    empty($request->desired_price)
                )
            ) || (
                $request->type == 1 && (
                    !isset($request->discount) || 
                    empty($request->discount)
                )
            )             
        ) {
            return [
                'success' => false,
                'message' => 'Provide all the necessary information'
            ];
        }

        $user = Auth::user();

        $offer = new BuyOffer;
        $offer->user_id = $user->id;
        $offer->amount = $request->amount;
        $offer->type = $request->type;
        $offer->discount = $request->discount;
        $offer->desired_price = $request->desired_price;
        $offer->save();

        return ['success' => true];
    }

    public function updateOffer(Request $request) {
        $id = $request->id;
        $user = Auth::user();
        $offer = BuyOffer::find($id);

        Log::info(json_encode($request->all()));
        if ($offer->user_id == $user->id) {
            if ( isset($request->price) && !empty($request->price))
                $offer->desired_price = $request->price;
            
            if ( isset($request->discount) && !empty($request->discount))
                $offer->discount = $request->discount;
            
            if ( isset($request->amount) && !empty($request->amount))
                $offer->amount = $request->amount;
            
            $offer->save();
        }

        return ['success' => true];
    }

    public function removeOffer(Request $request) {
        $id = $request->id;
        $user = Auth::user();
        $offer = BuyOffer::find($id);
        
        if ($offer->user_id == $user->id || $user->role == 'admin')
            $offer->delete();

        return ['success' => true];
    }
}
