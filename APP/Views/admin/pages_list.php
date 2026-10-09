{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="card card-custom wow animate__animated animate__fadeInUp">
    <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa-solid fa-file me-2"></i> Content Pages</h5>
        <a href="{{ pathto('admin/page/add') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add New Page</a>
    </div>
    
    <div class="card-body card-custom-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable" data-order='[[0, "desc"]]'>
                <thead class="table-light">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="35%">Title</th>
                        <th width="15%">Status</th>
                        <th width="15%">Page Builder</th>
                        <th width="15%">Created At</th>
                        <th width="15%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page) { ?>
                        <tr id="row{{ $page['id'] }}">
                            <td>{{ $page['id'] }}</td>
                            <td>
                                <strong class="d-block">{{ htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') }}</strong>
                                <small class="text-muted font-monospace">/{{ $page['slug'] }}</small>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-toggle" data-id="{{ $page['id'] }}" data-action="post_status" style="width: 120px;">
                                    <option value="draft" <?= $page['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </td>
                            <td>
                                <a href="{{ pathto('admin/page-editor/' . $page['id']) }}" class="btn btn-sm btn-outline-info font-weight-bold"><i class="fa-solid fa-shapes me-1"></i> Block Builder</a>
                            </td>
                            <td>
                                {{ $page['created_at'] }}
                            </td>
                            <td class="text-end">
                                <a href="{{ pathto('admin/page/edit/' . $page['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button class="btn btn-sm btn-outline-danger delete" data-id="{{ $page['id'] }}" data-action="delete" data-url="{{ pathto('admin/page/delete') }}"><i class="fa-solid fa-trash-can"></i></button>
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
