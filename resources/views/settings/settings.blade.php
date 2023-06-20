<nav class="nav col-2 flex-column position-fixed top-0 start-0 nav-side-bar bottom-0 bg-dark">
    <div class="d-flex flex-column justify-content-between" style="height: 100vh;">
        <div class="mx-4 mt-lg-5">
            <!-- <h4 class="mb-4">Settings</h4> -->
            <a href="{{ route('catalog') }}" class="nav-link mb-3 text-light">Add Catalog</a>
            <a href="{{ route('users') }}" class="nav-link mb-3 text-light">Edit API User Access</a>
            <a href="{{ route('userManagementPage') }}" class="nav-link mb-3 text-light">Add User</a>
            <a href="#" class="nav-link mb-3 text-light">Update Vendor</a>
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
