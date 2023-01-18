<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\RideRequest;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
   use DB;
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
  
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_type' => 'required|in:p,d',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $num_str = sprintf("%06d", mt_rand(1, 999999));
        
        $user = User::create(array_merge($input,['verification_code' => $num_str]) );
            
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

       // $this->htmlmail($user->email,$user->verification_code);

   
        return $this->sendResponse($success, 'User register successfully.');
    }

    public function registerAdmin(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_type' => 'required|in:a',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create(array_merge($input,['verification_code' => $num_str]) );
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
 
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
            $success['user_type'] =  $user->user_type;
            $success['ride_status']='none';//ride_created,ride_started,
            $success['registration_status'] =  $user->registration_status; 
            //ride_requests
            //ride_calculations
            //rides
            if($user->user_type=='p'){
                $ride=DB::table('rides')->where('user_id','=',$user->id)->get()->first();
                if($ride){
                    if($ride->accepted_driver_id==null){
                        $success['ride_status']='driver_to_be_accepted';

                    }
                }
            }
            
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function verify_user(Request $request){
        $user=User::find(Auth::user()->id);
        if(Auth::user()->verification_code==$request->verification_code){
        $user->email_verified_at= \Carbon\Carbon::now();
        $user->markEmailAsVerified();
        $user->save();
        return response()->json(['success' => ' true' , 'message' => 'User Verified Successfully' ]);
        }
        else{
        return response()->json(['success' => ' false' , 'message' => 'Code Does Not Match' ]);

        }
    }
    public function resend_verify_code(Request $request){
        $user=User::find(Auth::user()->id);
        $num_str = sprintf("%06d", mt_rand(1, 999999));
        $user->verification_code= $num_str;
        $user->save();
        $this->htmlmail($user->email,$user->verification_code);
        return response()->json(['success' => ' true' , 'message' => 'Code Sent Again' ]);

    }
   
    public function update_user(Request $request){
        if(User::find(Auth::user()->id)){
            $user=User::find(Auth::user()->id);
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            if($request->password != null && $request->password != 'null' && $request->password != ''){
                $user->password= $input['password'] ;
            }
            if($request->name != null && $request->name != 'null' && $request->name != ''){
                $user->name= $input['name'] ;
            }
            $user->save();
            return response()->json(['success' => ' true' , 'message' => 'User Updated Successfully' ]);
            
        
        }
        else{
            return response()->json(['error' => ' true' , 'message' => 'User Not Valid' ]);

        }
    }
    public function logout_user(Request $request){
            $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['success' => ' true' , 'message' => 'User Loged Out.' ]);

      
    }
}