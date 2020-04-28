<?php

namespace App\Http\Controllers\api;



//use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use  Validator;
use App\necklase;
use Illuminate\Support\Facades\DB;
use App\customer;
use App\User;
use Illuminate\Support\Facades\Auth;
//use PDF;
use Carbon\Carbon;
use View;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;


class contractcontroller extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $necklases = necklase::all();
        
        foreach($necklases as $necklase)
        {
                       $necklase['contractCreator'] = customer::select('name')->where('id','=',$necklase->customerid)->first();
                       $necklase['userCreator'] = User::select('name')->where('id','=',$necklase->userid)->first();


        }
        return response()->json([
            'message' => 'Contracts viewed successfully',
            'contracts' => $necklases,
            
        ],
            200);//
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
            'date' => 'required',
            'marsaid' => 'required',
            'customerid' => 'required',
            'many' => 'required',
            'add' => 'required',
            'total' => 'required',
            'from' => 'required',
            'to' => 'required',
            'batch' => 'required',
            'totalbatches' => 'required',
            'wastaname' => 'required',
            'codewasta' => 'required',
            'hieght' => 'required',
            'width' => 'required',

        ]);

        if ($validator->fails()) {
          
            return $this->sendError('error validation', $validator->errors());
        }
        
         $input['from'] =  \Carbon\Carbon::parse($input['from'])->format('Y-m-d');
         $input['to'] =  \Carbon\Carbon::parse($input['to'])->format('Y-m-d');
          $input['date'] =  \Carbon\Carbon::parse($input['date'])->format('Y-m-d');
                                    
        
        $input['total'] = $request->total;
        $input['totalbatches'] =$request->totalbatches;
        $input['remainingamount'] = $input['total'];
        $input['userid'] = Auth::user()->id;
        
        
        $necklases = necklase::create($input);
   
        $contractData  = DB::table('necklases')
            ->join('users','necklases.userid','=','users.id')
            ->join('customers','necklases.customerid','=','customers.id')
            ->where('necklases.id',$necklases->id)
            ->select('necklases.*','users.name as owner','customers.*')
            ->get();
            
            
            $contractUrl = $this->contractUrl($request,$necklases->id);
            $necklases->update([
           'contractUrl'=>$contractUrl
           ]);
        
        return response()->json([
            'message' => 'Contract created successfully',
            'Contract' => $necklases,
            'ContractData' => $contractData],
            200);
   
    }

   protected function contractUrl(Request $request,$id)
    {
        
        $contractData = $this->getContractById($id);
        $created_at = $contractData[0]->created_at;
        $dateAndTime = explode(" ",$created_at);
        $contractData['date'] = $dateAndTime[0];
        $contractData['time'] = $dateAndTime[1];
       

        $fileName = 'contract-'.$id.'.pdf';
        $path     = public_path('contracts/'.$fileName.'');

        $day =$this->convertDaysToArabic($contractData[0]->date);
        $contractData['day'] = $day;


        $pdf = PDF::loadView('contracts.pdf', ['contract'=>$contractData]);
        $pdf_string =$pdf->output($fileName);
        file_put_contents($path, $pdf_string);

        $url = 'https://'.$request->server('HTTP_HOST').'/new/public/contracts/contract-' . $id . ".pdf";
        return $url;

    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $necklase = necklase::find($id);
        if (is_null($necklase)) {
            # code...
            return $this->sendError('marsa not found ! ');
        }
        return response()->json([
            'message' => 'contract viewed successfully',
            'contracts' => $necklase
        ],
            200);
    }

    public function showPdf(Request $request,$id)
    {
        
        $contractData = $this->getContractById($id);
        $created_at = $contractData[0]->created_at;
        $dateAndTime = explode(" ",$created_at);
        $contractData['date'] = $dateAndTime[0];
        $contractData['time'] = $dateAndTime[1];
       

        $fileName = 'contract-'.$id.'.pdf';
        $path     = public_path('contracts/'.$fileName.'');

        $day =$this->convertDaysToArabic($contractData[0]->date);
        $contractData['day'] = $day;


        $pdf = PDF::loadView('contracts.pdf', ['contract'=>$contractData]);
        $pdf_string =$pdf->output($fileName);
        file_put_contents($path, $pdf_string);

        $url = 'https://'.$request->server('HTTP_HOST').'/new/public/contracts/contract-' . $id . ".pdf";
        return response()->json(["url"=>$url]);

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
       /* $validator = Validator::make($input, [

            'marsaid' => 'required',
            'customerid' => 'required',
            'many' => 'required',
            'add' => 'required',
            'total' => 'required',
            'from' => 'required',
            'to' => 'required',
            'batch' => 'required',
            'totalbatches' => 'required',
            'wastaname' => 'required',
            'codewasta' => 'required',
            'hieght' => 'required',
            'width' => 'required',
        ]);

        if ($validator->fails()) {
            # code...
            return $this->sendError('error validation', $validator->errors());
        }*/
        $necklas = necklase::find($id);
      
        $input['userid']=  $input['userid']=Auth::user()->id;
         $input['from'] =  \Carbon\Carbon::parse($input['from'])->format('Y-m-d');
         $input['to'] =  \Carbon\Carbon::parse($input['to'])->format('Y-m-d');
         $input['date'] =  \Carbon\Carbon::parse($input['date'])->format('Y-m-d');
        
        
        $necklas->update($input);
        return response()->json([
            'message' => 'Contract updated successfully',
            'contract' => $necklas
        ],
            200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $necklase = necklase::find($id);

        if(!$necklase)
        {
            return response()->json([
                'error' => 'Contract not found',
            ],
                400);
        }
        $necklase->delete();

        return response()->json([
            'message' => 'Contract deleted successfully',
        ],
            200);
    }
    
       public function getContractsByCustomerId(Request $request,$id)
   {
       $customer = customer::find($id);
       $contracts = $customer->contracts;

           if(empty($contracts))
       {
             if(strtoupper($request->lang)=='EN')
             {
                 return response()->json([
                     'message' => 'This Customer Have No Contracts',
                 ], 200);
             }
           if(strtoupper($request->lang)=='AR')
           {
               return response()->json([
                   'message' => 'هذ العميل ليس لديه عقود',
               ], 200);
           }
       }

       return response()->json([
           'message' => 'Contracts retrieved successfully',
           'contracts' => $contracts,
       ],
           200);
   }
   
   protected function getContractById($id)
   {
       $contractData  = DB::table('necklases')
           ->join('marsas','necklases.marsaid','=','marsas.id')
           ->join('users','necklases.userid','=','users.id')
           ->join('customers','necklases.customerid','=','customers.id')
           ->where('necklases.id', $id)
           ->select('necklases.*','users.name as owner','customers.*','marsas.numbervat')
           ->get();
       return $contractData;

   }

   protected function convertDaysToArabic($date)
   {
       $englishName =Carbon::parse($date)->format('l');
       switch ($englishName)
       {
           case 'Saturday':
               return "السبت";
               break;
           case 'Sunday':
               return "اﻷحد";
               break;
           case 'Monday':
               return "اﻹثنين";
               break;
           case 'Tuesday':
               return "الثلاثاء";
               break;
           case 'Wednesday':
               return "اﻷربعاء";
               break;
           case 'Thursday':
               return "الخميس";
               break;
           default:
               return "الجمعه";
               break;
       }
   }
   
}
