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
            <h3>Get Wheels</h3>
            <p>Get a list of tires by brand, mspn, and size for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>{{ url('/api/v1/catalog/wheels') }}</code></p>
            <h3>Parameters</h3>
            <p>brand</p>
            <p>mspn</p>
            <p>wheel_diameter</p>
            <p>wheel_width</p>

            <h3>JSON</h3>
            <p>To get a list of tires by brand, mspn, wheel_diameter, and wheel_width available in VISION, 521-5165P-25, 15,
                and 10: </p>

            <div class="divider"></div>
            <pre>
        <code class="language-json" style=" color:aqua ">
<p>GET {{ url('/api/v1/catalog/wheels') }}</p>
{
"brand": VISION,
"mspn": 521-5165P-25,
"section_width": 15,
"aspect_ratio": 10

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
            "aspect_ratio": "",
            "brand": "VISION",
            "category": 2,
            "full_size": "15x10",
            "id": 109584,
            "mspn": "521-5165P-25",
            "rim_diameter": "",
            "section_width": "",
            "size_dimensions": "15x10",
            "unq_id": "7-521-5165P-25",
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
