{{-- c_z_rated, length_unit_id, tread_depth, image_url_tread, --}}

{{-- full_bolt_patterns, rim_diameter, simple_finish, max_rim_width_unit_id, season, bolt_circle_diameter_2 --}}

@extends('layouts.mainlayout')

@section('title', 'Admin - API Access Authorization')
@section('content')

    <div class="d-flex">
        <div class="col-2 bg-dark text-light side-bar">
            <!-- Sidebar content goes here -->
            @include('settings.settings')
        </div>
        <div class="col-10">
            <div class="center-form p-4">
                <h2 id="example" class="mt-2">API Access Authorization</h2>
                <h5> {{ ucfirst(strtolower($role->role)) }}</h5>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-secondary fw-medium" data-bs-toggle="modal" data-bs-target="#catalogModal">
                        DISABLE CATALOG COLUMNS
                    </button>
                    <button type="button" class="btn btn-secondary fw-medium">
                        VENDOR LOCATION CONTROLLER
                    </button>
                    <button type="button" class="btn btn-secondary fw-medium">
                        ENDPOINTS
                    </button>
                </div>
                @include('settings.users.modal.catalog')
            </div>
        </div>
    </div>
@endsection