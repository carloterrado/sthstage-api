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
            <h3>Get by years</h3>
            <p>Get a list of vehicles by years for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/vehicle/years</code></p>
            <h3>Parameters</h3>
            <p></p>

            <h3>JSON</h3>
            <p>To get list of years:</p>

            <div class="divider"></div>
            <pre>
                <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/vehicle/years</p>
{
    
}
                </code>
            </pre>
            <div class="divider"></div>
            <pre>
                <code class="language-json" style=" color:aqua ">
<p>Response:</p>
{
    "Years": [
        2024,
        2023,
        2022,
        2021,
        2020,...],
    "Success": true,
    "Code": 10,
    "Message": ""
}
                </code>
            </pre>
        </div>

    </div>
@endsection
