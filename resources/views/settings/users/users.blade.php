@extends('layouts.mainlayout')

@section('content')
    <div class="container">
        <div class="sticky-top">
            <h3>Users</h3>
            <div class="row px-3">
                <div class="col">
                    <b>#</b> 
                </div>
                <div class="col">
                    <b>Name</b> 
                </div>
                <div class="col">
                    <b>Email</b> 
                </div>
                <div class="col">
                    <b>Status</b> 
                </div>
                <div class="col">
                   <b>Action</b> 
                </div>
            </div>
        </div>

        @foreach ($users as $user)
        <div class="row text-bg-light p-3 my-2 rounded-3">
            <div class="col">
                {{ $user->id }}
            </div>
            <div class="col">
                {{ $user->firstname . ' ' . $user->lastname }}
            </div>
            <div class="col">
                {{ $user->email }}
            </div>
            <div class="col">
                {{ $user->status }}
            </div>
            <div class="col">
                <a href="{{ route('user.column.settings', ['user_id' => $user->id]) }}"><button type="button"
                    class="btn btn-primary">Edit Access</button></a>
            </div>
        </div>
        @endforeach

    </div>
@endsection

{{-- @section('content')
    <table class="table">
        @if (session('success_message'))
            <div class="alert alert-success">
                {{ session('success_message') }}
            </div>
        @endif
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>

            </tr>
        </thead>
        <tbody>

            @foreach ($users as $user)
                <tr>
                    <th scope="row">{{ $user->id }}</th>
                    <td>{{ $user->firstname . ' ' . $user->lastname }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->status }}</td>
                    <td><a href="{{ route('user.column.settings', ['user_id' => $user->id]) }}"><button type="button"
                                class="btn btn-primary">Edit Access</button></a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection --}}
