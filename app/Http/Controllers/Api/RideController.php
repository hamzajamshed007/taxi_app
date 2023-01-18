<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Ride;
use App\Models\RideCalculation;
use Validator;
use App\Http\Resources\RideResource;
use App\Models\RideRequest;
use Auth;
use Carbon\Carbon;

class RideController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rides = Ride::all();
      
        return $this->sendResponse(RideResource::collection($rides), 'Rides retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $location_data = json_encode($request->except('name'));
        $request->request->add(['detail'=> $location_data]);
        $request->request->add(['user_id'=> Auth::user()->id]);
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'name' => 'required',
            'from_latitude' => 'required|numeric',
            'from_longitude' => 'required|numeric',
            'to_latitude' => 'required|numeric',
            'to_longitude' => 'required|numeric',
           
        ]);
     
        if($validator->fails()){
            return $this->sendResponse('Validation Error.', $validator->errors());       
        }
        
        
  

        $ride = Ride::create($input);
     
        return $this->sendResponse(new RideResource($ride), 'Ride created successfully.');
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
            'id' => 'required|exists:rides,id',
          
        ]);
     
        if($validator->fails()){
            return $this->sendResponse('Validation Error.', $validator->errors());       
        }
     
        $ride = Ride::find($id);
    
        if (is_null($ride)) {
            return $this->sendResponse('Ride not found.');
        }
     
        return $this->sendResponse(new RideResource($ride), 'Ride retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ride $ride)
    {
        
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'name' => 'required',
            'from_latitude' => 'required|numeric',
            'from_longitude' => 'required|numeric',
            'to_latitude' => 'required|numeric',
            'to_longitude' => 'required|numeric',
        ]);
     
        if($validator->fails()){
            return $this->sendResponse('Validation Error.', $validator->errors());       
        }
     
        $ride->name = $input['name'];
        $location_data = json_encode($request);
        
        $ride->detail = $location_data;
        $ride->save();
     
        return $this->sendResponse(new RideResource($ride), 'Ride updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ride $ride)
    {
        $ride->delete();
     
        return $this->sendResponse([], 'Ride deleted successfully.');
    }



     /**
     * Accept api
     *
     * @return \Illuminate\Http\Response
     */
    public function acceptRequest($request_id){
        

        if(RideRequest::where('id','=',$request_id)->exists()){
            $ride_id=RideRequest::where('id','=',$request_id)->get()->first()->ride_id;
           
            if(Auth::user()->user_type=='p'){
                if(RideRequest::where('id','=',$request_id)->get()->first()->accepted != null){
                    return $this->sendResponse('Invalid.', ['error'=>'This Request Is Already Accepted']);
                }
                else{
                    if(RideRequest::where('ride_id','=',$ride_id)->where('accepted','<>',null)->count()>0){
                        return $this->sendResponse('Invalid.', ['error'=>'Some Other Request On This Ride Is Already Accepted']);
                    }
                    else{
                        $success=RideRequest::where('id','=',$request_id)->update(['accepted'=>1]);
                        $success=Ride::find($ride_id)->update(['accepted_driver_id' => Auth::user()->id]);
                        if($success){
                            return $this->sendResponse('Success', 'Request Accepted');
                        }
                    }
            }
            }
            else{
                return $this->sendError('Invalid.', ['error'=>'Only Passengers Can Accept A Ride Request']);
                
            }

        }
        else{
            return $this->sendError('Invalid.', ['error'=>'No Such Request Exist']);

        }
        return $this->sendError('error.', ['error'=>'error']);


    }

    public function startRide(Request $request){
        $request_id=$request->ride_id;
        if(Ride::find($request_id)){
            $ride=Ride::where('id','=',$request_id)->get()->first();
            if($ride->accepted_driver_id==null){
                return $this->sendError('Invalid.', ['error'=>'Ride Request Not Accepted Yet']);

            }
            else{
                if(RideCalculation::where('ride_id','=',$request_id)->exists()){
                return $this->sendError('Invalid.', ['error'=>'Ride Is Already Started']);

                }
                else{
                RideCalculation::create(['ride_id' => $request_id,'per_km_rate' => $request->per_km_rate,'pause_per_min_rate' => $request->pause_per_min_rate]);
                return $this->sendResponse('Success', 'Ride Started');
                }

            }

        }
        else{
            return $this->sendError('Invalid.', ['error'=>'No Such Ride Exist']);

        }

    }

    public function pauseRide(Request $request){
        $request_id=$request->ride_id;
        if(RideCalculation::where('ride_id','=',$request_id)->exists()){
            $calc=RideCalculation::where('ride_id','=',$request_id)->get()->first();
            if($calc->pause_start==null){
                RideCalculation::where('ride_id','=',$request_id)->update(['pause_start' => \Carbon\Carbon::now() ]);
                return $this->sendResponse('Success', 'Ride Paused');
                
            }
            else{
            return $this->sendError('Invalid.', ['error'=>'Ride Already In Pause']);

            }
        }
        else{
            return $this->sendError('Invalid.', ['error'=>'Ride Not Started Yet']);

        }

    }

    public function resumeRide(Request $request){
        $request_id=$request->ride_id;
        if(RideCalculation::where('ride_id','=',$request_id)->exists()){
            $calc=RideCalculation::where('ride_id','=',$request_id)->get()->first();
            if($calc->pause_start==null){
                
                return $this->sendError('Invalid.', ['error'=>'Ride Not In Pause']);
                
            }   
            else{
                RideCalculation::where('ride_id','=',$request_id)->update(['pause_end' => \Carbon\Carbon::now() ]);
                $calc=RideCalculation::where('ride_id','=',$request_id)->get()->first();

                $start=$calc->pause_start; //2022-08-09 17:15:02
                $end=$calc->pause_end; //2022-08-09 17:15:02
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $end);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $start);
                $diff_in_minutes = $to->diffInMinutes($from);
                $new_pause=$diff_in_minutes;
                $total_pause=$new_pause+$calc->total_pause_time;
                RideCalculation::where('ride_id','=',$request_id)->update(['total_pause_time' =>$total_pause ]);
                RideCalculation::where('ride_id','=',$request_id)->update(['pause_start' =>null ]);
                RideCalculation::where('ride_id','=',$request_id)->update(['pause_end' =>null ]);
                return $this->sendResponse('Success', 'Ride Paused');

            }
        }
        else{
            return $this->sendError('Invalid.', ['error'=>'Ride Not Started Yet']);

        }

    }

    public function endRide(Request $request){
        $request_id=$request->ride_id;
        if(RideCalculation::where('ride_id','=',$request_id)->exists()){
            if(Ride::where('id','=',$request_id)->get()->first()->ended==null){
            $calc=RideCalculation::where('ride_id','=',$request_id)->get()->first();
            $total_distance_in_km=$request->total_distance_in_km;
            $distance_total=$calc->per_km_rate*$total_distance_in_km;
            $pause_total=$calc->pause_per_min_rate*$calc->total_pause_time;

            RideCalculation::where('ride_id','=',$request_id)->update(['total_distance_traveled' =>$total_distance_in_km
            ,'distance_total' => $distance_total,
            'pause_total' => $pause_total,
            ]);
            Ride::where('id','=',$request_id)->update(['ended'=>1]);
            return $this->sendResponse('Success', 'Ride End');

            }
            else{
                return $this->sendError('Invalid.', ['error'=>'Ride Already Ended']);
    
            }
           
        }
        else{
            return $this->sendError('Invalid.', ['error'=>'Ride Not Started Yet']);

        }

    }

 
    public function getCalculations(Request $request){
        $data=RideCalculation::where('ride_id','=',$request->ride_id)->get();
        if($data != []){
            return response()->json(['calculations' => $data]);
        }
    }
}