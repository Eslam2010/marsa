<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use JWTFactory;
use JWTAuth;
use Validator;
use App\UsersLoggedIn;


class apilogin extends Controller
{
	public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required|max:13|min:10',
            'password' => 'required'
        ]);

  
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
         $user = User::where('phone','=',$request->phone)->first();


        $data = $this->checkLogIn($request,$user->id);
        
        if ($data) {
          
            $token  =JWTAuth::fromUser($user);
            return response()->json([
                'message' => 'oh sorry sir this account is in use Please Logout To be Login again and thank you sir','token'=>$token],
                200);

        } else {
            $credentials = $request->only('phone', 'password');
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    # code...
                    return response()->json(['error' => 'invalid phone and password'], 401);
                }
            } catch (\JWTException $e) {

                return response()->json(['error' => 'could not create token'], 500);
            }


                session(['token'=>$token]);
                
                

            return response()->json(['message' => 'login successfully', 'Token' => $token,'role'=>$user->role], 200);
        }
    }



    protected function checkLogIn(Request $request,$id)
    {

        $data = UsersLoggedIn::where('user_id', '=', $id)->first();
        if ((is_null($data)))
        {
            $userData = User::find($id);
            $user = UsersLoggedIn::create([
               'phone'=>$userData->phone,
                'user_id'=>$userData->id,
            ]);

            return false;
        }
        else
        {
            return true;
        }

    }

    public function userLogout(Request $request)
    {
           $id = Auth::user()->id;
            $data = UsersLoggedIn::where('user_id', '=', $id)->first();
            if(!is_null($data)) {
                $data->delete();
                Auth::logout();
                return response()->json(['message' => 'logout successfully'], 200);
            }
            else
            {
                return response()->json(['message' => 'you already logged out sir'], 200);

            }
    }
    
    
}
