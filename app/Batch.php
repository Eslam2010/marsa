<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $guarded = [];

    public function necklace(){
        return $this->belongsTo(necklase::class,'necklaceid');
    }
}
