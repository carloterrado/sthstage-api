@extends('layouts.mainlayout')

{{-- @section('content')
    <div class="container">
        <h3>Users</h3>
        <table class="table table-borderless">
            @if (session('success_message'))
                <div class="alert alert-success">
                    {{ session('success_message') }}
                </div>
            @endif
            <thead class="sticky-top">
                <tr class="">
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($users as $user)
                <div class="my-2">
                    <tr class="bg-white">
                        <th scope="row">{{ $user->id }}</th>
                        <td>{{ $user->firstname . ' ' . $user->lastname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->status }}</td>
                        <td><a href="{{ route('user.column.settings', ['user_id' => $user->id]) }}"><button type="button"
                                    class="btn btn-primary">Edit Access</button></a></td>
                    </tr>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection --}}

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-2 bg-secondary">
                @include('settings.settings')
            </div>

            <div class="col-10">
                <div class="container my-3">
                    <h3>Users</h3>
                    <div class="row sticky-top bg-light px-3 py-2 align-items-center">
                        <div class="col-1">
                            <b>
                                <h6>#</h6>
                            </b>
                        </div>
                        <div class="col-4">
                            <b>
                                <h6>Name</h6>
                            </b>
                        </div>
                        <div class="col-4">
                            <b>
                                <h6>Email</h6>
                            </b>
                        </div>
                        <div class="col-1">
                            <b>
                                <h6>Status</h6>
                            </b>
                        </div>
                        <div class="col-2">
                            <b>
                                <h6>Action</h6>
                            </b>
                        </div>
                    </div>

                    @foreach ($users as $user)
                        <div class="row bg-white p-3 my-2 rounded-2 align-items-center">
                            <div class="col-1">
                                {{ $user->id }}
                            </div>
                            <div class="col-4">
                                {{ $user->firstname . ' ' . $user->lastname }}
                            </div>
                            <div class="col-4">
                                {{ $user->email }}
                            </div>
                            <div class="col-1">
                                {{ $user->status }}
                            </div>
                            <div class="col-2 text-center">
                                {{-- <a href="{{ route('user.column.settings', ['user_id' => $user->id]) }}"><button type="button"
                            class="btn btn-secondary form-control">Edit Access</button></a> --}}
                                <button type="button" class="btn btn-secondary form-control" data-bs-toggle="modal"
                                    data-bs-target="#myModal-{{ $user->id }}">
                                    Edit Access
                                </button>
                                @include('settings.users.user-modal')
                            </div>
                        </div>
                       
                    @endforeach

                </div>
            </div>
@section('content')
    <div class="container ">
        <div class="intro d-flex align-items-center justify-content-center">
            <table class="table table-bordered">
                @if (session('success_message'))
                    <div class="alert alert-success">
                        {{ session('success_message') }}
                    </div>
                @endif
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td><a href="{{ route('user.column.settings', ['id' => $user->id]) }}"><button type="button"
                                        class="btn btn-primary">Edit Access</button></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
