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
@endsection
