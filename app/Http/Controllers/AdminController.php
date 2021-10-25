<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use Str;

use App\Models\User;
use App\Models\Offer;
use App\Models\BuyOffer;
use App\Models\Batch;
use Log;

class AdminController extends Controller
{
	public function getSellOffersList(Request $request) {
		$total = 0;

		$perPage = 10;
		$sort_key = 'offers.id';
		$sort_direction = 'desc';
		$page_id = 1;
		$filter = 0;
		$hideLocked = false;

        $user = Auth::user();
       
		$data = $request->all();
		extract($data);

		$page_id = (int) $page_id;
		$perPage = (int) $perPage;
		$sort_key = trim($sort_key);
		$sort_direction = trim($sort_direction);
		$filter = (int) $filter;
		$hideLocked = (bool) $hideLocked;
		
		if ($page_id < 1) $page_id = 1;
			$start = ($page_id - 1) * $perPage;
        
		$whereClause = [];
		if ($filter == 1)
			$whereClause = ['is_batch' => 1];
		if ($filter == 2)
			$whereClause = ['is_batch' => 0];

		$whereLocked = [];
		if ($hideLocked) 
			$whereLocked = ['unlocked' => 1];


        $total = Offer::where($whereClause)
						->where($whereLocked)
						->count();

        $offer_list = Offer::join('users', 'users.id', 'offers.user_id')
						->with('user')
						->where($whereClause)
						->where($whereLocked)
						->select(['offers.*'])
                        ->orderBy($sort_key, $sort_direction)
                        ->offset($start)
                        ->limit($perPage)
                        ->get();
        
        return [
			'success' => true,
			'offer_list' => $offer_list,
			'total' => $total,
        ];
	}

	public function getBuyOffersList(Request $request) {
		$total = 0;

		$perPage = 10;
		$sort_key = 'buy_offers.id';
		$sort_direction = 'desc';
		$page_id = 1;
		$filter = 0;

        $user = Auth::user();
       
		$data = $request->all();
		extract($data);

		$page_id = (int) $page_id;
		$perPage = (int) $perPage;
		$sort_key = trim($sort_key);
		$sort_direction = trim($sort_direction);
		$filter = (int) $filter;
		
		if ($page_id < 1) $page_id = 1;
			$start = ($page_id - 1) * $perPage;
        
		$whereClause = [];
		if ($filter == 1)
			$whereClause = ['is_batch' => 1];
		if ($filter == 2)
			$whereClause = ['is_batch' => 0];

		$total = BuyOffer::where($whereClause)
						->count();

        $offer_list = BuyOffer::join('users', 'users.id', 'buy_offers.user_id')
						->with('user')
						->where($whereClause)
						->select(['buy_offers.*'])
                        ->orderBy($sort_key, $sort_direction)
                        ->offset($start)
                        ->limit($perPage)
                        ->get();
        
        return [
			'success' => true,
			'offer_list' => $offer_list,
			'total' => $total,
        ];
	}

	public function getBatchesList(Request $request) {
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
        
        $total = Batch::where([])->count();

        $batch_list = Batch::where([])
                        ->orderBy($sort_key, $sort_direction)
                        ->offset($start)
                        ->limit($perPage)
                        ->get();
        
        return [
			'success' => true,
			'batch_list' => $batch_list,
			'total' => $total,
        ];
	}

	public function createBatch(Request $request) {
		
		$validator = Validator::make($request->all(), [
			'notes' => 'required',
			'price' => 'required',
			'checks' => 'required',
			'buyChecks' => 'required',
		]);
		if ($validator->fails()) {
			return [
				'success' => false,
				'message' => 'Provide all the necessary information'
			];
		}

		$price = $request->price;
		$notes = $request->notes;
		$checks = $request->checks;
		$buyChecks = $request->buyChecks;
		if (count($checks) == 0)
			return ['success' => false];

		$total_cspr = Offer::whereIn('id', $checks)->sum('amount');	
		$total_price = $price * $total_cspr;	

		$batch = new Batch;
		$batch->price = $price;
		$batch->notes = $notes;
		$batch->total_cspr = $total_cspr;
		$batch->total_price = $total_price;
		$batch->save();
		
		Offer::whereIn('id', $checks)
				->update([
					'is_batch' => 1, 
					"batch_id" => $batch->id]
				);

		BuyOffer::whereIn('id', $buyChecks)
				->update([
					'is_batch' => 1, 
					"batch_id" => $batch->id]
				);		

		return ['success' => true];
	}

	public function updateBatch(Request $request) {
		$validator = Validator::make($request->all(), [
			'notes' => 'required'
		]);
		if ($validator->fails()) {
			return [
				'success' => false,
				'message' => 'Provide all the necessary information'
			];
		}

		$batch = Batch::find($request->id);
		if (isset($batch)) {
			$batch->notes = $request->notes;
			$batch->save();
		}

		return ['success' => true];
  
	}

	public function removeBatch(Request $request) {
		$id = $request->id;
		Batch::find($id)->delete();
		Offer::where("batch_id", $id)->update(["batch_id" => null, "is_batch" => 0]);	
		BuyOffer::where("batch_id", $id)->update(["batch_id" => null, "is_batch" => 0]);		
		
		return ['success' => true];
	}

	public function getBatchDetail(Request $request) {
		$batch_id = (int) $request->id;
		if ($batch_id == 0) 
			return ['success' => false];

		$batch = Batch::find($batch_id);

		return [
			'success' => true,
			'batch' => $batch,
		];		
		
	}

	public function getBatchSellOffersList(Request $request) {
		$batch_id = (int) $request->id;
		if ($batch_id == 0) 
			return ['success' => false];

		$perPage = 10;
		$sort_key = 'id';
		$sort_direction = 'desc';
		$page_id = 1;

		$data = $request->all();
		extract($data);

		$page_id = (int) $page_id;
		$perPage = (int) $perPage;
		$sort_key = trim($sort_key);
		$sort_direction = trim($sort_direction);
		if ($page_id < 1) $page_id = 1;
		$start = ($page_id - 1) * $perPage;

		$total = Offer::where('batch_id', $batch_id)->count();
		$offers = Offer::join('users', 'users.id', 'offers.user_id')
						->with('user')
						->where('batch_id', $batch_id)
						->select(['offers.*'])
						->orderBy($sort_key, $sort_direction)
                        ->offset($start)
                        ->limit($perPage)
                        ->get();

		return [
			'success' => true,
			'offer_list' => $offers,
			'total' => $total,
		];	
	}

	public function getBatchBuyOffersList(Request $request) {
		$batch_id = (int) $request->id;
		if ($batch_id == 0) 
			return ['success' => false];

		$perPage = 10;
		$sort_key = 'id';
		$sort_direction = 'desc';
		$page_id = 1;

		$data = $request->all();
		extract($data);

		$page_id = (int) $page_id;
		$perPage = (int) $perPage;
		$sort_key = trim($sort_key);
		$sort_direction = trim($sort_direction);
		if ($page_id < 1) $page_id = 1;
		$start = ($page_id - 1) * $perPage;

		$total = BuyOffer::where('batch_id', $batch_id)->count();
		$offers = BuyOffer::join('users', 'users.id', 'buy_offers.user_id')
						->with('user')
						->where('batch_id', $batch_id)
						->select(['buy_offers.*'])
						->orderBy($sort_key, $sort_direction)
                        ->offset($start)
                        ->limit($perPage)
                        ->get();

		return [
			'success' => true,
			'offer_list' => $offers,
			'total' => $total,
		];	
	}

	public function exportCSV() {
		$offers = Offer::with('user')->where([])->get();
		$csv = "Req #,Request Date,Name,Email,Telegram,Amount For Sale,Desired Price,Unlocked,Batch\n";
		foreach ($offers as $offer) {
			$row = '';
			$row .= $offer->id.',';
			$row .= $offer->created_at.',';
			$row .= $offer->user->name.',';
			$row .= $offer->user->email.',';
			$row .= $offer->user->telegram.',';
			$row .= $offer->amount.',';
			$row .= $offer->desired_price.',';
			$row .= ($offer->unlocked == 1 ? 'Yes' : 'No').',';
			$row .= ($offer->is_batch == 1 ? $offer->batch_id : '')."\n";

			$csv .= $row;
		}

		return [
			'success' => true,
			'csv' => $csv
		];

	}

	public function detailExportCSV(Request $request) {
		$offers = Offer::with('user')
						->where('batch_id', $request->id)
						->get();

		$csv = "Req #,Request Date,Name,Email,Telegram,CSPR Amount,Unlocked\n";
		foreach ($offers as $offer) {
			$row = '';
			$row .= $offer->id.',';
			$row .= $offer->created_at.',';
			$row .= $offer->user->name.',';
			$row .= $offer->user->email.',';
			$row .= $offer->user->telegram.',';
			$row .= $offer->amount.',';
			$row .= ($offer->unlocked == 1 ? 'Yes' : 'No')."\n";

			$csv .= $row;
		}

		return [
			'success' => true,
			'csv' => $csv
		];
	}


}
