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
                    <h3 class="my-5">User Management</h3>
                    <div class="row my-5">
                        <div class="col-8">
                            <input type="text" class="form-control" placeholder="Search">
                        </div>
                        <div class="col-4">
                            <div class="d-flex justify-content-end">
                                <a href="" class="btn btn-dark me-3">FILTER</a>
                                <a href="" class="btn btn-dark">ADD</a>
                            </div>
                        </div>
                    </div>

                    {{-- User List --}}
                    @foreach ($users as $user)
                    <div class="bg-white p-4 my-3 rounded-2 align-items-center d-flex justify-content-end">
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

