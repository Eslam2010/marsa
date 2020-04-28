<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customer extends Model
{

    protected $guarded = [];
	public function contracts()
    {
        return $this->hasMany(necklase::class,'customerid');

    }//end
    //
}
