<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogVendorLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $emailArray = strpos($this->email, ',') !== false ? array_map('trim', explode(',', $this->email)) : $this->email;

        return [
            'vendor_id' => $this->id,
            'vendor_short_code' => $this->short_code,
            'vendor_name' => $this->name,
            'vendor_email' => $emailArray,
            'vast_vendor_number' => $this->vast_vendor_number,
            'store_location' => [
                'id' => $this->store_location_id,
                'addr' => $this->addr,
                'city' => $this->city,
                'state' =>$this->state,
                'zip_code' =>$this->zip_code,
                'lat' => $this->lat,
                'lon' => $this->lon,
                'phone' => $this->phone,
                'cut_off' => $this->cut_off
            ]
        ];
    }
}
