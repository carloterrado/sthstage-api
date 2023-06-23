<nav class="nav col-2 flex-column position-fixed top-0 start-0 nav-side-bar bottom-0 bg-dark">
    <div class="d-flex flex-column justify-content-between" style="height: 100vh;">
        <div class="mx-5 mt-lg-5">
            <h4>API</h4>
            <ul class="list-unstyled ms-3">
                <li><a href="{{ route('userManagementPage') }}">Role Management</a></li>
                <li><a href="{{ route('users') }}">User Role</a></li>
                <li><a href="#">Vendor Update</a></li>
            </ul>
            <h4>Catalog</h4>
            <ul class="list-unstyled ms-3">
                <li><a href="#">Add Catalog</a></li>
            </ul>
        </div>
        <div class="mb-3 text-center">
            <form action="{{ route('logout') }}" method="POST" class="mb-3 mx-4">
                @csrf
                <button type="submit" class="btn btn-secondary form-control">Logout</button>
            </form>
        </div>
    </div>
</nav>

{{-- <nav class="nav col-2 flex-column position-fixed top-0 start-0 nav-side-bar bottom-0 bg-dark">
    <div class="d-flex flex-column justify-content-between" style="height: 100vh;">
        <div class="mx-4 mt-lg-5">
            <div>
                <h6>
                    <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#apiCollapse" aria-expanded="false"
                        aria-controls="apiCollapse">
                        API
                    </a>
                </h6>
                <div id="apiCollapse" class="collapse">
                    <ul class="list-unstyled">
                        <li><a href="{{ route('userManagementPage') }}">Role Management</a></li>
                        <li><a href="{{ route('users') }}">User Role</a></li>
                        <li><a href="#">Vendor Update</a></li>
                    </ul>
                </div>
            </div>

            <div>
                <h6>
                    <a class="collapsed" data-bs-toggle="collapse" data-bs-target="#catalogCollapse"
                        aria-expanded="false" aria-controls="catalogCollapse">
                        Catalog
                    </a>
                </h6>
                <div id="catalogCollapse" class="collapse">
                    <ul class="list-unstyled">
                        <li><a href="#">Add Catalog</a></li>
                    </ul>
                </div>
            </div>
            <div class="mb-3">
                <form action="{{ route('logout') }}" method="POST" class="mb-3 mx-4">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav> --}}