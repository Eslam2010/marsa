<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\User;
use JWTFactory;
use JWTAuth;
use Validator;
use Response;
class apiregister extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            # code...
            return response()->json($validator->errors());

        }

        $user=User::create([
            'name' => $request->get('name'),
            'phone' => $request->get('phone'),
            'password' => bcrypt($request->get('password')),
            'role'=> $request->get('role'),
        ]);
     /*   $user = User::first();
        $token = JWTAuth::fromUser($user);*/
    
       
       
        return Response::json([
            'message'=>'user created successfully ',
            'user'=>$user
        ],200);
        //
    }
}
