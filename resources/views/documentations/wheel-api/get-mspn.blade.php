@extends('layouts.mainlayout')
@section('content')
    <div class="row">
        <div class="col-2 text-light side-bar">
            <!-- Sidebar content goes here -->
            @include('documentations.side-bar-button')

        </div>
        <div class="col-10">
            <h1>Wheel API</h1>
            <div class="divider"></div>
            <h3>Wheel Get by mspn</h3>
            <p>Get a list of wheels by mspn for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/wheels</code></p>
            <h3>Parameters</h3>
            <p>mspn</p>
            <h3>Optional Parameters</h3>
            <p>wheel_diameter</p>
            <p>wheel_width</p>
            <p>brand</p>

            <h3>JSON</h3>
            <p>To get a list of wheels that are available in the MSPN 521-5165P-25:</p>

            <div class="divider"></div>
            <pre>
        <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/wheels</p>
{
"mspn": 521-5165P-25
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
            "id": 109584,
            "unq_id": "7-521-5165P-25",
            "category": 2,
            "brand": "VISION",
            "mspn": "521-5165P-25",
            "size_dimensions": "15x10",
            "full_size": "15x10",
            "section_width": "",
            "aspect_ratio": "",
            "rim_diameter": "",
            "wheel_diameter": "15",
            "wheel_width": "10"
        }
    ]
}
        </code>
    </pre>
        </div>
    </div>
@endsection
