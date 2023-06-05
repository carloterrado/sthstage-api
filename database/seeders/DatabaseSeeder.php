<?php

namespace Database\Seeders;

use App\Http\Controllers\SettingsController;
use App\Models\CatalogSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $catalogs = ['id',
        'unq_id',
        'category',
        'sub_category',
        'discontinued',
        'legacy_brand',
        'brand',
        'mspn',
        'external_id',
        'external_id_type',
        'brand_id',
        'model',
        'manufacturer',
        'lt_p',
        'size_dimensions',
        'full_size',
        'full_bolt_patterns',
        'full_bolt_pattern_1',
        'full_bolt_pattern_2',
        'c_z_rated',
        'rft',
        'vast_description',
        'description',
        'long_description',
        'notes',
        'features',
        'install_time',
        'length_val',
        'section_width',
        'section_width_unit_id',
        'aspect_ratio',
        'rim_diameter',
        'rim_diameter_unit_id',
        'overall_diameter',
        'overall_diameter_unit_id',
        'weight_tire',
        'weight_tire_unit_id',
        'length_package',
        'length_unit_id',
        'width_package',
        'width_unit_id',
        'height_package',
        'height_unit_id',
        'weight_package',
        'weight_unit_id',
        'wheel_finish',
        'simple_finish',
        'side_wall_style',
        'load_index_1',
        'load_index_2',
        'speed_rating',
        'load_range',
        'load_rating',
        'back_spacing',
        'offset',
        'center_bore',
        'ply',
        'tread_depth',
        'tread_depth_unit_id',
        'rim_width',
        'rim_width_unit_id',
        'max_rim_width',
        'min_rim_width',
        'utqg',
        'tread_wear',
        'traction',
        'temperature',
        'warranty_type',
        'warranty_in_miles',
        'max_psi',
        'max_load_lb',
        'image_url_full',
        'image_url_quarter',
        'image_side',
        'image_url_tread',
        'image_kit_1',
        'image_kit_2',
        'season',
        'tire_type_performance',
        'car_type',
        'country',
        'quality_tier',
        'construction',
        'source',
        'oem_fitments',
        'status',
        'msct',
        'wheel_diameter',
        'wheel_width',
        'bolt_pattern_1',
        'bolt_circle_diameter_1',
        'bolt_pattern_2',
        'bolt_circle_diameter_2',
        ];


        foreach($catalogs as $catalog)
        {
            CatalogSettings::create([
                'catalog_key' => $catalog
            ]);
        };
    }
}
