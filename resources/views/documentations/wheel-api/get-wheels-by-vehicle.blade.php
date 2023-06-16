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
            <h3>Get Wheels by vehicle</h3>
            <p>Get a list of wheels by vehicle for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/vehicle/wheels</code></p>
            <h3>Parameters</h3>
            <p>year</p>
            <p>make</p>
            <p>model</p>
            <p>option</p>
            <p>size</p>

            <h3>JSON</h3>
            <p>To get a list of wheels by vehicle - year, make, model, option, and size available in 2024, Acura, Integra, A-Spec 4DR Hatchback, and 18x8:</p>

            <div class="divider"></div>
            <pre>
<code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/vehicle/wheels</p>
{
"year": 2024,
"make": Acura,
"model": Integra,
"option": A-Spec 4DR Hatchback,
"size": 18x8
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
            "id": 400,
            "unq_id": "1-104GG-TM88514356",
            "category": 2,
            "brand": "KONIG",
            "mspn": "104GG-TM88514356",
            "size_dimensions": "18x8",
            "full_size": "18x8",
            "section_width": "",
            "aspect_ratio": "",
            "rim_diameter": "",
            "wheel_diameter": "18",
            "wheel_width": "8"
        },
        {
            "id": 401,
            "unq_id": "1-104GG-TM88514456",
            "category": 2,
            "brand": "KONIG",
            "mspn": "104GG-TM88514456",
            "size_dimensions": "18x8",
            "full_size": "18x8",
            "section_width": "",
            "aspect_ratio": "",
            "rim_diameter": "",
            "wheel_diameter": "18",
            "wheel_width": "8"
        },
        {
            "id": 1284,
            "unq_id": "1-16MB-LA88514355",
            "category": 2,
            "brand": "KONIG",
            "mspn": "16MB-LA88514355",
            "size_dimensions": "18x8",
            "full_size": "18x8",
            "section_width": "",
            "aspect_ratio": "",
            "rim_diameter": "",
            "wheel_diameter": "18",
            "wheel_width": "8"
        },
        {
            "id": 1285,
            "unq_id": "1-16MB-LA88514455",
            "category": 2,
            "brand": "KONIG",
            "mspn": "16MB-LA88514455",
            "size_dimensions": "18x8",
            "full_size": "18x8",
            "section_width": "",
            "aspect_ratio": "",
            "rim_diameter": "",
            "wheel_diameter": "18",
            "wheel_width": "8"
        },...]
}
</code>
</pre>
        </div>
    </div>
@endsection
