@extends('layouts.mainlayout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-2 bg-dark text-light side-bar">
                <div class="position-fixed top-0 start-0 bg-dark text-light side-bar">
                    <!-- Sidebar content goes here -->
                    @include('settings.settings')
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-10">
                <div class="container">
                    <h3 class="mt-4">User Management</h3>
                    <div class="row mb-5 mt-3">
                        <div class="col-6 d-flex align-items-center">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-symbols-outlined">search</i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" placeholder="Search">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end">
                                <a href="" class="btn btn-dark me-3 d-flex align-items-center"><span class="material-symbols-outlined">
                                    filter_alt
                                    </span> FILTER</a>
                                <a href="" class="btn btn-dark d-flex align-items-center"><span class="material-symbols-outlined">
                                    add
                                    </span> ADD</a>
                            </div>
                        </div>
                    </div>

                    {{-- User List --}}
                    <div class="row me-2 ms-1">
                        <div class="col-10">
                            <h5>User</h5>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <h5>Action</h5>
                        </div>
                    </div>
                    @foreach ($users as $user)
                        <div class="row bg-white p-3 my-2 rounded-2 align-items-center">
                            <div class="col">
                                {{ $user->name }}
                            </div>
                            <div class="col d-flex justify-content-end">
                                <a href="{{ route('user.column.settings', ['id' => $user->id]) }}"><button type="button"
                                        class="btn btn-secondary">Edit Access</button></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection