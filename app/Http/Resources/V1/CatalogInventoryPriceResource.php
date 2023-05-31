<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogInventoryPriceResource extends JsonResource
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
            'brand' => $this->brand,
            'mspn' => $this->mspn,
            // 'vendor_main_id' => $this->vendor_main_id,

            'location' => [
                'vendor' => $this->vendor,
                'vendor_name' => $this->name,
                'netnet' => $this->netnet,
                'qty' => $this->qty,
            ],


        ];
    }
}
