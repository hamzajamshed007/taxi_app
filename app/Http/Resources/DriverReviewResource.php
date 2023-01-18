<?php
  
namespace App\Http\Resources;
  
use Illuminate\Http\Resources\Json\JsonResource;
  
class DriverReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'description' => $this->description,
            'driver_id' => $this->driver_id,
            'ride_id' => $this->ride_id,
          
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}