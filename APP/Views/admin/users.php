{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <!-- Add/Edit Column -->
    <div class="col-md-4 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0" id="formTitle"><i class="fa-solid fa-user-plus me-2"></i> Add New User</h5>
            </div>
            <div class="card-body card-custom-body">
                <form action="{{ pathto('admin/user/save') }}" method="POST" id="userForm">
                    <input type="hidden" name="id" id="user_id" value="">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label font-weight-bold">Username</label>
                        <input type="text" name="username" id="user_username" class="form-control" placeholder="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label font-weight-bold">Email Address</label>
                        <input type="email" name="email" id="user_email" class="form-control" placeholder="user@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label font-weight-bold">Role</label>
                        <select name="role" id="user_role" class="form-select" required>
                            <option value="subscriber">Subscriber</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label font-weight-bold">Status</label>
                        <select name="status" id="user_status" class="form-select" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label font-weight-bold" id="lblPass">Password</label>
                        <input type="password" name="password" id="user_password" class="form-control" placeholder="Enter password">
                        <div class="form-text small" id="passHelp">Required for new users. Leave blank when editing to keep unchanged.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-2"></i> Save User Profile</button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="btnCancelEdit" style="display:none;">Cancel Edit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Listing Column -->
    <div class="col-md-8 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-users me-2"></i> User Accounts</h5>
            </div>
            <div class="card-body card-custom-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="45%">User details</th>
                                <th width="15%">Role</th>
                                <th width="15%">Status</th>
                                <th width="20%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) { ?>
                                <tr id="row{{ $user['id'] }}">
                                    <td>{{ $user['id'] }}</td>
                                    <td>
                                        <strong class="d-block">{{ htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') }}</strong>
                                        <small class="text-muted">{{ htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'editor' ? 'warning' : 'secondary') ?>">
                                            {{ ucwords($user['role']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-toggle" data-id="{{ $user['id'] }}" data-action="user_status" style="width: 100px;" <?= (int)$user['id'] === $_SESSION['ICTM_Auth']['user_id'] ? 'disabled' : '' ?>>
                                            <option value="1" <?= $user['status'] === 1 ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= $user['status'] === 0 ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-edit btn-outline-primary" 
                                                data-id="{{ $user['id'] }}" 
                                                data-username="{{ htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') }}" 
                                                data-email="{{ htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') }}" 
                                                data-role="{{ $user['role'] }}"
                                                data-status="{{ $user['status'] }}"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <?php if ((int)$user['id'] !== $_SESSION['ICTM_Auth']['user_id']) { ?>
                                            <button class="btn btn-sm btn-outline-danger delete" data-id="{{ $user['id'] }}" data-action="delete"><i class="fa-solid fa-trash-can"></i></button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Edit User
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const username = $(this).data('username');
            const email = $(this).data('email');
            const role = $(this).data('role');
            const status = $(this).data('status');

            $('#user_id').val(id);
            $('#user_username').val(username);
            $('#user_email').val(email);
            $('#user_role').val(role);
            $('#user_status').val(status);
            
            $('#lblPass').html('Change Password (Optional)');
            $('#formTitle').html('<i class="fa-solid fa-user-pen me-2"></i> Edit User Profile');
            $('#btnCancelEdit').show();
        });

        // Cancel Edit
        $('#btnCancelEdit').on('click', function() {
            $('#user_id').val('');
            $('#userForm')[0].reset();
            $('#lblPass').html('Password');
            $('#formTitle').html('<i class="fa-solid fa-user-plus me-2"></i> Add New User');
            $(this).hide();
        });

        // Ajax User Status change
        $(document).on('change', '.status-toggle', function() {
            const $select = $(this);
            const id = $select.data('id');
            const action = $select.data('action');
            const status = $select.val();

            $.ajax({
                type: 'POST',
                url: "{{ pathto('admin/api/update-status') }}",
                data: { id, action, status },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        flash('User status updated successfully', 'success');
                    } else {
                        flash(res.message || 'Failed to update status', 'warning');
                    }
                },
                error: function() {
                    flash('Network / Server error', 'danger');
                }
            });
        });
    });
</script>

{{ $this->view('admin/layout/footer') }}
