<nav class="nav col-2 flex-column position-fixed top-0 start-0 nav-side-bar bottom-0 bg-dark">
    <div class="d-flex flex-column justify-content-between" style="height: 100vh;">
        <div class="mx-5 mt-lg-5">
            <div class="mt-3">
                <h3>Admin Panel</h3>
                <hr>
            </div>
            <h4 class="mt-3">API</h4>
            <ul class="list-unstyled ms-3">
                <li><a href="{{ route('userManagementPage') }}">Role Management</a></li>
                <li><a href="{{ route('users') }}">Role Setting</a></li>
                <li><a href="#">Vendor Update</a></li>
            </ul>
            <h4>Catalog</h4>
            <ul class="list-unstyled ms-3">
                <li><a href="{{ route('catalog') }}">Add Catalog</a></li>
            </ul>
            <h4><a href="{{ route('home') }}">API Document</a></h4>
        </div>
        <div class="mb-3 text-center">
            <form action="{{ route('logout') }}" method="POST" class="mb-3 mx-4">
                @csrf
                <button type="submit" class="btn btn-secondary form-control">Logout</button>
            </form>
        </div>
    </div>
</nav>