<?php

namespace App\Http\Controllers\api;


use Illuminate\Http\Request;
use App\customer;
use App\Http\Controllers\api\BaseController as BaseController;
use  Validator;



class customercontroller extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cutomers = customer::all();
        return $this->sendResponse($cutomers->toArray(), ' cutomers  read succesfully');
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required',
            'nationalid' => 'required',
            'phoneone' => 'required',
        ]);

        if ($validator->fails()) {
            # code...
            return $this->sendError('error validation', $validator->errors());
        }

        $customers = Customer::all();
        
        foreach($customers as $customer)
        {
            if($customer->phoneone == $input['phoneone'])
            {
              return response()->json([
            'message' => 'this number already taken'],
            200);
            }
        
        }
        
        $customer = customer::create($input);
        
        return response()->json([
            'message' => 'customer created successfully',
            'Customer' => $customer],
            200);
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = customer::find($id);
        if (is_null($customer)) {
            # code...
            return $this->sendError('customer not found ! ');
        }

        return response()->json([
            'message' => 'customer view successfully',
            'Customer' => $customer],
            200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $input = $request->all();
    

        $customer = customer::find($id);
        
    
        $customer->update($input);
        return response()->json([
            'message' => 'customer view successfully',
            'Customer' => $customer],
            200);        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = customer::find($id);

        if(!$customer)
        {
            return response()->json([
                'error' => 'customer not found',
            ],
                400);
        }
        $customer->delete();

        return response()->json([
            'message' => 'customer deleted successfully',
            ],
            200);
    }
}
