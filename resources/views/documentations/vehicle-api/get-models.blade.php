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
        <h3>Get by models</h3>
        <p>Get a list of vehicles by models for the specified parameters.</p>
        <h3>HTTP Request</h3>
        <p><code>{{ url('/api/v1/catalog/vehicle/models') }}</code></p>
        <h3>Parameters</h3>
        <p>year</p>
        <p>make</p>
        <h3>JSON</h3>
        <p>To get a list of vehicle models available in 1937 and Chavrolet:</p>

        <div class="divider"></div>
        <pre>
            <code class="language-json" style=" color:aqua ">
<p>GET {{ url('/api/v1/catalog/vehicle/models') }}</p>
{
"year": 1937,
"make": Chevrolet
}
            </code>
        </pre>
        <div class="divider"></div>
        <pre>
            <code class="language-json" style=" color:aqua ">
<p>Response:</p>
{
    "Models": [
        "1/2 Ton",
        "Coupe",
        "Sedan"
    ]
}
            </code>
        </pre>
    </div>
</div>
@endsection
