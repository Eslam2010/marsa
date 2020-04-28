<?php

namespace App\Http\Controllers\api;

use App\customer;
use App\marsa;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class usercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
     public function updatePassword(Request $request,$id)
    {
    //     $input = $request->all();

    //     $validator = Validator::make($input,[
    //         'oldPassword'=>'required',
    //         'password'=>'required|min:11'
    //     ]);

    //   if ($validator->fails()) {
    //         # code...
    //       return response()->json($validator->errors());
    //     }
        
    //     $hashedPassword = Auth::user()->password;
       

    //     if (Hash::check($request->oldPassword,$hashedPassword))
    //     {
    //         $user = User::find(Auth::id());
    //         $user->password = Hash::make($request->password);
    //         $user->save();
    //         //Auth::logout(); 
    //              return response()->json([
    //         'message' => 'Password is changed Successfully'],
    //         200);
           
    //     }else {
    //         return response()->json([
    //           'message' => 'Old Password is Not Valid'],
    //         401);
           
            
            
    //     }
    
    $input = $request->all();

        $user = User::find($id);
       
        
          if(!is_null($request->input('password'))) 
          {
             

                      $user->update([
                          'name'=>$request->input('name'),
                          'phone'=>$request->input('phone'),
                          'password'=>Hash::make($request->input('password')),
                      ]);
                  
                       return response()->json([
                    'message' => 'user updated successfully',
                    'user' => $user],
                    200);
          }
           
          
                      $user->update([
                          'name'=>$request->input('name'),
                          'phone'=>$request->input('phone'),
                      ]);
                      
                    return response()->json([
                    'message' => 'user updated successfully',
                    'user' => $user],
                    200);
    }
     
    
     public function getAuthUser(Request $request) {
        try {
            
            $token = JWTAuth::getToken();

            if (!$user = JWTAuth::toUser($token)) {
                return response()->json(['code' => 404, 'message' => 'user_not_found']);
            } else {

                $user = JWTAuth::toUser($token);
                return response()->json(['code' => 200, 'data' => ['user' => $user]]);
            }
        } catch (Exception $e) {

            return response()->json(['code' => 404, 'message' => 'Something went wrong']);

        }
    }
    
     
     
    public function update(Request $request, $id)
    {
        $input = $request->all();
       $userRole = User::find(Auth::user()->id);
      
        $user = User::find($id);
        
        
        
         if($userRole->role=='user'){
             
                 if($this->Check($request->input('currentPassword')))
                  {
                      $user->update([
                          'name'=>$request->input('name'),
                          'phone'=>$request->input('phone'),
                          'role'=>$request->input('role'),
                      ]);
                      if(!is_null($request->input('newPassword')))
                          {
                              $user->password = Hash::make($request->input('newPassword'));
                          }
                       return response()->json([
                    'message' => 'user updated successfully',
                    'user' => $user],
                    200);
                  }
             
         }
         
          else if($userRole->role=='admin')
          {
              $user->update([
                  'name'=>$request->input('name'),
                  'phone'=>$request->input('phone'),
                  'role'=>$request->input('role'),
              ]);
              if(!is_null($request->input('newPassword')))
                  {
                      $user->password = Hash::make($request->input('newPassword'));
                  }
                
               return response()->json([
            'message' => 'user updated successfully',
            'user' => $user],
            200);
          }
          else
          {
             return response()->json([
            'message' => 'please enter correct password'],
            401);
          }
     
    }
   
   
   
   public function updateUserData(Request $request, $id)
    {
        $input = $request->all();

        $user = User::find($id);
        return $user;
        
          if(!is_null($request->input('password'))) 
          {
             

                      $user->update([
                          'name'=>$request->input('name'),
                          'phone'=>$request->input('phone'),
                          'password'=>Hash::make($request->input('password')),
                      ]);
                  
                       return response()->json([
                    'message' => 'user updated successfully',
                    'user' => $user],
                    200);
          }
           
          
                      $user->update([
                          'name'=>$request->input('name'),
                          'phone'=>$request->input('phone'),
                      ]);
                      
                    return response()->json([
                    'message' => 'user updated successfully',
                    'user' => $user],
                    200);
             
   }
         
          

       /* if(!is_null($request->input('password')))
        {
            $input['password']=Hash::make($request->input('password'));

        }*/


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if(!$user)
        {
            return response()->json([
                'error' => 'user not found',
            ],
                400);
        }
        $user->delete();

        return response()->json([
            'message' => 'user deleted successfully',
        ],
            200);
    }
    
    
    protected function Check($password)
    {
        if(Hash::check($password,Auth::user()->getAuthPassword()))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
