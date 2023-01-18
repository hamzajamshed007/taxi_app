<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController as BaseController;
use Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB;

class DriverController extends BaseController
{
    //
    function updateDriverInfo(Request $request){
        
        if(!DB::table('extra_driver_info')->where('user_id','=',Auth::user()->id)->exists()){
            Db::table('extra_driver_info')->insert(['user_id' => Auth::user()->id]);
        }
        
        if($request->type=='step_1'){

        $driver_license_front='';
        $driver_license_back='';

 
        if ($request->hasFile('photoFront')) {
            $name=Auth::user()->name;
            Storage::disk('local')->makeDirectory('public/tenant_files/' . $name . '/product_colors_images/');
            $filename = $request->photoFront->store('public/tenant_files/' . $name . '/product_colors_images');
            $driver_license_front = substr($filename, 7);
        }
        if ($request->hasFile('photo')) {
            $name=Auth::user()->name;
            Storage::disk('local')->makeDirectory('public/tenant_files/' . $name . '/product_colors_images/');
            $filename = $request->photo->store('public/tenant_files/' . $name . '/product_colors_images');
            $driver_license_back = substr($filename, 7);
        }

        DB::table('extra_driver_info')->where('user_id','=',Auth::user()->id)->update(
            [
                'driver_license_front' => $driver_license_front,
                'driver_license_back' => $driver_license_back,
                'date_of_expiration' => $request->driver_license_expiry,
                'driver_license_number' => $request->driver_license_number,
            ]
        );
       // return $this->sendResponse([], 'Driver info updated successfully.');


    }

    if($request->type=='step_2'){

        $driver_license_front='';
        $driver_license_back='';

 
        if ($request->hasFile('photoFront')) {
            $name=Auth::user()->name;
            Storage::disk('local')->makeDirectory('public/tenant_files/' . $name . '/product_colors_images/');
            $filename = $request->photoFront->store('public/tenant_files/' . $name . '/product_colors_images');
            $driver_license_front = substr($filename, 7);
        }
        if ($request->hasFile('photo')) {
            $name=Auth::user()->name;
            Storage::disk('local')->makeDirectory('public/tenant_files/' . $name . '/product_colors_images/');
            $filename = $request->photo->store('public/tenant_files/' . $name . '/product_colors_images');
            $driver_license_back = substr($filename, 7);
        }

        DB::table('extra_driver_info')->where('user_id','=',Auth::user()->id)->update(
            [
                'cnic_front' => $driver_license_front,
                'cnic_back' => $driver_license_back,
                'date_of_expiration' => $request->driver_license_expiry,
                'cnic_number' => $request->driver_license_number,
            ]
        );
       // return $this->sendResponse([], 'Driver info updated successfully.');


    }

    if($request->type=='step_3'){

        $car_photo='';
        if ($request->hasFile('photo')) {
            $name=Auth::user()->name;
            Storage::disk('local')->makeDirectory('public/tenant_files/' . $name . '/product_colors_images/');
            $filename = $request->photo->store('public/tenant_files/' . $name . '/product_colors_images');
            $car_photo = substr($filename, 7);
        }

        DB::table('extra_driver_info')->where('user_id','=',Auth::user()->id)->update(
            [
                'car_photo' => $car_photo,
                'number_plate' =>  $request->number_plate,
                'model_year' => $request->model_year,
                'car_name' => $request->car_name,
            ]
        );
       // return $this->sendResponse([], 'Driver info updated successfully.');


    }
    if($request->type=='step_4'){

        $driver_license_front='';
        $driver_license_back='';

 
        if ($request->hasFile('photoFront')) {
            $name=Auth::user()->name;
            Storage::disk('local')->makeDirectory('public/tenant_files/' . $name . '/product_colors_images/');
            $filename = $request->photoFront->store('public/tenant_files/' . $name . '/product_colors_images');
            $driver_license_front = substr($filename, 7);
        }
        if ($request->hasFile('photo')) {
            $name=Auth::user()->name;
            Storage::disk('local')->makeDirectory('public/tenant_files/' . $name . '/product_colors_images/');
            $filename = $request->photo->store('public/tenant_files/' . $name . '/product_colors_images');
            $driver_license_back = substr($filename, 7);
        }

        DB::table('extra_driver_info')->where('user_id','=',Auth::user()->id)->update(
            [
                'car_certificate_front' => $driver_license_front,
                'car_certificate_back' => $driver_license_back,
               
            ]
        );
       // return $this->sendResponse([], 'Driver info updated successfully.');


    }
    
    //status check
    $info=DB::table('extra_driver_info')->where('user_id','=',Auth::user()->id)->get()->first();
    if($info->driver_license_number != null && $info->driver_license_front != null && $info->driver_license_back != null && $info->date_of_expiration != null){
        $user=User::find(Auth::user()->id);
        $user->registration_status=1;
        $user->save();
    }
    if($info->cnic_number != null && $info->cnic_front != null && $info->cnic_back != null ){
        $user=User::find(Auth::user()->id);
        $user->registration_status=2;
        $user->save();
    }
    if($info->car_name != null && $info->number_plate != null && $info->model_year != null && $info->car_photo != null){
        $user=User::find(Auth::user()->id);
        $user->registration_status=3;
        $user->save();
    }
    if($info->car_certificate_front != null && $info->car_certificate_back != null ){
        $user=User::find(Auth::user()->id);
        $user->registration_status=4;
        $user->save();
    }
    return $this->sendResponse([], 'Driver info updated successfully.');
    }
}
