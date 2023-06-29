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
         $formattedData = [];
     
         foreach ($this->resource as $data) {
             // Check if data with the same brand and part number already exists
             $existingDataKey = null;
             foreach ($formattedData as $key => $existingData) {
                 if ($existingData['brand'] === $data->brand && $existingData['part_number'] === $data->part_number) {
                     $existingDataKey = $key;
                     break;
                 }
             }
     
             if ($existingDataKey === null) {
                 // If no existing data found, create a new entry
                 $formattedData[] = [
                     'brand' => $data->brand,
                     'part_number' => $data->part_number,
                     'price' => $data->selling_price,
                     'qty' => $data->qty,
                 ];
             } else {
                 // If existing data found, update the quantity and price
                 $formattedData[$existingDataKey]['qty'] += $data->qty;
                 $formattedData[$existingDataKey]['price'] = $data->selling_price;
             }
         }
     
         return $formattedData;
     }
     
}
