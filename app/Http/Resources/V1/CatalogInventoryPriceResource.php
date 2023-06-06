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

            //pass sa $existingdata array kapag may duplicated vendor_main_id value
            $existingData = array_filter($formattedData, function ($combinedData) use ($data) {
                return $combinedData['vendor_main_id'] === $data->vendor_main_id;
            });

        
            if (empty($existingData)) {

                //add ganto pag walang laman si $existingdata
                $formattedData[] = [
                    'brand' => $data->brand,
                    'mspn' => $data->part_number,
                    'vendor_main_id' => $data->vendor_main_id,
                    'location' => [
                        [
                            'store_location_id' => $data->store_location_id,
                            'price' => $data->netnet,
                            'qty' => $data->qty,
                        ],
                    ],
                ];

            } else {

                //add ganto pag meron laman
                $formattedData[array_key_first($existingData)]['location'][] = [
                    'store_location_id' => $data->store_location_id,
                    'price' => $data->netnet,
                    'qty' => $data->qty,
                ];

            }

        }


        //remove vendor_main_id sa response
        $responseData = array_map(function ($data) {
            unset($data['vendor_main_id']);
            return $data;
        }, $formattedData);
        
        
        return $responseData;

    }
}
