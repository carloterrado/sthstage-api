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
            <h3>Wheel Get by brand</h3>
            <p>Get a list of wheels by brand for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/wheels</code></p>
            <h3>Parameters</h3>
            <p>brand</p>
            <h3>Optional Parameters</h3>
            <p>wheel_diameter</p>
            <p>wheel_width</p>
            <p>mspn</p>

            <h3>JSON</h3>
            <p>To get a list of wheels that are available in the Brand VISION:</p>

            <div class="divider"></div>
            <pre>
            <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/wheels</p>
{
"brand": VISION   
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
            "id": 106077,
            "unq_id": "7-112-127110GMMF4",
            "category": 2,
            "brand": "VISION",
            "mspn": "112-127110GMMF4",
            "size_dimensions": "12x7",
            "full_size": "12x7",
            "section_width": "",
            "aspect_ratio": "",
            "rim_diameter": "",
            "wheel_diameter": "12",
            "wheel_width": "7"
        },
        {
            "id": 106078,
            "unq_id": "7-112-127136GMMF4",
            "category": 2,
            "brand": "VISION",
            "mspn": "112-127136GMMF4",
            "size_dimensions": "12x7",
            "full_size": "12x7",
            "section_width": "",
            "aspect_ratio": "",
            "rim_diameter": "",
            "wheel_diameter": "12",
            "wheel_width": "7"
        },....]
}
            </code>
        </pre>
        </div>
    </div>
@endsection
