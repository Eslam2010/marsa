<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class necklase extends Model
{
    protected $guarded = [];

    public function collections()
    {
        return $this->hasmany(collection::class);

    }

    public function customer()
    {
        return $this->belongsTo('App\customer','customerid');

    }


     public function marsas()
    {
        return $this->belongsTo(marsa::class);

    }

     public function user()
    {
        return $this->belongsTo('App\User','userid');
    }
    
     public function batches()
    {
        return $this->hasMany(Batch::class,'necklaceid');
    }
  
}
