<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentMethod;
use App\Models\Ride;
use Illuminate\Http\Request;
use Auth;
use DB;
class DashboardController extends BaseController
{
    //

    function index(){
    
        $user_type=Auth::user()->user_type;
        if($user_type=='a'){
            $number_of_passengers=DB::table('users')->where('user_type','=','p')->count();
            $number_of_drivers=DB::table('users')->where('user_type','=','d')->count();
            $transactions = DB::table('transactions')->where('user_id','=',Auth::user()->id)->take(10)->get();
             return response()->json([
                'transactions'=>$transactions,
                'number_of_passengers'=>$number_of_passengers,
                'number_of_drivers'=>$number_of_drivers,
            ]);
           }
        else if($user_type=='p'){
            $data['name']=Auth::user()->name;
            $data['email']=Auth::user()->email;
            $data['payment_method']=PaymentMethod::where('user_id',Auth::user()->id)->get()->first();
            $data['number_of_rides']=Ride::where('user_id',Auth::user()->id)->where('ended','=',1)->count();
            $data['total_distance']=DB::table('ride_calculations')
                                 ->join('rides','ride_calculations.ride_id','=','rides.id')
                                 ->where('rides.user_id',Auth::user()->id)->where('rides.ended','=',1)
                                 ->sum('ride_calculations.total_distance_traveled');
 
            return response()->json([
             'data'=>$data
         ]);
        }
        else if($user_type=='d'){
           $data['name']=Auth::user()->name;
           $data['email']=Auth::user()->email;
           $data['payment_method']=PaymentMethod::where('user_id',Auth::user()->id)->get()->first();
           $data['number_of_rides']=Ride::where('accepted_driver_id',Auth::user()->id)->where('ended','=',1)->count();
           $data['total_distance']=DB::table('ride_calculations')
                                ->join('rides','ride_calculations.ride_id','=','rides.id')
                                ->where('rides.accepted_driver_id',Auth::user()->id)->where('rides.ended','=',1)
                                ->sum('ride_calculations.total_distance_traveled');

           return response()->json([
            'data'=>$data
        ]);
        }
        else {
            return $this->sendError('Validation Error.','invalid user');       
        }
    }
}
