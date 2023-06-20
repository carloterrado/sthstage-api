<div class="modal fade" id="deleteUserModal{{ $user->id }}">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="{{ route('deleteUser', ['id' => $user->id]) }}">
      @csrf
      <div class="modal-body">
      Are you sure you want to delete this user?
      </div>

        <div class="modal-footer">
            <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-secondary">Yes</button>
        </div>
      </form>

    </div>
  </div>
</div>