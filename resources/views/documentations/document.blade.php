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
            <p>This documentation offers a detailed and thorough guide for developers to gain direct access to STH using an
                API. It provides comprehensive instructions on setting up and acquiring knowledge about interacting with the
                API. </p>
            <p>By following this documentation, developers will be able to comprehend to utilize the API for integration
                purposes. </p>
            <div class="divider"></div>
            <h3>AUTHENTICATION</h3>
            <p>Tokens are generated on each user authentication. To authenticate with API for STH, you need to call the
                login endpoint and put the required parameters:</p>
            <h3>HTTP Request</h3>
            <p><code> {{ url('/api/v1/login') }}</code></p>

            <h3>Parameters</h3>
            <p>email</p>
            <p>password</p>


            <h3>JSON</h3>
            <p>To get token</p>
            <div class="divider"></div>
            <pre>
            <code class="language-json" style=" color:aqua ">
<p>GET {{ url('/api/v1/login') }}</p>
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

            {{-- VEHiCLE API --}}
            {{-- Get Years --}}
            <div id="vehiclegetyears">
                <h1>Vehicle API</h1>
                <div class="divider"></div>
                <h3>Get by years</h3>
                <p>Get a list of vehicles by years for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/vehicle/years') }}</code></p>
                <h3>Parameters</h3>
                <p></p>

                <h3>JSON</h3>
                <p>To get list of years:</p>

                <div class="divider"></div>
                <pre>
                <code class="language-json" style=" color:aqua ">
<p>GET {{ url('/api/v1/catalog/vehicle/years') }}</p>
{
    
}
                </code>
            </pre>
                <div class="divider"></div>
                <pre>
                <code class="language-json" style=" color:aqua ">
<p>Response:</p>
{
    "Years": [
        2024,
        2023,
        2022,
        2021,
        2020,...],
    "Success": true,
    "Code": 10,
    "Message": ""
}
                </code>
            </pre>
            </div>


            {{-- Get Makes --}}

            <div id="vehiclegetmakes">
                <h1>Vehicle API</h1>
                <div class="divider"></div>
                <h3>Get by makes</h3>
                <p>Get a list of vehicles by makes for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/vehicle/makes') }}</code></p>
                <h3>Parameters</h3>
                <p>year</p>

                <h3>JSON</h3>
                <p>To get a list of vehicle makes available in 1937:</p>

                <div class="divider"></div>
                <pre>
                    <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/vehicle/makes') }}</p>
    {
     "year": 1937   
    }
                    </code>
                </pre>
                <div class="divider"></div>
                <pre>
                    <code class="language-json" style=" color:aqua ">
    <p>Response:</p>
    {
        "Makes": [
            "Buick",
            "Chevrolet",
            "Ford",
            "Plymouth",
            "Studebaker"
        ]
    }
                    </code>
                </pre>
            </div>

            {{-- Get Models --}}

            <div id="vehiclegetmodels">
                <h1>Vehicle API</h1>
                <div class="divider"></div>
                <h3>Get by models</h3>
                <p>Get a list of vehicles by models for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/vehicle/models') }}</code></p>
                <h3>Parameters</h3>
                <p>year</p>
                <p>make</p>
                <h3>JSON</h3>
                <p>To get a list of vehicle models available in 1937 and Chavrolet:</p>

                <div class="divider"></div>
                <pre>
                    <code class="language-json" style=" color:aqua ">
        <p>GET {{ url('/api/v1/catalog/vehicle/models') }}</p>
        {
        "year": 1937,
        "make": Chevrolet
        }
                    </code>
                </pre>
                <div class="divider"></div>
                <pre>
                    <code class="language-json" style=" color:aqua ">
        <p>Response:</p>
        {
            "Models": [
                "1/2 Ton",
                "Coupe",
                "Sedan"
            ]
        }
                    </code>
                </pre>
            </div>

            {{-- Get Options --}}

            <div id="vehiclegetoptions">
                <h1>Vehicle API</h1>
                <div class="divider"></div>
                <h3>Get by options</h3>
                <p>Get a list of vehicles by options for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/vehicle/options') }}</code></p>
                <h3>Parameters</h3>
                <p>year</p>
                <p>make</p>
                <p>model</p>
                <h3>JSON</h3>
                <p>To get a list of vehicle options available in 1937, Chevrolet, and ½ Ton:</p>

                <div class="divider"></div>
                <pre>
                <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/vehicle/options') }}</p>
    {
    "year": 1937,
    "make": Chevrolet,
    "model": ½ Ton
    }
                </code>
            </pre>
                <div class="divider"></div>
                <pre>
                <code class="language-json" style=" color:aqua ">
    <p>Response:</p>
    {
        "Options": [
            "Base 2DR Reg. Cab Step Side",
            "Panel 2DR"
        ]
    }
                </code>
            </pre>
            </div>

            {{-- Get Size --}}

            <div id="vehiclegetsize">
                <h1>Vehicle API</h1>
                <div class="divider"></div>
                <h3>Get by size</h3>
                <p>Get a list of vehicles by size for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/vehicle/size') }}</code></p>
                <h3>Parameters</h3>
                <p>year</p>
                <p>make</p>
                <p>model</p>
                <p>option</p>
                <h3>JSON</h3>
                <p>To get a list of vehicle size available in 2024, Acura, Integra, and A-Spec 4DR Hatchback:</p>

                <div class="divider"></div>
                <pre>
                <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/vehicle/size') }}</p>
    {
    "year": 1937,
    "make": Chevrolet,
    "model": ½ Ton,
    "option": A-Spec 4DR Hatchback
    }
                </code>
            </pre>
                <div class="divider"></div>
                <pre>
                <code class="language-json" style=" color:aqua ">
    <p>Response:</p>
    {
        "Size": {
            "Tires": [
                "235/40R18"
            ],
            "Wheels": [
                "18x6.5",
                "18x7",
                "18x7.5",
                "18x8",
                "18x8.5",
                "18x9"
            ]
        }
    }
                </code>
            </pre>
            </div>

            {{-- END VEHICLE API --}}

            {{-- WHEEL API --}}
            {{-- GET WHEELS --}}

            <div id="getwheels">
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
                <p>To get a list of tires by brand, mspn, wheel_diameter, and wheel_width available in VISION, 521-5165P-25,
                    15,
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

            {{-- GET BRAND --}}
            <div id="getwheelsbrand">
                <h1>Wheel API</h1>
                <div class="divider"></div>
                <h3>Wheel Get by brand</h3>
                <p>Get a list of wheels by brand for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/wheels') }}</code></p>

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
    <p>GET {{ url('/api/v1/catalog/wheels') }}</p>
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

            {{-- GET MSPN --}}
            <div id="getwheelsmspn">
                <h1>Wheel API</h1>
                <div class="divider"></div>
                <h3>Wheel Get by mspn</h3>
                <p>Get a list of wheels by mspn for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/wheels') }}</code></p>
                <h3>Parameters</h3>
                <p>mspn</p>
                <h3>Optional Parameters</h3>
                <p>wheel_diameter</p>
                <p>wheel_width</p>
                <p>brand</p>

                <h3>JSON</h3>
                <p>To get a list of wheels that are available in the MSPN 521-5165P-25:</p>

                <div class="divider"></div>
                <pre>
            <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/wheels') }}</p>
    {
    "mspn": 521-5165P-25
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
                "id": 109584,
                "unq_id": "7-521-5165P-25",
                "category": 2,
                "brand": "VISION",
                "mspn": "521-5165P-25",
                "size_dimensions": "15x10",
                "full_size": "15x10",
                "section_width": "",
                "aspect_ratio": "",
                "rim_diameter": "",
                "wheel_diameter": "15",
                "wheel_width": "10"
            }
        ]
    }
            </code>
        </pre>
            </div>

            {{-- GET Size --}}
            <div id="getwheelssize">
                <h1>Wheel API</h1>
                <div class="divider"></div>
                <h3>Wheel Get by size</h3>
                <p>Get a list of wheels by size for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/wheels') }}</code></p>
                <h3>Parameters</h3>
                <p>wheel_diameter</p>
                <p>wheel_width</p>
                <h3>Optional Parameters</h3>
                <p>mspn</p>
                <p>brand</p>

                <h3>JSON</h3>
                <p>To get a list of wheels that are available in the size of 15, 10:</p>

                <div class="divider"></div>
                <pre>
        <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/wheels') }}</p>
    {
    "wheel_diameter": 15,
    "wheel_width": 10
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
                "id": 1,
                "unq_id": "1-000P-51061-12",
                "category": 2,
                "brand": "CENTERLINE",
                "mspn": "000P-51061-12",
                "size_dimensions": "15x10",
                "full_size": "15x10",
                "section_width": "",
                "aspect_ratio": "",
                "rim_diameter": "",
                "wheel_diameter": "15",
                "wheel_width": "10"
            },
            {
                "id": 2,
                "unq_id": "1-000P-51061-55",
                "category": 2,
                "brand": "CENTERLINE",
                "mspn": "000P-51061-55",
                "size_dimensions": "15x10",
                "full_size": "15x10",
                "section_width": "",
                "aspect_ratio": "",
                "rim_diameter": "",
                "wheel_diameter": "15",
                "wheel_width": "10"
            },...]
    }
        </code>
    </pre>
            </div>

            {{-- GET Wheels by Vehicle --}}
            <div id="getwheelsbyvehicle">
                <h1>Wheel API</h1>
                <div class="divider"></div>
                <h3>Get Wheels by vehicle</h3>
                <p>Get a list of wheels by vehicle for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code> {{ url('/api/v1/catalog/vehicle/wheels') }}</code></p>
                <h3>Parameters</h3>
                <p>year</p>
                <p>make</p>
                <p>model</p>
                <p>option</p>
                <p>size</p>

                <h3>JSON</h3>
                <p>To get a list of wheels by vehicle - year, make, model, option, and size available in 2024, Acura,
                    Integra, A-Spec 4DR Hatchback, and 18x8:</p>

                <div class="divider"></div>
                <pre>
    <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/vehicle/wheels') }}</p>
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
            {{-- END WHEEL API --}}

            {{-- TIRE API --}}
            {{-- Get Tires --}}
            <div id="gettires">
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
                <p>To get a list of tires by brand, mspn, section_width, aspect_ratio and rim_diameter available in DELINTE,
                    805850, 275, 25 and 24: </p>

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
            {{-- Get Brand --}}
            <div id="gettiresbrand">
                <h1>Tire API</h1>
                <div class="divider"></div>
                <h3>Tire Get by brand</h3>
                <p>Get a list of tires by brand for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/tires') }}</code></p>
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
    <p>GET {{ url('/api/v1/catalog/tires') }}</p>
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

            {{-- Get Mspn --}}
            <div id="gettiresmspn">
                <h1>Tire API</h1>
                <div class="divider"></div>
                <h3>Tire Get by mspn</h3>
                <p>Get a list of tires by mspn for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/tires') }}</code></p>

                <h3>Parameters</h3>
                <p>mspn</p>
                <h3>Optional Parameters</h3>
                <p>brand</p>
                <p>section_width</p>
                <p>aspect_ratio</p>
                <p>rim_diameter</p>
                <h3>JSON</h3>
                <p>To get a list of tires that are available in the MSPN 805850:</p>

                <div class="divider"></div>
                <pre>
            <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/tires') }}</p>
    {
    "mspn": 805850
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
                "id": 394752,
                "unq_id": "DELINTE805850",
                "category": 1,
                "brand": "DELINTE",
                "mspn": "805850",
                "size_dimensions": "",
                "full_size": "275/25R24",
                "section_width": "275",
                "aspect_ratio": "25",
                "rim_diameter": "24",
                "wheel_diameter": "",
                "wheel_width": ""
            }
        ]
    }
            </code>
        </pre>
            </div>
            {{-- Get Size --}}
            <div id="gettiressize">
                <h1>Tire API</h1>
                <div class="divider"></div>
                <h3>Tire get by size</h3>
                <p>Get a list of tires by size for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/tires') }}</code></p>
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
    <p>GET {{ url('/api/v1/catalog/tires') }}</p>
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
            {{-- Get Tires by vehicle --}}
            <div id="gettiresbyvehicle">
                <h1>Tire API</h1>
                <div class="divider"></div>
                <h3>Get tires by vehicle</h3>
                <p>Get a list of tires by vehicle for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/vehicle/tires') }}</code></p>
                <h3>Parameters</h3>
                <p>year</p>
                <p>make</p>
                <p>model</p>
                <p>option</p>
                <p>size</p>
                <h3>JSON</h3>
                <p>To get a list of tires by vehicle - year, make, model, option, and size available in 2024, Acura,
                    Integra,
                    A-Spec 4DR Hatchback, and 235/40R18:</p>

                <div class="divider"></div>
                <pre>
        <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/vehicle/tires') }}</p>
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
            {{-- END TIRE API --}}

            {{-- INVENTORY API --}}
            {{-- GET Location --}}
            <div id="getlocation">
                <h1>Catalog Inventory API</h1>
                <div class="divider"></div>
                <h3>Get locatin details</h3>
                <p>Get a list of location details for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/locations') }}</code></p>
                <h3>Parameters</h3>
                <p>location_id</p>

                <h3>JSON</h3>
                <p>To get a list of location details available in 3:</p>

                <div class="divider"></div>
                <pre>
        <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/locations') }}</p>
    
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

            {{-- GET Invetory/Price by location --}}
            <div id="getinventorypricebylocation">
                <h1>Catalog Inventory API</h1>
                <div class="divider"></div>
                <h3>Get Inventory/Price by Location</h3>
                <p>Get a list of inventory and prices by locations for the specified parameters.</p>
                <h3>HTTP Request</h3>
                <p><code>{{ url('/api/v1/catalog/inventory') }}</code></p>
                <h3>Parameters</h3>
                <p>mspn</p>
                <p>brand</p>
    
                <h3>JSON</h3>
                <p>To get a list of inventory and prices by locations that are available in the MSPN - 001770 or Brand -
                    BRIDGESTONE:</p>
    
                <div class="divider"></div>
                <pre>
    <code class="language-json" style=" color:aqua ">
    <p>GET {{ url('/api/v1/catalog/inventory') }}</p>
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
            {{-- END INVENTORY API --}}


        </div>
    </div>
@endsection
