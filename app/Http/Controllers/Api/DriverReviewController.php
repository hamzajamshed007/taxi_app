<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\DriverReview;
use Validator;
use App\Http\Resources\DriverReviewResource;
use App\Models\Ride;
use Auth;
     
class DriverReviewController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $driver_reviews = DriverReview::where('driver_id','=',$request->driver_id)->get();
      
        return $this->sendResponse(DriverReviewResource::collection($driver_reviews), 'RideRequests retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       

        if(!Ride::find($request->ride_id)){
            return $this->sendError('Invalid.', ['error'=>'Ride Not Valid']);
        }

        if(DriverReview::where('ride_id','=',$request->ride_id)->exists()){
            return $this->sendError('Invalid.', ['error'=>'Ride Already Reviewed']);
        }
        if(Ride::where('id','=',$request->ride_id)->where('ended','=',null)->exists()){
            return $this->sendError('Invalid.', ['error'=>'Ride Not Ended']);
        }
        $ride=Ride::where('id','=',$request->ride_id)->get()->first();
        if($ride->user_id == Auth::user()->id){
            $input = $request->all();
      
            $validator = Validator::make($input, [
                // 'ride_id' => 'required|exists:rides,id',
                'ride_id'  => 'required|exists:rides,id|unique:driver_reviews,ride_id',
                'description'  => 'required',
                'rating'  => 'required',
             
            ]);
         
            if($validator->fails()){
                return $this->sendResponse('Validation Error.', $validator->errors());       
            }
            $driver_id=$ride->accepted_driver_id;
            $driver_review = DriverReview::create([
                'driver_id' => $driver_id,
                'ride_id' => $request->ride_id,
                'description' => $request->description,
                'rating' => $request->rating,
        ]);
        return $this->sendResponse(new DriverReviewResource($driver_review), 'DriverReview created successfully.');

        }
        else{
            return $this->sendError('Invalid.', ['error'=>'Only Ride Passenger Can Review']);
        }

      
       
        } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $driver_review = DriverReview::find($id);
    
        if (is_null($driver_review)) {
            return $this->sendError('RideRequest not found.');
        }
     
        return $this->sendResponse(new DriverReviewResource($driver_review), 'RideRequest retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DriverReview $driver_review)
    {
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'description' => 'required',
            'rating' => 'required',
          
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $driver_review->description = $input['description'];
        $driver_review->rating = $input['rating'];
        $driver_review->save();
     
        return $this->sendResponse(new DriverReviewResource($driver_review), 'RideRequest updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DriverReview $driver_review)
    {
        $driver_review->delete();
     
        return $this->sendResponse([], 'RideRequest deleted successfully.');
    }



}