<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marsa extends Model
{
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);

    }//end
    //
}
