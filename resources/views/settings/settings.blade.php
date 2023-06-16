{{-- <nav class="nav flex-column position-fixed">
    <div class="mx-4 mt-lg-5">
        <h4>Settings</h4>
        <h6 class="my-5"><a href="{{ route('users') }}">User Management Settings</a></h6>
        <h6 class="my-5"><a href="#">Update Vendor Settings</a></h6> 
        <h6 class="my-5"><a href="{{ route('catalog') }}">Catalog Settings</a></h6> 
    </div>
</nav> --}}

<nav class="nav flex-column position-fixed bg-dark text-light">
    <div class="p-4">
        <h4 class="mb-4">Settings</h4>
        <a href="{{ route('users') }}" class="nav-link mb-3 text-light">User Management Settings</a>
        <a href="#" class="nav-link mb-3 text-light">Update Vendor Settings</a> 
        <a href="{{ route('catalog') }}" class="nav-link mb-3 text-light">Catalog Settings</a> 
    </div>
</nav>