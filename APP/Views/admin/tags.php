{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <!-- Add/Edit Column -->
    <div class="col-md-4 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0" id="formTitle"><i class="fa-solid fa-tags me-2"></i> Add New Tag</h5>
            </div>
            <div class="card-body card-custom-body">
                <form id="tagForm">
                    <input type="hidden" name="id" id="tag_id" value="">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label font-weight-bold">Name</label>
                        <input type="text" name="name" id="tag_name" class="form-control" placeholder="Tag Name" required>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (Optional)</label>
                        <input type="text" name="slug" id="tag_slug" class="form-control" placeholder="e.g. tag-slug">
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-2"></i> Save Tag</button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="btnCancelEdit" style="display:none;">Cancel Edit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Listing Column -->
    <div class="col-md-8 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-tags me-2"></i> All Tags</h5>
            </div>
            <div class="card-body card-custom-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">ID</th>
                                <th width="45%">Name</th>
                                <th width="30%">Slug</th>
                                <th width="15%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tags as $tag) { ?>
                                <tr id="row{{ $tag['id'] }}">
                                    <td>{{ $tag['id'] }}</td>
                                    <td><strong>{{ htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') }}</strong></td>
                                    <td><span class="font-monospace text-muted">{{ htmlspecialchars($tag['slug'], ENT_QUOTES, 'UTF-8') }}</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $tag['id'] }}" data-name="{{ htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') }}" data-slug="{{ htmlspecialchars($tag['slug'], ENT_QUOTES, 'UTF-8') }}"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete" data-id="{{ $tag['id'] }}" data-action="delete" data-url="{{ pathto('admin/tag/delete') }}"><i class="fa-solid fa-trash-can"></i></button>
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
        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const slug = $(this).data('slug');

            $('#tag_id').val(id);
            $('#tag_name').val(name);
            $('#tag_slug').val(slug);
            $('#formTitle').html('<i class="fa-solid fa-pen-to-square me-2"></i> Edit Tag');
            $('#btnCancelEdit').show();
        });

        // Cancel Edit
        $('#btnCancelEdit').on('click', function() {
            $('#tag_id').val('');
            $('#tagForm')[0].reset();
            $('#formTitle').html('<i class="fa-solid fa-tags me-2"></i> Add New Tag');
            $(this).hide();
        });

        // Save Tag AJAX
        $('#tagForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: "{{ pathto('admin/tag/save') }}",
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        flash('Tag saved successfully!', 'success');
                        setTimeout(() => { location.reload(); }, 1000);
                    } else if (res.status === 'duplicate') {
                        flash(res.message || 'Slug already exists', 'warning');
                    } else {
                        flash('Save failed', 'warning');
                    }
                },
                error: function() {
                    flash('Network / Server error', 'danger');
                }
            });
            return false;
        });
    });
</script>

{{ $this->view('admin/layout/footer') }}
