<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogWheelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return 
        [
            // 'location' => [

            // ],
            'id' => $this->id,
            'unq_id' => $this->unq_id,
            'category' => $this->category,
            'sub_category' => $this->sub_category,
            'discontinued' => $this->discontinued,
            'legacy_brand' => $this->legacy_brand,
            'brand' => $this->brand,
            'mspn' => $this->mspn,
            'external_id' => $this->external_id,
            'external_id_type' => $this->external_id_type,
            'brand_id' => $this->brand_id,
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'lt_p' => $this->lt_p,
            'size_dimensions' => $this->size_dimensions,
            'full_size' => $this->full_size,
            'full_bolt_patterns' => $this->full_bolt_patterns,
            'full_bolt_pattern_1' => $this->full_bolt_pattern_1,
            'full_bolt_pattern_2' => $this->full_bolt_pattern_2,
            'c_z_rated' => $this->c_z_rated,
            'rft' => $this->rft,
            'vast_description' => $this->vast_description,
            'description' => $this->description,
            'long_description' => $this->long_description,
            'notes' => $this->notes,
            'features' => $this->features,
            'install_time' => $this->install_time,
            'length_val' => $this->length_val,
            'section_width_unit_id' => $this->section_width_unit_id,
            'rim_diameter_unit_id' => $this->rim_diameter_unit_id,
            'overall_diameter' => $this->overall_diameter,
            'overall_diameter_unit_id' => $this->overall_diameter_unit_id,
            'weight_tire' => $this->weight_tire,
            'weight_tire_unit_id' => $this->weight_tire_unit_id,
            'length_package' => $this->length_package,
            'length_unit_id' => $this->length_unit_id,
            'width_package' => $this->width_package,
            'width_unit_id' => $this->width_unit_id,
            'height_package' => $this->height_package,
            'height_unit_id' => $this->height_unit_id,
            'weight_package' => $this->weight_package,
            'weight_unit_id' => $this->weight_unit_id,
            'wheel_finish' => $this->wheel_finish,
            'simple_finish' => $this->simple_finish,
            'side_wall_style' => $this->side_wall_style,
            'load_index_1' => $this->load_index_1,
            'load_index_2' => $this->load_index_2,
            'speed_rating' => $this->speed_rating,
            'load_range' => $this->load_range,
            'load_rating' => $this->load_rating,
            'back_spacing' => $this->back_spacing,
            'offset' => $this->offset,
            'center_bore' => $this->center_bore,
            'ply' => $this->ply,
            'tread_depth' => $this->tread_depth,
            'tread_depth_unit_id' => $this->tread_depth_unit_id,
            'rim_width' => $this->rim_width,
            'rim_width_unit_id' => $this->rim_width_unit_id,
            'max_rim_width' => $this->max_rim_width,
            'min_rim_width' => $this->min_rim_width,
            'utqg' => $this->utqg,
            'tread_wear' => $this->tread_wear,
            'traction' => $this->traction,
            'temperature' => $this->temperature,
            'warranty_type' => $this->warranty_type,
            'warranty_in_miles' => $this->warranty_in_miles,
            'max_psi' => $this->max_psi,
            'max_load_lb' => $this->max_load_lb,
            'image_url_full' => $this->image_url_full,
            'image_url_quarter' => $this->image_url_quarter,
            'image_side' => $this->image_side,
            'image_url_tread' => $this->image_url_tread,
            'image_kit_1' => $this->image_kit_1,
            'image_kit_2' => $this->image_kit_2,
            'season' => $this->season,
            'tire_type_performance' => $this->tire_type_performance,
            'car_type' => $this->car_type,
            'country' => $this->country,
            'quality_tier' => $this->quality_tier,
            'construction' => $this->construction,
            'source' => $this->source,
            'oem_fitments' => $this->oem_fitments,
            'status' => $this->status,
            'msct' => $this->msct,
            'wheel_diameter' => $this->wheel_diameter,
            'wheel_width' => $this->wheel_width,
            'bolt_pattern_1' => $this->bolt_pattern_1,
            'bolt_circle_diameter_1' => $this->bolt_circle_diameter_1,
            'bolt_pattern_2' => $this->bolt_pattern_2,
            'bolt_circle_diameter_2' => $this->bolt_circle_diameter_2,
           
        ];
    }
}
