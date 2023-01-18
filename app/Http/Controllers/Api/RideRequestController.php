<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\RideRequest;
use Validator;
use App\Http\Resources\RideRequestResource;
use Auth;
use DB;
class RideRequestController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ride_requests = RideRequest::where('ride_id','=',$request->ride_id)->get();
      
        return $this->sendResponse(RideRequestResource::collection($ride_requests), 'RideRequests retrieved successfully.');
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
            // 'ride_id' => 'required|exists:rides,id',
            'ride_id'  => 'required|exists:rides,id|unique:ride_requests,ride_id,user_id'.Auth::user()->id
       
         
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
       
        $ride_request = RideRequest::create($input);
     
        return $this->sendResponse(new RideRequestResource($ride_request), 'RideRequest created successfully.');
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
            'id' => 'required|exists:ride_requests,id',
          
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $ride_request = RideRequest::find($id);
    
        if (is_null($ride_request)) {
            return $this->sendError('RideRequest not found.');
        }
     
        return $this->sendResponse(new RideRequestResource($ride_request), 'RideRequest retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RideRequest $ride_request)
    {
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'ride_id' => 'required|exists:rides,id',
          
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $ride_request->ride_id = $input['ride_id'];
        $ride_request->save();
     
        return $this->sendResponse(new RideRequestResource($ride_request), 'RideRequest updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(RideRequest $ride_request)
    {
        $ride_request->delete();
     
        return $this->sendResponse([], 'RideRequest deleted successfully.');
    }

    public function show_for_ride(Request $request){
        $data= DB::table('ride_requests')->join('users','ride_requests.user_id','=','users.id')->where('ride_requests.ride_id','=',$request->ride_id)->select('users.name','ride_requests.*')->get();
        return $this->sendResponse($data, 'RideRequest deleted successfully.');
        
    }
}