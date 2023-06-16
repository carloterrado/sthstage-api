@extends('layouts.mainlayout')
@section('content')
    <div class="row">
        <div class="col-2 text-light side-bar">
            <!-- Sidebar content goes here -->
            @include('documentations.side-bar-button')

        </div>
        <div class="col-10">
            <h1>Catalog Inventory API</h1>
            <div class="divider"></div>
            <h3>Get Inventory/Price by Location</h3>
            <p>Get a list of inventory and prices by locations for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/inventory</code></p>
            <h3>Parameters</h3>
            <p>mspn</p>
            <p>brand</p>

            <h3>JSON</h3>
            <p>To get a list of inventory and prices by locations that are available in the MSPN - 001770 or Brand - BRIDGESTONE:</p>

            <div class="divider"></div>
            <pre>
<code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/inventory</p>
{
"mspn": 001770,
"brand": BRIDGESTONE
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
            "brand": "BRIDGESTONE",
            "part_number": "001770",
            "location": [
                {
                    "store_location_id": 102,
                    "price": 421.05,
                    "qty": 1
                },
                {
                    "store_location_id": 102,
                    "price": 421.05,
                    "qty": 1
                },
                {
                    "store_location_id": 134,
                    "price": 421.05,
                    "qty": 5
                },...]
        }
    ]
}
</code>
</pre>
        </div>
    </div>
@endsection
