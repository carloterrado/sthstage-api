@extends('layouts.mainlayout')

@section('content')

@include('settings.userManagement.addUserModal')

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-2 bg-dark text-light side-bar">
            <div class="position-fixed top-0 start-0 bg-secondary text-light side-bar">
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
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addUserModal">ADD</button>
                        </div>
                    </div>
                </div>

                {{-- User List --}}
                <table class="table table-hover table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="text-center"><b>{{ $loop->iteration }}</b></td>
                                <td class="text-center">{{ $user->firstname . ' ' . $user->lastname }}</td>
                                <td class="text-center">{{ $user->email }}</td>
                                <td class="text-center">{{ $user->role }}</td>
                                <td class="text-center">
                                    <button class="btn"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn"><i class="fas fa-trash"></i></button>
                                </t class="text-center"d>
                            </tr>
                        @endforeach
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection