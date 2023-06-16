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
            <h3>Get locatin details</h3>
            <p>Get a list of location details for the specified parameters.</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/catalog/locations</code></p>
            <h3>Parameters</h3>
            <p>location_id</p>

            <h3>JSON</h3>
            <p>To get a list of location details available in 3:</p>

            <div class="divider"></div>
            <pre>
    <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/catalog/locations</p>
{
"location_id": 3
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
            "location_id": 3,
            "address": "15083 E Imperial Hwy",
            "city": "La Mirada",
            "state": "CA",
            "zip_code": "90638",
            "email": "lamirada@americantiredepot.com",
            "phone": "(562) 902-9124",
            "cut_off": "10:00:00",
            "TWIbranchCode": "07",
            "TWIshiptocode": "ATV013",
            "NTWshiptocode": "0040010585",
            "branch_code": null
        }
    ]
}
    </code>
</pre>
        </div>
    </div>
@endsection
