<nav class="nav flex-column position-fixed top-0 start-0 nav-side-bar bottom-0 bg-dark">
    <div class="mx-4 mt-lg-5 ">


        <a href="{{ route('home') }}"> <img src="{{ asset('kimlogo.png') }}" alt="logo" class="image"></a><br>
        <h6><a href="{{ route('home') }}">Introduction</a></h6>
        <div>
            <h6>
                <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#vehicleCollapse" aria-expanded="false"
                    aria-controls="vehicleCollapse">
                    Vehicle API
                </a>
            </h6>
            <div id="vehicleCollapse" class="collapse">
                <ul class="list-unstyled" style="padding-left: 20px">
                    <li><a href="#vehiclegetyears">GetYears</a></li>
                    <li><a href="#vehiclegetmakes">GetMakes</a></li>
                    <li><a href="#vehiclegetmodels">GetModels</a></li>
                    <li><a href="#vehiclegetoptions">GetOptions</a></li>
                    <li><a href="#vehiclegetsize">GetSize</a></li>
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
                <ul class="list-unstyled" style="padding-left: 20px">
                    <li><a href="#getwheels">GetWheels</a></li>
                    <li><a href="#getwheelsbrand">GetBrand</a></li>
                    <li><a href="#getwheelsmspn">GetMspn</a></li>
                    <li><a href="#getwheelssize">GetSize</a></li>
                    <li><a href="#getwheelsbyvehicle">GetWheels by vehicle</a></li>
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
                <ul class="list-unstyled" style="padding-left: 20px">
                    <li><a href="#gettires">GetTires</a></li>
                    <li><a href="#gettiresbrand">GetBrand</a></li>
                    <li><a href="#gettiresmspn">GetMspn</a></li>
                    <li><a href="#gettiressize">GetSize</a></li>
                    <li><a href="#gettiresbyvehicle">GetTires by vehicle</a></li>

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
                <ul class="list-unstyled" style="padding-left: 20px">
                    <li><a href="#getlocation">GetLocation</a></li>
                    <li><a href="#getinventorypricebylocation">GetInventory/Price by location</a></li>
                </ul>
            </div>
        </div>
        <h6><a href="{{ route('users') }}">Back to users</a></h6>

    </div>
</nav>
