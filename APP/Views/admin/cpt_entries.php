{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="card card-custom wow animate__animated animate__fadeInUp">
    <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa-solid fa-folder-open me-2"></i> {{ $cpt['label'] }} Entries</h5>
        <a href="{{ pathto('admin/cpt/entry/add/' . $cpt['name']) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add New Entry</a>
    </div>
    
    <div class="card-body card-custom-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead class="table-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th width="50%">Title</th>
                        <th width="15%">Status</th>
                        <th width="25%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $item) { ?>
                        <tr id="row{{ $item['id'] }}">
                            <td>{{ $item['id'] }}</td>
                            <td>
                                <strong>{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</strong>
                                <small class="text-muted d-block font-monospace">/{{ $item['slug'] }}</small>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-toggle" data-id="{{ $item['id'] }}" data-action="post_status" style="width: 120px;">
                                    <option value="draft" <?= $item['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= $item['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </td>
                            <td class="text-end">
                                <a href="{{ pathto('admin/cpt/entry/edit/' . $cpt['name'] . '/' . $item['id']) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button class="btn btn-sm btn-outline-danger delete" data-id="{{ $item['id'] }}" data-action="delete"><i class="fa-solid fa-trash-can"></i></button>
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
