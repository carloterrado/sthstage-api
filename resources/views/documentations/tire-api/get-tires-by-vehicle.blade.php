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
            <h3>Get tires by vehicle</h3>
            <p>Get a list of tires by vehicle for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/vehicle/tires</code></p>
            <h3>Parameters</h3>
            <p>year</p>
            <p>make</p>
            <p>model</p>
            <p>option</p>
            <p>size</p>
            <h3>JSON</h3>
            <p>To get a list of tires by vehicle - year, make, model, option, and size available in 2024, Acura, Integra,
                A-Spec 4DR Hatchback, and 235/40R18:</p>

            <div class="divider"></div>
            <pre>
    <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/vehicle/tires</p>
{
"year": 2024,
"make": Acura,
"model": Integra,
"option": A-Spec 4DR Hatchback,
"size": 235/40R18
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
            "id": 194570,
            "unq_id": "MSCT1951338403",
            "category": 1,
            "brand": "MISC TIRES",
            "mspn": "1951338403",
            "size_dimensions": "235/40-18",
            "full_size": "235/40R18",
            "section_width": "235",
            "aspect_ratio": "40",
            "rim_diameter": "18",
            "wheel_diameter": null,
            "wheel_width": null
        },
        {
            "id": 372326,
            "unq_id": "ACCELERA1200000110",
            "category": 1,
            "brand": "ACCELERA",
            "mspn": "1200000110",
            "size_dimensions": "235/40R18",
            "full_size": "235/40R18",
            "section_width": "235",
            "aspect_ratio": "40",
            "rim_diameter": "18",
            "wheel_diameter": "",
            "wheel_width": ""
        },
        {
            "id": 372549,
            "unq_id": "ACCELERA1200038597",
            "category": 1,
            "brand": "ACCELERA",
            "mspn": "1200038597",
            "size_dimensions": "235/40R18",
            "full_size": "235/40R18",
            "section_width": "235",
            "aspect_ratio": "40",
            "rim_diameter": "18",
            "wheel_diameter": "",
            "wheel_width": ""
        },...]
}
    </code>
</pre>
        </div>
    </div>
@endsection
