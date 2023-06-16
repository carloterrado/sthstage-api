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
            <h3>Tire Get by brand</h3>
            <p>Get a list of tires by brand for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/tires</code></p>
            <h3>Parameters</h3>
            <p>brand</p>
            <h3>Optional Parameters</h3>
            <p>mspn</p>
            <p>section_width</p>
            <p>aspect_ratio</p>
            <p>rim_diameter</p>
            <h3>JSON</h3>
            <p>To get a list of tires that are available in the Brand DELINTE:</p>

            <div class="divider"></div>
            <pre>
        <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/tires</p>
{
"brand": DELINTE
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
            "id": 394186,
            "unq_id": "DELINTE101022",
            "category": 1,
            "brand": "DELINTE",
            "mspn": "101022",
            "size_dimensions": "",
            "full_size": "305/35R24",
            "section_width": "305",
            "aspect_ratio": "35",
            "rim_diameter": "24",
            "wheel_diameter": "",
            "wheel_width": ""
        },
        {
            "id": 394187,
            "unq_id": "DELINTE101183",
            "category": 1,
            "brand": "DELINTE",
            "mspn": "101183",
            "size_dimensions": "",
            "full_size": "225/45R18",
            "section_width": "205",
            "aspect_ratio": "45",
            "rim_diameter": "18",
            "wheel_diameter": "",
            "wheel_width": ""
        },
        {
            "id": 394188,
            "unq_id": "DELINTE101329",
            "category": 1,
            "brand": "DELINTE",
            "mspn": "101329",
            "size_dimensions": "",
            "full_size": "255/30R24",
            "section_width": "305",
            "aspect_ratio": "30",
            "rim_diameter": "24",
            "wheel_diameter": "",
            "wheel_width": ""
        },...]
}
        </code>
    </pre>
        </div>
    </div>
@endsection
