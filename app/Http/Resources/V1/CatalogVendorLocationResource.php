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


    public function toExplode($var){
        $toArray = strpos($var, ',') !== false ? array_map('trim', explode(',', $var)) : $var;

        return $toArray;
    }
    public function toArray($request)
    {
        return [
            'vendor_id' => $this->id,
            'vendor_short_code' => $this->short_code,
            'vendor_name' => $this->name,
            'vendor_email' => $this->toExplode($this->email),
            'store_location' => $this->toExplode($this->store_locations),
        ];

    }
}
