@extends('layouts.mainlayout')
@section('content')
    <div class="row">
        <div class="col-2 text-light side-bar">
            <!-- Sidebar content goes here -->
            @include('documentations.side-bar-button')

        </div>
        <div class="col-10">
            <h1>Introduction</h1>
            <div class="divider"></div>
            <p>This documentation offers a detailed and thorough guide for developers to gain direct access to STH using an API. It provides comprehensive instructions on setting up and acquiring knowledge about interacting with the API. </p>
            <p>By following this documentation, developers will be able to comprehend to utilize the API for integration purposes. </p>
            <div class="divider"></div>
            <h3>AUTHENTICATION</h3>
            <p>Tokens are generated on each user authentication. To authenticate with API for STH, you need to call the login endpoint and put the required parameters:</p>
            <h3>HTTP Request</h3>
            <p><code>https://api/v1/login</code></p>
            <h3>Parameters</h3>
            <p>email</p>
            <p>password</p>
        

            <h3>JSON</h3>
            <p>To get token</p>
            <div class="divider"></div>
            <pre>
            <code class="language-json" style=" color:aqua ">
<p>GET https://api/v1/login</p>
{
"email": user@example.com, 
"password": password
}
            </code>
        </pre>
            <div class="divider"></div>
            <pre>
            <code class="language-json" style=" color:aqua ">
<p>Response:</p>
{ 

    “token”:"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5OTVjZDU4Ni0yMDc4LTQ5MTUtYjNmNi04NzE1YzM1NGM3NjgiLCJqdGkiOiI2OTAyNTE5MjI0ZTVlMzEwOWE3NTE1MWIzZ
    WQ5Y2QxYWExYzkyMjQ3M2I1NDI2ZjNmNzA3MWJmNWYwZjQ1MGM1ZTVhZDI1MjRiYjcxZTcwNSIsImlhdCI6MTY4Njk0NDIzNi40NzE0NzQsIm5iZiI6MTY4Njk0NDIzNi40NzE0NzgsImV4cCI6MTcx
    ODU2NjYzNi4wMzMyMjYsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.SJWMqt7AJXySOigl8PAfCKMO0fHNPV6yjCN5575znyW8TJnahCL4WZIJIkiwGo95xsRLMauRLRkLXPmGvfqtauAtJpWUhRo-
    ZNYgA_S8Ugo9697r4QQFw4cqtA4Ljk3CctYd-5kpey-3P_xn7mSFhDwbEHfx2ns5Mj5WP6nLQFgIfMbBo6R7cFLnhzd3DNzyM8XQxIttTfbtdApSgzHq5vK
    dIpJSwNvQUs5DqMo4YV-eDaS8FmFg3LtQlS8meCS6alkwm_mdtgvyy09Hjif9W_-vMZJEWrRT9Wjxl9XcbtCyYaZ-H2YOZMzCXdiiRwUj39k2XGkqXW
    mL6SKpJNxDm07LHMLmudhpZVkYMAGz0MCyHkcyCmov-RRviSeu8gmhaf5e_waJ5xu_6AbU3WnsXYQlj7VfrHzy8GK9S3wdVRuSgmCRIoWUOOGYeVxZbbTE
    iiJ6GyI5lzoJh5KqHHW4Rv-EEg3hy9aK3EbAocb82XOoqLKz92zrtNGd3IlmpzHF-o3zta1ZStB3zQXldR4T6INuHJBeODm4l-8ZqwtqQ561dl-5Oac49S1m73ez6rwx9oOg1-
    qlF5ltm62QzrKY-PVVM6iPYh4vigEmnzybur9rZVIruOSz9IKx-lELTSF5ZRuh5IsvDZ9-IynB6srqwM3yXZ1z_dPqrNomyt9_T8"
} 
            </code>
        </pre>
        </div>
    </div>
@endsection
