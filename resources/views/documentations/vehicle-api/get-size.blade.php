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
            <h3>Get by size</h3>
            <p>Get a list of vehicles by size for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/vehicle/size</code></p>
            <h3>Parameters</h3>
            <p>year</p>
            <p>make</p>
            <p>model</p>
            <p>option</p>
            <h3>JSON</h3>
            <p>To get a list of vehicle size available in 2024, Acura, Integra, and A-Spec 4DR Hatchback:</p>

            <div class="divider"></div>
            <pre>
            <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/vehicle/size</p>
{
"year": 1937,
"make": Chevrolet,
"model": ½ Ton,
"option": A-Spec 4DR Hatchback
}
            </code>
        </pre>
            <div class="divider"></div>
            <pre>
            <code class="language-json" style=" color:aqua ">
<p>Response:</p>
{
    "Size": {
        "Tires": [
            "235/40R18"
        ],
        "Wheels": [
            "18x6.5",
            "18x7",
            "18x7.5",
            "18x8",
            "18x8.5",
            "18x9"
        ]
    }
}
            </code>
        </pre>
        </div>
    </div>
@endsection
