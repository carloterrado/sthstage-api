@extends('layouts.mainlayout')

@section('title', 'Admin - Role Setting')
@section('content')
    @include('settings.users.role-modal')

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
                    <h3 class="my-5">Role Setting</h3>
                    <div class="row my-5">
                        <div class="col-8">
                            <form action="{{ route('searchRole') }}" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search">
                                <button type="submit" class="btn btn-secondary">Search</button>
                            </form>
                        </div>
                        <div class="col-4">
                            <div class="d-flex justify-content-end">
                                {{-- <a href="" class="btn btn-dark me-3 d-flex align-items-center"><span
                                        class="material-symbols-outlined">
                                        filter_alt
                                    </span> FILTER</a>
                                <button class="btn btn-dark d-flex align-items-center" type="button" data-bs-toggle="modal" data-bs-target="#addRoleModal"><span
                                        class="material-symbols-outlined">
                                        add
                                    </span> ADD</button> --}}
                                <a href="" class="btn btn-dark me-3 d-flex align-items-center">FILTER</a>
                                <button class="btn btn-dark d-flex align-items-center" type="button" data-bs-toggle="modal"
                                    data-bs-target="#addRoleModal">ADD</button>
                            </div>
                        </div>
                    </div>

                    {{-- User Role --}}
                    @foreach ($roles as $role)
                        <div class="bg-white p-4 my-3 rounded-2 align-items-center d-flex justify-content-end">
                            <div class="col">
                                {{ ucfirst(strtolower($role->role)) }}
                            </div>
                            <div class="col-2 d-flex justify-content-end">
                                <a href="{{ route('user.column.settings', ['id' => $role->id]) }}"
                                    class="btn btn-secondary d-flex align-items-center me-2"><span
                                        class="material-symbols-outlined me-2">visibility
                                    </span><b> View Access</b></a>

                                @if ($role->id === 1 || $role->id === 2)
                                    <button class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#deleteRole{{ $role->id }}" disabled><i
                                            class="fas fa-trash"></i></button>
                                @else
                                    <button class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#deleteRole{{ $role->id }}"><i
                                            class="fas fa-trash"></i></button>
                                @endif

                                @include('settings.users.role-delete')
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
@endsection
