@extends('layouts.mainlayout')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <div class="container-fluid">
        <div class="row">
            <div class="col-2 bg-dark text-light side-bar">
                <div class="position-absolute top-0 start-0 bg-dark text-light side-bar">
                    <!-- Sidebar content goes here -->
                    @include('settings.settings')
                </div>
            </div>
            <div class="col-10">
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session('match'))
                                <div class="alert alert-success">
                                    {{ session('match') }}
                                </div>
                            @endif
                            <div class="modal fade" id="filterModal" tabindex="-1" role="dialog"
                                aria-labelledby="filterModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="filterModalLabel">Toggle Columns</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="text" id="columnSearch" class="form-control mb-2"
                                                placeholder="Search columns">
                                            @foreach ($columns as $column)
                                                <div class="form-check column-check">
                                                    <input type="checkbox" name="columns[]" value="{{ $column }}"
                                                        class="form-check-input column-toggle">
                                                    <label class="form-check-label">{{ $column }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="container mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="h2 w-50">ADD CATALOG</div>
                                            <div class="input-group d-grid gap-4 d-md-flex justify-content-md-end">
                                                <form method="POST" id="myForm" action="{{ route('import') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <input id="file_input" style="display:none" type="file"
                                                        name="excel_file" accept=".csv,.xls,.xlsx">
                                                    <button type="button" onclick="selectFile()"
                                                        class="rounded-pill p-2 fs-6 btn btn-secondary"
                                                        style="width: 250px;"><i class="fa-solid fa-file-arrow-up"></i>
                                                        UPLOAD A NEW FILE</button>
                                                </form>
                                            </div>
                                        </div>
                                        <button class="p-2 mt-2 btn btn-secondary" style="width: 150px;" data-toggle="modal"
                                            data-target="#filterModal"><i class="fa fa-filter" aria-hidden="true"></i>
                                            FILTER</button>
                                    </div>
                                    @if (isset($empty))
                                        <p class="text-center fs-3 mt-4">{{ $empty }}</p>
                                    @else
                                        <div class="h-75 table-responsive mt-4">
                                            <table class="vh-100 table table-bordered border-dark text-justify">
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
                                                    @foreach ($rows as $catalog)
                                                        <tr>
                                                            <td>{{ $catalog->id }}</td>
                                                            <td class="text-truncate ellipsis">{{ $catalog->unq_id }}</td>
                                                            <td>{{ $catalog->category }}</td>
                                                            <td>{{ $catalog->sub_category }}</td>
                                                            <td>{{ $catalog->discontinued }}</td>
                                                            <td>{{ $catalog->legacy_brand }}</td>
                                                            <td>{{ $catalog->brand }}</td>
                                                            <td class="text-truncate ellipsis">{{ $catalog->mspn }}</td>
                                                            <td>{{ $catalog->external_id }}</td>
                                                            <td>{{ $catalog->external_id_type }}</td>
                                                            <td>{{ $catalog->brand_id }}</td>
                                                            <td class="text-truncate ellipsis">{{ $catalog->model }}</td>
                                                            <td>{{ $catalog->manufacturer }}</td>
                                                            <td>{{ $catalog->lt_p }}</td>
                                                            <td>{{ $catalog->size_dimensions }}</td>
                                                            <td>{{ $catalog->full_size }}</td>
                                                            <td>{{ $catalog->full_bolt_patterns }}</td>
                                                            <td>{{ $catalog->full_bolt_pattern_1 }}</td>
                                                            <td>{{ $catalog->full_bolt_pattern_2 }}</td>
                                                            <td>{{ $catalog->c_z_rated }}</td>
                                                            <td>{{ $catalog->rft }}</td>
                                                            <td>{{ $catalog->vast_description }}</td>
                                                            <td class="text-truncate ellipsis" style="max-width: 250px;">
                                                                {{ $catalog->description }}</td>
                                                            <td class="text-truncate ellipsis" style="max-width: 250px;">
                                                                {{ $catalog->long_description }}</td>
                                                            <td>{{ $catalog->notes }}</td>
                                                            <td>{{ $catalog->features }}</td>
                                                            <td>{{ $catalog->install_time }}</td>
                                                            <td>{{ $catalog->length_val }}</td>
                                                            <td>{{ $catalog->section_width }}</td>
                                                            <td>{{ $catalog->section_width_unit_id }}</td>
                                                            <td>{{ $catalog->aspect_ratio }}</td>
                                                            <td>{{ $catalog->rim_diameter }}</td>
                                                            <td>{{ $catalog->rim_diameter_unit_id }}</td>
                                                            <td>{{ $catalog->overall_diameter }}</td>
                                                            <td>{{ $catalog->overall_diameter_unit_id }}</td>
                                                            <td>{{ $catalog->weight_tire }}</td>
                                                            <td>{{ $catalog->weight_tire_unit_id }}</td>
                                                            <td>{{ $catalog->length_package }}</td>
                                                            <td>{{ $catalog->length_unit_id }}</td>
                                                            <td>{{ $catalog->width_package }}</td>
                                                            <td>{{ $catalog->width_unit_id }}</td>
                                                            <td>{{ $catalog->height_package }}</td>
                                                            <td>{{ $catalog->height_unit_id }}</td>
                                                            <td>{{ $catalog->weight_package }}</td>
                                                            <td>{{ $catalog->weight_unit_id }}</td>
                                                            <td class="text-truncate ellipsis">{{ $catalog->wheel_finish }}
                                                            </td>
                                                            <td>{{ $catalog->simple_finish }}</td>
                                                            <td>{{ $catalog->side_wall_style }}</td>
                                                            <td>{{ $catalog->load_index_1 }}</td>
                                                            <td>{{ $catalog->load_index_2 }}</td>
                                                            <td>{{ $catalog->speed_rating }}</td>
                                                            <td>{{ $catalog->load_range }}</td>
                                                            <td>{{ $catalog->load_rating }}</td>
                                                            <td>{{ $catalog->back_spacing }}</td>
                                                            <td>{{ $catalog->offset }}</td>
                                                            <td>{{ $catalog->center_bore }}</td>
                                                            <td>{{ $catalog->ply }}</td>
                                                            <td>{{ $catalog->tread_depth }}</td>
                                                            <td>{{ $catalog->tread_depth_unit_id }}</td>
                                                            <td>{{ $catalog->rim_width }}</td>
                                                            <td>{{ $catalog->rim_width_unit_id }}</td>
                                                            <td>{{ $catalog->max_rim_width }}</td>
                                                            <td>{{ $catalog->max_rim_width_unit_id }}</td>
                                                            <td>{{ $catalog->min_rim_width }}</td>
                                                            <td>{{ $catalog->min_rim_width_unit_id }}</td>
                                                            <td>{{ $catalog->utqg }}</td>
                                                            <td>{{ $catalog->tread_wear }}</td>
                                                            <td>{{ $catalog->traction }}</td>
                                                            <td>{{ $catalog->warranty_type }}</td>
                                                            <td>{{ $catalog->warranty_in_miles }}</td>
                                                            <td>{{ $catalog->max_psi }}</td>
                                                            <td>{{ $catalog->max_load_lb }}</td>
                                                            <td>{{ $catalog->image_url_full }}</td>
                                                            <td>{{ $catalog->image_url_quarter }}</td>
                                                            <td>{{ $catalog->image_side }}</td>
                                                            <td>{{ $catalog->image_url_tread }}</td>
                                                            <td>{{ $catalog->image_kit_1 }}</td>
                                                            <td>{{ $catalog->image_kit_2 }}</td>
                                                            <td>{{ $catalog->season }}</td>
                                                            <td>{{ $catalog->tire_type_performance }}</td>
                                                            <td>{{ $catalog->car_type }}</td>
                                                            <td>{{ $catalog->country }}</td>
                                                            <td>{{ $catalog->quality_tier }}</td>
                                                            <td>{{ $catalog->construction }}</td>
                                                            <td>{{ $catalog->source }}</td>
                                                            <td>{{ $catalog->oem_fitments }}</td>
                                                            <td>{{ $catalog->status }}</td>
                                                            <td>{{ $catalog->msct }}</td>
                                                            <td>{{ $catalog->wheel_diameter }}</td>
                                                            <td>{{ $catalog->wheel_width }}</td>
                                                            <td>{{ $catalog->bolt_pattern_1 }}</td>
                                                            <td>{{ $catalog->bolt_circle_diameter_1 }}</td>
                                                            <td>{{ $catalog->bolt_pattern_2 }}</td>
                                                            <td>{{ $catalog->bolt_circle_diameter_2 }}</td>
                                                            <td class="text-truncate ellipsis">{{ $catalog->created_at }}
                                                            </td>
                                                            <td class="text-truncate ellipsis">{{ $catalog->updated_at }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                            @if ($rows->previousPageUrl())
                                                <a href="{{ $rows->previousPageUrl() }}"
                                                    class="rounded-pill btn btn-secondary" style="width: 150px;"><i
                                                        class="fas fa-arrow-left"></i> Previous Page</a>
                                            @endif

                                            <button class="rounded-pill fs-6 btn btn-secondary" type="button"
                                                id="pageDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                                style="width: 100px;">
                                                Page {{ $rows->currentPage() }}
                                            </button>

                                            @if ($rows->hasMorePages())
                                                <a href="{{ $rows->nextPageUrl() }}"
                                                    class="rounded-pill fs-6 btn btn-secondary" style="width: 150px;">Next
                                                    Page <i class="fas fa-arrow-right"></i></a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function selectFile() {
            document.getElementById('file_input').click();
        }

        document.getElementById('file_input').addEventListener('change', function() {
            document.getElementById('myForm').submit();
        });

        $(document).ready(function() {
            // Update hidden column input when checkboxes are toggled
            $('.column-toggle').change(function() {
                hideColumns();
            });

            // Filter columns based on search input
            $('#columnSearch').keyup(function() {
                filterColumns();
            });

            // Update hidden column input on modal close
            $('#filterModal').on('hidden.bs.modal', function() {
                hideColumns();
            });

            // Function to hide columns
            function hideColumns() {
                $('.table th, .table td').each(function() {
                    var columnText = $(this).text().trim().toLowerCase();
                    var isHidden = $('.column-toggle[value="' + columnText + '"]').is(':checked');
                    $(this).toggle(!isHidden);
                    $('td:nth-child(' + ($(this).index() + 1) + ')').toggle(!isHidden);
                });
            }

            // Function to filter columns based on search input
            function filterColumns() {
                var searchValue = $('#columnSearch').val().trim().toLowerCase();
                $('.table th').each(function() {
                    var columnText = $(this).text().trim().toLowerCase();
                    var isHidden = $('.column-toggle[value="' + columnText + '"]').is(':checked');
                    if (searchValue === '' || columnText.includes(searchValue)) {
                        $(this).toggle(!isHidden);
                        $('td:nth-child(' + ($(this).index() + 1) + ')').toggle(!isHidden);
                    } else {
                        $(this).hide();
                        $('td:nth-child(' + ($(this).index() + 1) + ')').hide();
                    }
                });
            }

            // Initialize column visibility on page load
            hideColumns();
        });
    </script>

@endsection
