@extends('layouts.mainlayout')
@section('content')
    <div class="row">
        <div class="col-2 text-light side-bar">
            <!-- Sidebar content goes here -->
            @include('documentations.side-bar-button')

        </div>
        <div class="col-10">
            <h1>Vehicle API</h1>
            <div class="divider"></div>
            <h3>Get by makes</h3>
            <p>Get a list of vehicles by makes for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/vehicle/makes</code></p>
            <h3>Parameters</h3>
            <p>year</p>

            <h3>JSON</h3>
            <p>To get a list of vehicle makes available in 1937:</p>

            <div class="divider"></div>
            <pre>
                <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/vehicle/makes</p>
{
 "year": 1937   
}
                </code>
            </pre>
            <div class="divider"></div>
            <pre>
                <code class="language-json" style=" color:aqua ">
<p>Response:</p>
{
    "Makes": [
        "Buick",
        "Chevrolet",
        "Ford",
        "Plymouth",
        "Studebaker"
    ]
}
                </code>
            </pre>
        </div>
    </div>
@endsection
