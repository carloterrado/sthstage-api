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
            'location_id' => $this->id,
            'address' => $this->addr,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'email' => $this->email,
            'phone' => $this->phone,
            'cut_off' => $this->cut_off,
            'TWIbranchCode' => $this->TWIbranchCode,
            'TWIshiptocode' => $this->TWIshiptocode,
            'NTWshiptocode' => $this->NTWshiptocode,
            'branch_code' => $this->branch_code
        ];

    }
}
