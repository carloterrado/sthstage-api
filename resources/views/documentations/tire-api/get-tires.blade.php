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
            <h3>Get Tires</h3>
            <p>Get a list of tires by brand, mspn, and size for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>{{ url('/api/v1/catalog/tires') }}</code></p>
            <h3>Parameters</h3>
            <p>brand</p> 
            <p>mspn</p>
            <p>section_width</p>
            <p>aspect_ratio</p>
            <p>rim_diameter</p>
            <h3>JSON</h3>
            <p>To get a list of tires by brand, mspn, section_width, aspect_ratio and rim_diameter available in DELINTE, 805850, 275, 25 and 24: </p>

            <div class="divider"></div>
            <pre>
        <code class="language-json" style=" color:aqua ">
<p>GET {{ url('/api/v1/catalog/tires') }}</p>
{
"brand": DELINTE,
"mspn": 805850,
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
            "aspect_ratio": "25",
            "brand": "DELINTE",
            "category": 1,
            "full_size": "275/25R24",
            "id": 394752,
            "mspn": "805850",
            "rim_diameter": "24",
            "section_width": "275",
            "size_dimensions": "",
            "unq_id": "DELINTE805850",
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
