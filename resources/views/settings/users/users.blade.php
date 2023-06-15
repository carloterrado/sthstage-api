@extends('layouts.mainlayout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-2 bg-dark text-light side-bar">
                <!-- Sidebar content goes here -->
                @include('settings.settings')
            </div>

            <!-- Main Content -->
            <div class="col-10">
                <div class="container">
                    <h3 class="mt-3">User Management</h3>
                    <div class="row">
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
                    <div class="row bg-white py-3 my-2 rounded-2 align-items-center">
                        <div class="col">
                            {{ $user->name }}
                        </div>
                        <div class="col text-center">
                            <a href="{{ route('user.column.settings', ['id' => $user->id]) }}"><button type="button"
                                    class="btn btn-primary">Edit Access</button></a>
                        </div>
                    </div>
                    @endforeach
        
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @section('content')
    <div class="container ">
        <div class="intro d-flex align-items-center justify-content-center">
            <table class="table table-borderless">
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
@endsection --}}
