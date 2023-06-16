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
            <h3>Tire get by size</h3>
            <p>Get a list of tires by size for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/tires</code></p>
            <h3>Parameters</h3>
            <p>section_width</p>
            <p>aspect_ratio</p>
            <p>rim_diameter</p>
            <h3>Optional Parameters</h3>
            <p>brand</p>
            <p>mspn</p>
            <h3>JSON</h3>
            <p>To get a list of tires that are available in the size of 275, 25, 24:</p>

            <div class="divider"></div>
            <pre>
            <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/tires</p>
{
"section_width": 275,
"aspect_ratio": 25,
"rim_diameter": 24  
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
            "id": 194602,
            "unq_id": "MSCT1951354257",
            "category": 1,
            "brand": "MISC TIRES",
            "mspn": "1951354257",
            "size_dimensions": "275/25-24",
            "full_size": "275/25R24",
            "section_width": "275",
            "aspect_ratio": "25",
            "rim_diameter": "24",
            "wheel_diameter": null,
            "wheel_width": null
        },
        {
            "id": 373604,
            "unq_id": "ADVANTA1951354257",
            "category": 1,
            "brand": "ADVANTA",
            "mspn": "1951354257",
            "size_dimensions": "275/25R24",
            "full_size": "275/25R24",
            "section_width": "275",
            "aspect_ratio": "25",
            "rim_diameter": "24",
            "wheel_diameter": "",
            "wheel_width": ""
        },
        {
            "id": 374884,
            "unq_id": "APLUSAP522H1",
            "category": 1,
            "brand": "APLUS",
            "mspn": "AP522H1",
            "size_dimensions": "",
            "full_size": "275/25R24",
            "section_width": "275",
            "aspect_ratio": "25",
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
