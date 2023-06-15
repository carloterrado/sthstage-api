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
            // Check if data with the same brand and mspn already exists
            $existingDataKey = null;
            foreach ($formattedData as $key => $existingData) {
                if ($existingData['brand'] === $data->brand && $existingData['mspn'] === $data->part_number) {
                    $existingDataKey = $key;
                    break;
                }
            }

            if ($existingDataKey === null) {
                // If no existing data found, create a new entry
                $formattedData[] = [
                    'brand' => $data->brand,
                    'mspn' => $data->part_number,
                    'location' => [
                        [
                            'store_location_id' => $data->store_location_id,
                            'price' => $data->netnet,
                            'qty' => $data->qty,
                        ],
                    ],
                ];
            } else {
                // If existing data found, append to the existing location array
                $formattedData[$existingDataKey]['location'][] = [
                    'store_location_id' => $data->store_location_id,
                    'price' => $data->netnet,
                    'qty' => $data->qty,
                ];
            }
        }

        // Sort the "location" array in descending order based on "store_location_id"
        foreach ($formattedData as &$data) {
            usort($data['location'], function ($a, $b) {
                return $a['store_location_id'] - $b['store_location_id'];
            });
        }

        return $formattedData;
    }
}
