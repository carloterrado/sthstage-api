@extends('layouts.mainlayout')

@section('content')
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
@endsection
