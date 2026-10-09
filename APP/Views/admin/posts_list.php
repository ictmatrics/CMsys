{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="card card-custom wow animate__animated animate__fadeInUp">
    <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa-solid fa-file-lines me-2"></i> Content Posts</h5>
        <a href="{{ pathto('admin/post/add') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add New Post</a>
    </div>
    
    <div class="card-body card-custom-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable" data-order='[[0, "desc"]]'>
                <thead class="table-light">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="40%">Title</th>
                        <th width="15%">Status</th>
                        <th width="20%">Publish Date</th>
                        <th width="20%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post) { ?>
                        <tr id="row{{ $post['id'] }}">
                            <td>{{ $post['id'] }}</td>
                            <td>
                                <strong class="d-block">{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}</strong>
                                <small class="text-muted font-monospace">/{{ $post['slug'] }}</small>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-toggle" data-id="{{ $post['id'] }}" data-action="post_status" style="width: 120px;">
                                    <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                    <option value="expired" <?= $post['status'] === 'expired' ? 'selected' : '' ?>>Expired</option>
                                    <option value="scheduled" <?= $post['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                </select>
                            </td>
                            <td>
                                {{ $post['publish_date'] ?? $post['created_at'] }}
                            </td>
                            <td class="text-end">
                                <a href="{{ pathto('admin/post/edit/' . $post['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button class="btn btn-sm btn-outline-danger delete" data-id="{{ $post['id'] }}" data-action="delete" data-url="{{ pathto('admin/post/delete') }}"><i class="fa-solid fa-trash-can"></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle asynchronous Ajax status toggles
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
                        flash('Status updated successfully', 'success');
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
