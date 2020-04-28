<?php

use Illuminate\Http\Request;
use App\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::get('/', function (Request $request) {
    return "API V 0.1.1";
});



Route::post('user/login', 'apilogin@login');


Route::group( ['middleware' => [ 'jwt.auth','cors']],  function(){
    
   
    Route::put('user/changePassword/{id}','api\usercontroller@updatePassword');
    Route::get('user/getUserData','api\usercontroller@getAuthUser');
    Route::put('user/updateUser/{id}','api\usercontroller@updateUserData');
     Route::post('user/register', 'apiregister@register');
    Route::get('user/logout', 'apilogin@userLogout');
    Route::resource('user','api\usercontroller');
    Route::resource('batch', 'api\batchController');
    Route::resource('customer', 'api\customercontroller') ;
    Route::resource('marsa', 'api\marsacontroller') ;
    Route::resource('contract', 'api\contractcontroller') ;
    Route::get('/users', function (Request $request) {
        $users = User::all();
         return response()->json([
            'message' => 'Users viewed successfully',
            'users' => $users
        ],200);
    });
    
    Route::get('/getBatchData/{id}', 'api\batchController@getBatch_ById');
    Route::get('/batchesOfContract/{id}', 'api\batchController@getBatchesByContractId');
    Route::get('/contractsOfCustomer/{id}', 'api\contractcontroller@getContractsByCustomerId');
    Route::get('/contract/pdf/{id}', 'api\contractcontroller@showPdf');
    
    Route::get('/contract/pdf/{id}', 'api\contractcontroller@showPdf');
    Route::get('/batch/pdf/{id}', 'api\batchController@showPdf');
} );
