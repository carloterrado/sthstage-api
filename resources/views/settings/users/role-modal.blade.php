<div class="modal fade" id="addRoleModal">
    <div class="modal-dialog modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('addRole') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 col">
                        <label for="firstname" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="role" placeholder="Role Name" name="role"
                            required>
                    </div>

                    <div class="mb-3 col">
                        <label for="firstname" class="form-label">Role Access</label>
                        <input type="text" class="form-control" id="access" placeholder="Access" name="access"
                            required>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
