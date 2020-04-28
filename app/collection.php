<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class collection extends Model
{
	public function necklases()
    {
        return $this->belongsTo(necklase::class);

    }//end 
    //
}
