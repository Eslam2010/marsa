<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class UsersLoggedIn extends  Model
{

    protected $table = 'users_logged_ins';


   // protected $guarded = [];

    protected $fillable = [
        'user_id','phone'
    ];
}
