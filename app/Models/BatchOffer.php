<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchOffer extends Model
{
    protected $guarded = [];

    public function batch() {
        return $this->belongsTo('App\Models\Batch');
    }

    public function offer() {
        return $this->belongsTo('App\Models\Offer');
    }

    
}
