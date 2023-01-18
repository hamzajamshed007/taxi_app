<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Notification;
use Validator;
use App\Http\Resources\NotificationResource;
use Auth;
use DB;
class NotificationController extends BaseController
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
        $notifications = DB::table('notifications')->where('user_id','=',Auth::user()->id)->where('title','LIKE','%'.$search_query.'%')->paginate(10);
      
        return response()->json(['results'=>$notifications]);
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
            'title' => 'required',
            'message' => 'required',
            'user_id' => 'required|exists:users,id',
            
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $ride = Notification::create($input);
     
        return $this->sendResponse(new NotificationResource($ride), 'Notification created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ride = Notification::find($id);
    
        if (is_null($ride)) {
            return $this->sendError('Notification not found.');
        }
     
        return $this->sendResponse(new NotificationResource($ride), 'Notification retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $ride)
    {
        $ride=Notification::where('id','=',$request->notification_id)->get()->first();
        $input = $request->all();
     
        $validator = Validator::make($input, [
            'title' => 'required',
            'message' => 'required',
            'user_id' => 'required|exists:users,id',

        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
     
        $ride->title = $input['title'];
        $ride->message = $input['message'];
        $ride->user_id = $input['user_id'];
  

        $ride->save();
     
        return $this->sendResponse(new NotificationResource($ride), 'Notification updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $ride)
    {
        
        $ride->delete();
     
        return $this->sendResponse([], 'Notification deleted successfully.');
    }
}