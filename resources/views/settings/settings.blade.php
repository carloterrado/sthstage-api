<nav class="nav col-2 flex-column position-fixed top-0 start-0 nav-side-bar bottom-0 bg-dark">
    <div class="d-flex flex-column justify-content-between" style="height: 100vh;">
        <div class="mx-4 mt-lg-5">
            <!-- <h4 class="mb-4">Settings</h4> -->
            <div>
                <h6>
                    <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#vehicleCollapse" aria-expanded="false"
                        aria-controls="vehicleCollapse">
                        API
                    </a>
                </h6>
                <div id="vehicleCollapse" class="collapse">
                    <ul class="list-unstyled">
                        <li><a href="{{ route('users') }}" class="nav-link mb-3 text-light">Edit API User Access</a>
                        </li>
                        <li><a href="{{ route('userManagementPage') }}" class="nav-link mb-3 text-light">Add User</a>
                        </li>
                        <li><a href="#" class="nav-link mb-3 text-light">Update Vendor</a></li>
                    </ul>
                </div>
            </div>

            <div>
                <h6>
                    <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#wheelCollapse" aria-expanded="false"
                        aria-controls="wheelCollapse">
                        CATALOG
                    </a>
                </h6>
                <div id="wheelCollapse" class="collapse">
                    <ul class="list-unstyled">
                        <li><a href="{{ route('catalog') }}" class="nav-link mb-3 text-light">Add Catalog</a></li>
                    </ul>
                </div>
            </div>

            <a href="{{ route('home') }}" class="nav-link mb-3 text-light">Documentation</a>

        </div>
        <div class="mb-3">
            <form action="{{ route('logout') }}" method="POST" class="mb-3 mx-4">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</nav>
