<!DOCTYPE html>
<html>

<head>
    <title>Excel File Uploader</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header h2">Excel Uploader</div>
                    <div class="card-body">
                        <div class="container mt-4">
                            <div class="row justify-content-between">
                                <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="input-group">
                                        <input class="form-control" type="file" name="excel_file" accept=".csv,.xls,.xlsx">
                                        <button type="submit" class="btn btn-warning">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @if(isset($empty))
                        <p class="text-center fs-3 mt-4">{{ $empty }}</p>
                        @else
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered text-justify">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>unq_id</th>
                                        <th>category</th>
                                        <th>sub_category</th>
                                        <th>discontinued</th>
                                        <th>legacy_brand</th>
                                        <th>brand</th>
                                        <th>mspn</th>
                                        <th>external_id</th>
                                        <th>external_id_type</th>
                                        <th>brand_id</th>
                                        <th>model</th>
                                        <th>manufacturer</th>
                                        <th>lt_p</th>
                                        <th>size_dimensions</th>
                                        <th>full_size</th>
                                        <th>full_bolt_patterns</th>
                                        <th>full_bolt_pattern_1</th>
                                        <th>full_bolt_pattern_2</th>
                                        <th>c_z_rated</th>
                                        <th>rft</th>
                                        <th>vast_description</th>
                                        <th>description</th>
                                        <th>long_description</th>
                                        <th>notes</th>
                                        <th>features</th>
                                        <th>install_time</th>
                                        <th>length_val</th>
                                        <th>section_width</th>
                                        <th>section_width_unit_id</th>
                                        <th>aspect_ratio</th>
                                        <th>rim_diameter</th>
                                        <th>rim_diameter_unit_id</th>
                                        <th>overall_diameter</th>
                                        <th>overall_diameter_unit_id</th>
                                        <th>weight_tire</th>
                                        <th>weight_tire_unit_id</th>
                                        <th>length_package</th>
                                        <th>length_unit_id</th>
                                        <th>width_package</th>
                                        <th>width_unit_id</th>
                                        <th>height_package</th>
                                        <th>height_unit_id</th>
                                        <th>weight_package</th>
                                        <th>weight_unit_id</th>
                                        <th>wheel_finish</th>
                                        <th>simple_finish</th>
                                        <th>side_wall_style</th>
                                        <th>load_index_1</th>
                                        <th>load_index_2</th>
                                        <th>speed_rating</th>
                                        <th>load_range</th>
                                        <th>load_rating</th>
                                        <th>back_spacing</th>
                                        <th>offset</th>
                                        <th>center_bore</th>
                                        <th>ply</th>
                                        <th>tread_depth</th>
                                        <th>tread_depth_unit_id</th>
                                        <th>rim_width</th>
                                        <th>rim_width_unit_id</th>
                                        <th>max_rim_width</th>
                                        <th>max_rim_width_unit_id</th>
                                        <th>min_rim_width</th>
                                        <th>min_rim_width_unit_id</th>
                                        <th>utqg</th>
                                        <th>tread_wear</th>
                                        <th>traction</th>
                                        <th>warranty_type</th>
                                        <th>warranty_in_miles</th>
                                        <th>max_psi</th>
                                        <th>max_load_lb</th>
                                        <th>image_url_full</th>
                                        <th>image_url_quarter</th>
                                        <th>image_side</th>
                                        <th>image_url_tread</th>
                                        <th>image_kit_1</th>
                                        <th>image_kit_2</th>
                                        <th>season</th>
                                        <th>tire_type_performance</th>
                                        <th>car_type</th>
                                        <th>country</th>
                                        <th>quality_tier</th>
                                        <th>construction</th>
                                        <th>source</th>
                                        <th>oem_fitments</th>
                                        <th>status</th>
                                        <th>msct</th>
                                        <th>wheel_diameter</th>
                                        <th>wheel_width</th>
                                        <th>bolt_pattern_1</th>
                                        <th>bolt_circle_diameter_1</th>
                                        <th>bolt_pattern_2</th>
                                        <th>bolt_circle_diameter_2</th>
                                        <th>created_at</th>
                                        <th>updated_at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $catalog)
                                    <tr>
                                        <td>{{ $catalog['id'] }}</td>
                                        <td class="text-truncate ellipsis">{{ $catalog['unq_id'] }}</td>
                                        <td>{{ $catalog['category'] }}</td>
                                        <td>{{ $catalog['sub_category'] }}</td>
                                        <td>{{ $catalog['discontinued'] }}</td>
                                        <td>{{ $catalog['legacy_brand'] }}</td>
                                        <td>{{ $catalog['brand'] }}</td>
                                        <td class="text-truncate ellipsis">{{ $catalog['mspn'] }}</td>
                                        <td>{{ $catalog['external_id'] }}</td>
                                        <td>{{ $catalog['external_id_type'] }}</td>
                                        <td>{{ $catalog['brand_id'] }}</td>
                                        <td class="text-truncate ellipsis">{{ $catalog['model'] }}</td>
                                        <td>{{ $catalog['manufacturer'] }}</td>
                                        <td>{{ $catalog['lt_p'] }}</td>
                                        <td>{{ $catalog['size_dimensions'] }}</td>
                                        <td>{{ $catalog['full_size'] }}</td>
                                        <td>{{ $catalog['full_bolt_patterns'] }}</td>
                                        <td>{{ $catalog['full_bolt_pattern_1'] }}</td>
                                        <td>{{ $catalog['full_bolt_pattern_2'] }}</td>
                                        <td>{{ $catalog['c_z_rated'] }}</td>
                                        <td>{{ $catalog['rft'] }}</td>
                                        <td>{{ $catalog['vast_description'] }}</td>
                                        <td class="text-truncate ellipsis" style="max-width: 250px;">{{ $catalog['description'] }}</td>
                                        <td class="text-truncate ellipsis" style="max-width: 250px;">{{ $catalog['long_description'] }}</td>
                                        <td>{{ $catalog['notes'] }}</td>
                                        <td>{{ $catalog['features'] }}</td>
                                        <td>{{ $catalog['install_time'] }}</td>
                                        <td>{{ $catalog['length_val'] }}</td>
                                        <td>{{ $catalog['section_width'] }}</td>
                                        <td>{{ $catalog['section_width_unit_id'] }}</td>
                                        <td>{{ $catalog['aspect_ratio'] }}</td>
                                        <td>{{ $catalog['rim_diameter'] }}</td>
                                        <td>{{ $catalog['rim_diameter_unit_id'] }}</td>
                                        <td>{{ $catalog['overall_diameter'] }}</td>
                                        <td>{{ $catalog['overall_diameter_unit_id'] }}</td>
                                        <td>{{ $catalog['weight_tire'] }}</td>
                                        <td>{{ $catalog['weight_tire_unit_id'] }}</td>
                                        <td>{{ $catalog['length_package'] }}</td>
                                        <td>{{ $catalog['length_unit_id'] }}</td>
                                        <td>{{ $catalog['width_package'] }}</td>
                                        <td>{{ $catalog['width_unit_id'] }}</td>
                                        <td>{{ $catalog['height_package'] }}</td>
                                        <td>{{ $catalog['height_unit_id'] }}</td>
                                        <td>{{ $catalog['weight_package'] }}</td>
                                        <td>{{ $catalog['weight_unit_id'] }}</td>
                                        <td class="text-truncate ellipsis">{{ $catalog['wheel_finish'] }}</td>
                                        <td>{{ $catalog['simple_finish'] }}</td>
                                        <td>{{ $catalog['side_wall_style'] }}</td>
                                        <td>{{ $catalog['load_index_1'] }}</td>
                                        <td>{{ $catalog['load_index_2'] }}</td>
                                        <td>{{ $catalog['speed_rating'] }}</td>
                                        <td>{{ $catalog['load_range'] }}</td>
                                        <td>{{ $catalog['load_rating'] }}</td>
                                        <td>{{ $catalog['back_spacing'] }}</td>
                                        <td>{{ $catalog['offset'] }}</td>
                                        <td>{{ $catalog['center_bore'] }}</td>
                                        <td>{{ $catalog['ply'] }}</td>
                                        <td>{{ $catalog['tread_depth'] }}</td>
                                        <td>{{ $catalog['tread_depth_unit_id'] }}</td>
                                        <td>{{ $catalog['rim_width'] }}</td>
                                        <td>{{ $catalog['rim_width_unit_id'] }}</td>
                                        <td>{{ $catalog['max_rim_width'] }}</td>
                                        <td>{{ $catalog['max_rim_width_unit_id'] }}</td>
                                        <td>{{ $catalog['min_rim_width'] }}</td>
                                        <td>{{ $catalog['min_rim_width_unit_id'] }}</td>
                                        <td>{{ $catalog['utqg'] }}</td>
                                        <td>{{ $catalog['tread_wear'] }}</td>
                                        <td>{{ $catalog['traction'] }}</td>
                                        <td>{{ $catalog['warranty_type'] }}</td>
                                        <td>{{ $catalog['warranty_in_miles'] }}</td>
                                        <td>{{ $catalog['max_psi'] }}</td>
                                        <td>{{ $catalog['max_load_lb'] }}</td>
                                        <td>{{ $catalog['image_url_full'] }}</td>
                                        <td>{{ $catalog['image_url_quarter'] }}</td>
                                        <td>{{ $catalog['image_side'] }}</td>
                                        <td>{{ $catalog['image_url_tread'] }}</td>
                                        <td>{{ $catalog['image_kit_1'] }}</td>
                                        <td>{{ $catalog['image_kit_2'] }}</td>
                                        <td>{{ $catalog['season'] }}</td>
                                        <td>{{ $catalog['tire_type_performance'] }}</td>
                                        <td>{{ $catalog['car_type'] }}</td>
                                        <td>{{ $catalog['country'] }}</td>
                                        <td>{{ $catalog['quality_tier'] }}</td>
                                        <td>{{ $catalog['construction'] }}</td>
                                        <td>{{ $catalog['source'] }}</td>
                                        <td>{{ $catalog['oem_fitments'] }}</td>
                                        <td>{{ $catalog['status'] }}</td>
                                        <td>{{ $catalog['msct'] }}</td>
                                        <td>{{ $catalog['wheel_diameter'] }}</td>
                                        <td>{{ $catalog['wheel_width'] }}</td>
                                        <td>{{ $catalog['bolt_pattern_1'] }}</td>
                                        <td>{{ $catalog['bolt_circle_diameter_1'] }}</td>
                                        <td>{{ $catalog['bolt_pattern_2'] }}</td>
                                        <td>{{ $catalog['bolt_circle_diameter_2'] }}</td>
                                        <td class="text-truncate ellipsis">{{ $catalog['created_at'] }}</td>
                                        <td class="text-truncate ellipsis">{{ $catalog['updated_at'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-4">
                            @if ($rows->previousPageUrl())
                            <a href="{{ $rows->previousPageUrl() }}" class="btn btn-primary">Previous Page</a>
                            @endif

                            @if ($rows->hasMorePages())
                            <a href="{{ $rows->nextPageUrl() }}" class="btn btn-primary">Next Page</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>