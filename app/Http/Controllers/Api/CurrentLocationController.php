<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Http\Resources\ProductResource;
use App\Models\Ride;
use App\Models\User;
use Auth;
use DB;
use PDO;

class CurrentLocationController extends BaseController
{
    public function update(Request $request){
        User::where('id','=',Auth::user()->id)->update(['latitude'=> $request->latitude,'longitude'=> $request->longitude]);
        $ride_request=DB::table('rides')->where('rides.accepted_driver_id','=',null)
        ->join('users','rides.user_id','=','users.id')->select('rides.full_address_drop','rides.full_address','users.name as uname','rides.id')->get();

        return $this->sendResponse($ride_request, 'Location updated successfully.');
    }

    public function update_passenger(Request $request){
        User::where('id','=',Auth::user()->id)->update(['latitude'=> $request->latitude,'longitude'=> $request->longitude]);
        $lat=$request->latitude;
        $long=$request->longitude;
        $result = DB::table('users')->where('user_type','=','d')
        ->selectRaw('users.name,users.latitude,users.longitude,users.id,(3956 * 2 * ASIN(SQRT( POWER(SIN(( '.$lat.' - latitude) *  pi()/180 / 2), 2) +COS( '.$lat.' * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(('.$long.' - longitude) * pi()/180 / 2), 2) ))) as distance')->havingRaw('distance <= 10')->orderByRaw('distance')->get();
        return $this->sendResponse($result, 'Location updated successfully.');
    }

    public function fetch(Request $request){
        
        if(Auth::user()->user_type != 'a'){
            if(Auth::user()->id != $request->user_id){
                if(!Ride::where('user_id','=',Auth::user()->id)->where('accepted_driver_id','=',$request->user_id)->where('ended','=',null)->exists()){
                    if(!Ride::where('accepted_driver_id','=',Auth::user()->id)->where('user_id','=',$request->user_id)->where('ended','=',null)->exists()){

                
                        $data['error_line_1']="You Can Only Get Location in 3 cases";
                        $data['error_line_2']="Either You Are An Admin";
                        $data['error_line_3']="Either You Are The User Himself";
                        $data['error_line_4']="Or If You Are In A Ride With User";
                        return $this->sendResponse('error', $data);
            }
        }
    }
        }
        $user=User::where('id','=',$request->user_id)->get()->first();
        $data['latitude']=$user->latitude;
        $data['longitude']=$user->longitude;
        return $this->sendResponse('Result', $data);
    }


    public function get_current_ride_driver(){
        $ride=DB::table('rides')->where('user_id','=',Auth::user()->id)
        ->where('accepted_driver_id','<>',null)
        ->where('ended','<>',1)
        ->orderBy('rides.id','DESC')
        ->join('users','accepted_driver_id','=','users.id')
        ->select('users.name','rides.id')
        ->get();
       
        if($ride->count()>0){
            $data['name']=$ride->first()->name;
            $data['id']=$ride->first()->id;
            return $this->sendResponse('name', $data);

        }
    }
}