<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Transaction;
use Validator;
use App\Http\Resources\TransactionResource;
use Auth;
use DB;
class TransactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(isset($request->searchquery)){
            $search_query= $request->searchquery;
         }
         else{
             $search_query="";
         }
        $transactions = DB::table('transactions')->where('user_id','=',Auth::user()->id)->where('name','LIKE','%'.$search_query.'%')->paginate(10);
      
        return response()->json(['results'=>$transactions]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->request->add(['user_id'=> Auth::user()->id]);
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'name' => 'required',
            'date'  =>  'required|date',
            'amount' => 'required|numeric'
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $ride = Transaction::create($input);
     
        return $this->sendResponse(new TransactionResource($ride), 'Transaction created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:transactions,id',
          
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $ride = Transaction::find($id);
    
        if (is_null($ride)) {
            return $this->sendError('Transaction not found.');
        }
     
        return $this->sendResponse(new TransactionResource($ride), 'Transaction retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $ride)
    {
        $ride=Transaction::where('id','=',$request->transaction_id)->get()->first();
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'name' => 'required',
            'date'  =>  'required|date',
            'amount' => 'required|numeric'
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $ride->name = $input['name'];
        $ride->date = $input['date'];
        $ride->amount = $input['amount'];
  

        $ride->save();
     
        return $this->sendResponse(new TransactionResource($ride), 'Transaction updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:transactions,id',
          
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $ride = Transaction::find($id);
        $ride->delete();
     
        return $this->sendResponse([], 'Transaction deleted successfully.');
    }
}