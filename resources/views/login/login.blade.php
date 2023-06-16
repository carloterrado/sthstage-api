@extends('layouts.mainlayout')

@section('content')

<div class="container" style="height: 100vh;">
    <div class="row justify-content-center align-items-center" style="height: 100%;">
        <div class="col-lg-4 card">
            <div class="card-body" style="margin:36px 13px 36px 13px;">

                <p class="text-center fs-4 mb-8">Login</p>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="email" class="form-control form-control-lg" placeholder="Enter email" name="email" style="font-size: 16px">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Password</label>
                        <input type="password" class="form-control form-control-lg" placeholder="Enter password" name="password" style="font-size: 16px">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-lg btn-block col-lg-12 mt-3">Submit</button>
                </form>

            </div>
        </div>
    </div>
</div>