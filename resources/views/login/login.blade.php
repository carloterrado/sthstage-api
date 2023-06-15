@extends('layouts.mainlayout')

@section('content')

<div class="container" style="height: 100vh;">
    <div class="row justify-content-center align-items-center" style="height: 100%;">
        <div class="col-md-4 card">
            <div class="card-body">

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" placeholder="Enter email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="Enter password" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

            </div>
        </div>
    </div>
</div>