<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use  Validator;
use App\Batch;
use App\necklase;
use DB;
use App\User;
use Illuminate\Support\Facades\Auth;
//use PDF;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;


class batchController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $batches = Batch::all();
        
        
        return response()->json([
            'message' => 'Batches viewed successfully',
            'batch' => $batches],
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
        $validator = Validator::make($input, [
            'necklaceid' => 'required',
            'amount' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);

       
$input['from'] =  \Carbon\Carbon::parse($input['from'])->format('Y-m-d');
$input['to'] =  \Carbon\Carbon::parse($input['to'])->format('Y-m-d');
         
         
         
        if ($validator->fails()) {
            # code...
            return $this->sendError('error validation', $validator->errors());
        }

        // $input['user_id'] = Auth::user()->id;
        $batch = Batch::create($input);

        if ($batch){
            $necklases = necklase::find($request->necklaceid);
            $necklases->remainingamount = $necklases->remainingamount - $request->amount;
            $necklases->remainingbatches = $necklases->remainingbatches - 1;
            $necklases->updated_at = date('Y-m-d');
            $necklases->save();
        }
        $contractBatch = DB::table('batches')
                             ->join('necklases','batches.necklaceid','=','necklases.id')
                             ->join('customers','necklases.customerid','=','customers.id')
                             ->where('batches.id',$batch->id)
                             ->select('batches.*','customers.name','necklases.wastaname','necklases.codewasta')
                             ->get();
       // return view('mails.batch',compact('contractBatch'));
       
       $btachUrl = $this->batchUrl($request,$batch->id);
       
       
       $batch->update([
           'batchUrl'=>$btachUrl,
           'user_id'=>Auth::user()->id,
           ]);
       
       
        if($necklases->remainingamount==0)
        {
            return response()->json([
            'message' => 'your batches are done  '],
            200);
        }
       
        return response()->json([
            'message' => 'batch stored successfully',
            'batch' => $contractBatch,
            'batchUrl'=>$btachUrl
        ],
            200);//    
    }


    protected function batchUrl(Request $request,$id)
    {
        $batchData = $this->getBatchById($id);
        $created_at = $batchData[0]->created_at;
        $dateAndTime = explode(" ",$created_at);
        $batchData['date'] = $dateAndTime[0];
        $batchData['time'] = $dateAndTime[1];
        

        $fileName = 'batch-'.$id.'.pdf';
        $path     = public_path('batches/'.$fileName.'');

        $pdf = PDF::loadView('contracts.batch', ['contract'=>$batchData]);
        $pdf_string =$pdf->output($fileName);
        file_put_contents($path, $pdf_string);

        $url = 'https://'.$request->server('HTTP_HOST').'/new/public/batches/batch-' . $id . ".pdf";
        return $url;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $batch = Batch::find($id);
        if (is_null($batch)) {
            # code...
            return $this->sendError('batch not found ! ');
        }
        return response()->json([
            'message' => 'batch Viewed successfully',
            'batch' => $batch],
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
        $batch =  Batch::find($id);

 $input['from'] =  \Carbon\Carbon::parse($input['from'])->format('Y-m-d');
 $input['to'] =  \Carbon\Carbon::parse($input['to'])->format('Y-m-d');
 

        
           $batch = $batch->update([
           'necklaceid' => $input['necklaceid'],
            'amount' => $input['amount'],
            'from' => $input['from'],
            'to' => $input['to'],
           ]);
        return response()->json([
            'message' => 'batch updated successfully',
            'batch' => $batch],
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
        $batch = Batch::find($id);

        if(!$batch)
        {
            return response()->json([
                'error' => 'batch not found',
            ],
                400);
        }
        $batch->delete();

        return response()->json([
            'message' => 'batch deleted successfully',
        ],
            200);
    }
    
    public function showPdf(Request $request,$id)
    {
        $batchData = $this->getBatchById($id);
        $created_at = $batchData[0]->created_at;
        $dateAndTime = explode(" ",$created_at);
        $batchData['date'] = $dateAndTime[0];
        $batchData['time'] = $dateAndTime[1];
        

        $fileName = 'batch-'.$id.'.pdf';
        $path     = public_path('batches/'.$fileName.'');

        $pdf = PDF::loadView('contracts.batch', ['contract'=>$batchData]);
        $pdf_string =$pdf->output($fileName);
        file_put_contents($path, $pdf_string);

        $url = 'https://'.$request->server('HTTP_HOST').'/new/public/batches/batch-' . $id . ".pdf";
        return response()->json(["url"=>$url]);

    }

    protected function getBatchById($id)
    {
        $batchData  = DB::table('batches')
            ->join('necklases','batches.necklaceid','=','necklases.id')
            ->join('users','necklases.userid','=','users.id')
            ->join('customers','necklases.customerid','=','customers.id')
            ->where('batches.id', $id)
            ->select('batches.*','necklases.wastaname','necklases.codewasta','users.name as owner','customers.*')
            ->get();
        return $batchData;

    }
    
    public function getBatchesByContractId(Request $request,$id)
   {
       $contract = necklase::find($id);
       $batches = $contract->batches;
       
    
       foreach($batches as $batch)
       {
           $batch['batchCreator'] = User::select('name')->where('id','=',$batch->user_id)->first();
           
       }

        if(empty($batches))
       {
             if(strtoupper($request->lang)=='EN')
             {
                 return response()->json([
                     'message' => 'This Contract Have No Contracts',
                 ], 200);
             }
           if(strtoupper($request->lang)=='AR')
           {
               return response()->json([
                   'message' => 'هذ العقد لم يتم تسجيل اى دفعه له حتى الان ',
               ], 200);
           }
       }

       return response()->json([
           'message' => 'Batches retrieved successfully',
           'batches' => $batches,
       ],
           200);
   }
   
   public function getBatch_ById($id)
    {
        $batchData  = DB::table('batches')
            ->join('necklases','batches.necklaceid','=','necklases.id')
            ->join('users','necklases.userid','=','users.id')
            ->join('customers','necklases.customerid','=','customers.id')
            ->where('batches.id', $id)
            ->select('batches.*','necklases.wastaname','necklases.codewasta','users.name as owner','customers.name as customerName')
            ->get();

       return response()->json([
           'message' => 'Batche retrieved successfully',
           'batchData' => $batchData,
       ],
           200);

    }
    
}
