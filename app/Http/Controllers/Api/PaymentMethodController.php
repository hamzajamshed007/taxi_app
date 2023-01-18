<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\PaymentMethod;
use Validator;
use App\Http\Resources\PaymentMethodResource;
use Auth;
use DB;

class PaymentMethodController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment_methods = PaymentMethod::all();
      
        return $this->sendResponse(PaymentMethodResource::collection($payment_methods), 'PaymentMethods retrieved successfully.');
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
        $request->request->add(['detail'=> 'Default']);
        $input = $request->all();
        DB::table('payment_methods')->where('user_id','=', Auth::user()->id)->update(['detail' => '']);
      
        $validator = Validator::make($input, [
            'name' => 'required',
            'card_number' => 'required',
            'cvv' => 'required',
            'expiry' => 'required',
         
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
       
        $payment_method = PaymentMethod::create($input);
     
        return $this->sendResponse(new PaymentMethodResource($payment_method), 'PaymentMethod created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment_method = PaymentMethod::find($id);
    
        if (is_null($payment_method)) {
            return $this->sendError('PaymentMethod not found.');
        }
     
        return $this->sendResponse(new PaymentMethodResource($payment_method), 'PaymentMethod retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentMethod $payment_method)
    {
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'card_number' => 'required',
            'cvv' => 'required',
            'expiry' => 'required',
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $payment_method->name = $input['name'];
        $payment_method->detail = $input['detail'];
        $payment_method->card_number = $input['card_number'];
        $payment_method->cvv = $input['cvv'];
        $payment_method->expiry = $input['expiry'];
        $payment_method->save();
     
        return $this->sendResponse(new PaymentMethodResource($payment_method), 'PaymentMethod updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $payment_method)
    {
        $payment_method->delete();
     
        return $this->sendResponse([], 'PaymentMethod deleted successfully.');
    }

    public function make_default(Request $request){
        DB::table('payment_methods')->where('user_id','=', Auth::user()->id)->update(['detail' => '']);
        DB::table('payment_methods')->where('user_id','=', Auth::user()->id)->where('id','=',$request->id)->update(['detail' => 'Default']);
        return $this->sendResponse([], 'PaymentMethod deleted successfully.');

    }

    public function get_default(Request $request){

        $result = DB::table('payment_methods')->where('user_id','=', Auth::user()->id)->where('detail' ,'=', 'Default')->get();
        return $this->sendResponse([],  $result->first()->card_number);
        


    }
}