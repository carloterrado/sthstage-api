@extends('layouts.mainlayout')
@section('content')
    <div class="row">
        <div class="col-2 text-light side-bar">
            <!-- Sidebar content goes here -->
            @include('documentations.side-bar-button')

        </div>
        <div class="col-10">
            <h1>Tire API</h1>
            <div class="divider"></div>
            <h3>Tire Get by mspn</h3>
            <p>Get a list of tires by mspn for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/tires</code></p>
            <h3>Parameters</h3>
            <p>mspn</p>
            <h3>Optional Parameters</h3>
            <p>brand</p>
            <p>section_width</p>
            <p>aspect_ratio</p>
            <p>rim_diameter</p>
            <h3>JSON</h3>
            <p>To get a list of tires that are available in the MSPN 805850:</p>

            <div class="divider"></div>
            <pre>
        <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/tires</p>
{
"mspn": 805850
}
        </code>
    </pre>
            <div class="divider"></div>
            <pre>
        <code class="language-json" style=" color:aqua ">
<p>Response:</p>
{
    "data": [
        {
            "id": 394752,
            "unq_id": "DELINTE805850",
            "category": 1,
            "brand": "DELINTE",
            "mspn": "805850",
            "size_dimensions": "",
            "full_size": "275/25R24",
            "section_width": "275",
            "aspect_ratio": "25",
            "rim_diameter": "24",
            "wheel_diameter": "",
            "wheel_width": ""
        }
    ]
}
        </code>
    </pre>
        </div>
    </div>
@endsection
