<nav class="nav flex-column position-fixed top-0 start-0 nav-side-bar bottom-0 bg-dark">
    <div class="mx-4 mt-lg-5 ">


        <a href="{{ route('home') }}"> <img src="{{asset('kimlogo.png')}}" alt="logo" class="image"></a><br>
        <h6><a href="{{ route('home') }}">Introduction</a></h6>
        <div>
            <h6>
                <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#vehicleCollapse" aria-expanded="false"
                    aria-controls="vehicleCollapse">
                    Vehicle API
                </a>
            </h6>
            <div id="vehicleCollapse" class="collapse">
                <ul class="list-unstyled">
                    <li><a href="{{ route('getyears') }}">GetYears</a></li>
                    <li><a href="{{ route('getmakes') }}">GetMakes</a></li>
                    <li><a href="{{ route('getmodels') }}">GetModels</a></li>
                    <li><a href="{{ route('getoptions') }}">GetOptions</a></li>
                    <li><a href="{{ route('getsize') }}">GetSize</a></li>
                </ul>
            </div>
        </div>
        <div> 
            <h6>
                <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#wheelCollapse" aria-expanded="false"
                    aria-controls="wheelCollapse">
                    Wheel API
                </a>
            </h6>
            <div id="wheelCollapse" class="collapse">
                <ul class="list-unstyled">
                    <li><a href="{{ route('wheelget') }}">GetWheels</a></li>
                    <li><a href="{{ route('wheelgetbrand') }}">GetBrand</a></li>
                    <li><a href="{{ route('wheelgetmspn') }}">GetMspn</a></li>
                    <li><a href="{{ route('wheelgetsize') }}">GetSize</a></li>
                    <li><a href="{{ route('getwheelsbyvehicle') }}">GetWheels by vehicle</a></li>
                </ul>
            </div>
        </div>
        <div>
            <h6>
                <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#tireCollapse" aria-expanded="false"
                    aria-controls="tireCollapse">
                    Tire API
                </a>
            </h6>
            <div id="tireCollapse" class="collapse">
                <ul class="list-unstyled">
                    <li><a href="{{ route('tireget') }}">GetTires</a></li>
                    <li><a href="{{ route('tiregetbrand') }}">GetBrand</a></li>
                    <li><a href="{{ route('tiregetmspn') }}">GetMspn</a></li>
                    <li><a href="{{ route('tiregetsize') }}">GetSize</a></li>
                    <li><a href="{{ route('gettiresbyvehicle') }}">GetTires by vehicle</a></li>
                </ul>
            </div>
        </div>
        <div>
            <h6>
                <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#inventoryCollapse" aria-expanded="false"
                    aria-controls="inventoryCollapse">
                    Inventory API
                </a>
            </h6>
            <div id="inventoryCollapse" class="collapse">
                <ul class="list-unstyled">
                    <li><a href="{{ route('getlocation') }}">GetLocation</a></li>
                    <li><a href="{{ route('getinventorybylocation') }}">GetInventory/Price by location</a></li>
                </ul>
            </div>
        </div>
        <h6><a href="{{ route('users') }}">Back to users</a></h6>

    </div>
</nav>
