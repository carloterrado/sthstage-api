@extends('layouts.mainlayout')
@section('content')
    <form method="post" action="{{ route('update.user.catalog.column.settings', ['user_id' => $catalog['user_id']]) }}">
        @csrf
        <section class="intro d-flex align-items-center justify-content-center min-">
            <div class="container mb-5">
                <h2 id="example" class="mt-2">User Catalog Access </h2>
                <div class="bd-example">
                    @if (session('error_message'))
                        <div class="alert alert-danger">
                            {{ session('error_message') }}
                        </div>
                    @endif
                    <div class="row">
                        {{-- column 1 --}}
                        <div class="col-md-3">
                            @foreach ($columns as $column)
                                @if ($column === 'notes')
                                @break
                            @endif
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" value="{{ $column }}" name="column[]"
                                        {{ in_array($column, json_decode($catalog['catalog_column_settings'])) ? 'checked' : '' }}
                                        type="checkbox" role="switch">
                                    <label class="form-check-label">{{ $column }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- column 2 --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="notes" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">notes</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="features" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">features</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="install_time" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">install_time</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="length_val" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">length_val</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="section_width" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">section_width</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="section_width_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">section_width_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="aspect_ratio" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">aspect_ratio</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="rim_diameter" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">rim_diameter</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="rim_diameter_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">rim_diameter_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="overall_diameter" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">overall_diameter</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="overall_diameter_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">overall_diameter_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="weight_tire" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">weight_tire</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="weight_tire_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">weight_tire_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="length_package" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">length_package</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="length_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">length_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="width_package" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">width_package</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="width_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">width_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="height_package" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">height_package</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="height_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">height_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="weight_package" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">weight_package</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="weight_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">weight_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="wheel_finish" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">wheel_finish</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="simple_finish" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">simple_finish</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="side_wall_style" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">side_wall_style</label>
                            </div>
                        </div>
                    </div>
                    {{-- column 3 --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="load_index_1" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">load_index_1</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="load_index_2" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">load_index_2</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="speed_rating" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">speed_rating</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="load_range" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">load_range</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="load_rating" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">load_rating</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="back_spacing" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">back_spacing</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="offset" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">offset</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="center_bore" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">center_bore</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="ply" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">ply</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="tread_depth" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">tread_depth</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="tread_depth_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">tread_depth_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="rim_width" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">rim_width</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="rim_width_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">rim_width_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="max_rim_width" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">max_rim_width</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="max_rim_width_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">max_rim_width_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="min_rim_width" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">min_rim_width</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="min_rim_width_unit_id" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">min_rim_width_unit_id</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="utqg" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">utqg</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="tread_wear" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">tread_wear</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="traction" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">traction</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="temperature" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">temperature</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="warranty_type" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">warranty_type</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="warranty_in_miles" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">warranty_in_miles</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="max_psi" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">max_psi</label>
                            </div>
                        </div>
                    </div>
                    {{-- column 4 --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="max_load_lb" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">max_load_lb</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="image_url_full" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">image_url_full</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="image_url_quarter" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">image_url_quarter</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="image_side" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">image_side</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="image_url_tread" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">image_url_tread</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="image_kit_1" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">image_kit_1</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="image_kit_2" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">image_kit_2</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="season" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">season</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="tire_type_performance" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">tire_type_performance</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="car_type" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">car_type</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="country" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">country</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="quality_tier" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">quality_tier</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="construction" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">construction</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="source" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">source</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="oem_fitments" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">oem_fitments</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="status" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">status</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="msct" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">msct</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="wheel_diameter" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">wheel_diameter</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="wheel_width" name="column[]" type="checkbox"
                                    role="switch">
                                <label class="form-check-label">wheel_width</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="bolt_pattern_1" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">bolt_pattern_1</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="bolt_circle_diameter_1" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">bolt_circle_diameter_1</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="bolt_pattern_2" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">bolt_pattern_2</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="bolt_circle_diameter_2" name="column[]"
                                    type="checkbox" role="switch">
                                <label class="form-check-label">bolt_circle_diameter_2</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row my-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="Submit">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>
@endsection
