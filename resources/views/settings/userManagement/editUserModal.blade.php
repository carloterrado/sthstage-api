<div class="modal fade" id="editUserModal{{ $user->id }}" >
  <div class="modal-dialog modal-dialog-centered modal-lg">
    
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User {{ $user->id }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="{{ route('editUser', ['id' => $user->id]) }}">
      @csrf
      <div class="modal-body text-start">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" placeholder="First Name" name="firstname" value="{{ $user->firstname }}"  required>
            </div>
            <div class="mb-3 col-lg-6">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastname" placeholder="Last Name" name="lastname" value="{{ $user->lastname }}" required>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Email" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="mb-3 col-lg-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
            </div>
        </div>


        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" value="{{ $user->role }}">
                <option value="Admin">Admin</option>
                <option value="User">User</option>
                </select>
            </div>
            <div class="mb-3 col-lg-6">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" value="{{ $user->status }}" >
                <option value="0">Inactive</option>
                <option value="1">Active</option>
                <option value="2">Pending</option>
                </select>
            </div>
        </div>


        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="seenlog" class="form-label">Seen Log</label>
                <select class="form-select" id="seenlog" name="seenlog" value="{{ $user->seenlog }}">
                <option value="0">No</option>
                <option value="1">Yes</option>
                </select>
            </div>
            <div class="mb-3 col-lg-6">
                <label for="display_user" class="form-label">Display User</label>
                <select class="form-select" id="display_user" name="display_user" value="{{ $user->display_user }}">
                <option value="0">No</option>
                <option value="1">Yes</option>
                </select>
            </div>
        </div>
      </div>

        <div class="modal-footer">
            <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
            <button class="btn btn-secondary" type="submit">Save</button>
        </div>
      </form>

    </div>
  </div>

  
</div>
