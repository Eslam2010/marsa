<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use  Validator ;
use App\marsa;

class marsacontroller extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marsas = marsa::all();
        return response()->json([
            'message' => 'Marsas viewed successfully',
            'marsas' => $marsas],
            200);
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
         $input = $request->all();
    $validator =    Validator::make($input, [
    'name'=> 'required',
    'numberof'=> 'required',
    'numbervat'=> 'required',
    'location'=> 'required',
    'space'=> 'required',
    ] );

    if ($validator -> fails()) {
        # code...
        return $this->sendError('error validation', $validator->errors());
    }

    $marsa = marsa::create($input);
        return response()->json([
            'message' => 'marsa created successfully',
            'marsa' => $marsa],
            200);        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marsa = marsa::find($id);
        if (is_null($marsa)) {
            # code...
            return $this->sendError('marsa not found ! ');
        }
        return response()->json([
            'message' => 'marsa Viewed successfully',
            'marsa' => $marsa],
            200);
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
    public function update(Request $request, $id)
    {
     $input = $request->all();

    $marsa = marsa::find($id);
 
    $marsa->update([
        'name'=> $input['name'],
        'numberof'=>$input['numberof'],
        'numbervat'=>$input['numbervat'],
        'location'=>$input['location'],
        'space'=>$input['space'],
        'taxValue'=>$input['taxValue']
        ]);
        return response()->json([
            'message' => 'marsa updated successfully',
            'marsa' => $marsa],
            200);
            
            
            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marsa = marsa::find($id);

        if(!$marsa)
        {
            return response()->json([
                'error' => 'marsa not found',
            ],
                400);
        }
         $marsa->delete();

        return response()->json([
            'message' => 'marsa deleted successfully',
        ],
            200);
    }
}
