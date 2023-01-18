<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class RideCalculation extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'ride_id','pause_start','pause_end','total_pause_time','total_distance_traveled','per_km_rate','pause_per_min_rate','distance_total','pause_total'
    ];
}