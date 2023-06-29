@extends('layouts.mainlayout')

@section('title', 'Admin - Role Management')
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
                <h3 class="my-5">Role Management</h3>
                <div class="row my-5">
                    <div class="col-8">
                        <form action="{{ route('searchUser') }}" method="GET" class="d-flex" id="searchForm">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search" maxlength="20">
                        </form>
                    </div>
                    <div class="col-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="" class="btn btn-dark fw-semibold">FILTER</a>
                            <button type="button" class="btn btn-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#addUserModal">ADD</button>
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
                    {{-- . --}}
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="text-center"><b>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</b></td>
                            <td class="text-center">{{ ucfirst(strtolower($user->firstname)) . " " .  ucfirst(strtolower($user->lastname)) }}</td>
                            <td class="text-center">{{ strtolower($user->email) }}</td>
                            <td class="text-center">{{ ucfirst(strtolower($user->role)) }}</td>
                            <td class="text-center">
                                <button class="btn" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i class="fas fa-pencil-alt"></i></button>
                                @include('settings.userManagement.editUserModal')
                                <button class="btn" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}"><i class="fas fa-trash"></i></button>
                                @include('settings.userManagement.deleteUserModal')
                            </td class="text-center">
                        </tr>
                        @endforeach
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    @if ($users->previousPageUrl())
                    <a href="{{ $users->previousPageUrl() }}" class="rounded-pill btn btn-secondary" style="width: 150px;"><i class="fas fa-arrow-left"></i> Previous Page</a>
                    @endif

                    @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="rounded-pill fs-6 btn btn-secondary" style="width: 150px;">Next Page <i class="fas fa-arrow-right"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{ asset('js/user.js') }}"></script>
