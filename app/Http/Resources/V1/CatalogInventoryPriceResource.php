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
        
        // $locations = $request->collection->groupBy('vendor_main_id')->map(function ($item) {
        //     $location = $item->first();
        //     $location['vendor_name'] = $location['name'] . ' - ' . $location['city'] . ', ' . $location['state'];
        //     $location['price'] = $location['netnet'];
        //     unset($location['name'], $location['city'], $location['state'], $location['netnet']);
        //     return $location;
        // })->values();

        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'mspn' => $this->mspn,
            // 'location' => $locations,

            'location' =>  [
                [
                    'vendor' => $this->vendor_main_id,
                    'vendor_name' => $this->name . ' ' . '-' . ' ' . $this->city . ',' . $this->state,
                    'price' => $this->netnet,
                    'qty' => $this->qty,
                ],
            ],

        ];
    }
}
