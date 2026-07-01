{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <!-- Register CPT Form -->
    <div class="col-md-5 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-plus me-2"></i> Register Custom Post Type</h5>
            </div>
            <div class="card-body card-custom-body">
                <form action="{{ pathto('admin/cpt/save') }}" method="POST" id="cptForm">
                    <div class="mb-3">
                        <label for="name" class="form-label font-weight-bold">CPT Name / Key</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. portfolio" required>
                        <div class="form-text small">Lowercase, no spaces or special characters.</div>
                    </div>

                    <div class="mb-3">
                        <label for="label" class="form-label font-weight-bold">Display Label (Plural)</label>
                        <input type="text" name="label" class="form-control" placeholder="e.g. Portfolios" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="e.g. Portfolio showcase CPT"></textarea>
                    </div>

                    <!-- Custom fields configuration -->
                    <div class="mb-4">
                        <label class="form-label font-weight-bold d-block">Custom Fields Schema</label>
                        
                        <div id="cpt-fields-wrapper" class="mb-3 border p-3 rounded bg-light">
                            <!-- Dynamically added fields -->
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" id="btnAddField"><i class="fa-solid fa-plus me-1"></i> Add Custom Field</button>
                    </div>

                    <input type="hidden" name="fields_data" id="fields_data" value="[]">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-2"></i> Register Post Type</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Registered CPTs Listing -->
    <div class="col-md-7 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-gear me-2"></i> Registered Custom Post Types</h5>
            </div>
            <div class="card-body card-custom-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Label</th>
                                <th>Name / Slug</th>
                                <th>Fields Count</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cpts)) { ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No custom post types registered yet.</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($cpts as $slug => $cpt) { ?>
                                    <tr id="row{{ $slug }}">
                                        <td class="ps-4">
                                            <strong>{{ htmlspecialchars($cpt['label'], ENT_QUOTES, 'UTF-8') }}</strong>
                                            <small class="text-muted d-block">{{ htmlspecialchars($cpt['description'] ?? '', ENT_QUOTES, 'UTF-8') }}</small>
                                        </td>
                                        <td><code class="font-monospace">{{ $slug }}</code></td>
                                        <td>{{ count($cpt['fields'] ?? []) }}</td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <a href="{{ pathto('admin/cpt/entries/' . $slug) }}" class="btn btn-sm btn-outline-primary me-2"><i class="fa-solid fa-folder-open"></i> Entries</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-cpt" data-slug="{{ $slug }}"><i class="fa-solid fa-trash-can"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
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
        let fieldCounter = 0;

        $('#btnAddField').on('click', function() {
            fieldCounter++;
            const field = $(`
                <div class="row g-2 align-items-center mb-2 field-row" id="f_row_${fieldCounter}">
                    <div class="col-5">
                        <input type="text" class="form-control form-control-sm f-name" placeholder="field_name" required>
                    </div>
                    <div class="col-4">
                        <select class="form-select form-select-sm f-type">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                        </select>
                    </div>
                    <div class="col-3 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-field"><i class="fa-solid fa-times"></i></button>
                    </div>
                </div>
            `);
            $('#cpt-fields-wrapper').append(field);
        });

        $(document).on('click', '.btn-remove-field', function() {
            $(this).closest('.field-row').remove();
        });

        // Form submit
        $('#cptForm').on('submit', function(e) {
            const fields = [];
            $('.field-row').each(function() {
                const name = $(this).find('.f-name').val();
                const type = $(this).find('.f-type').val();
                
                // Sanitize field name
                const cleanName = name.toLowerCase().replace(/[^a-z0-9_]/g, '');

                if (cleanName) {
                    fields.push({
                        name: cleanName,
                        type: type
                    });
                }
            });

            $('#fields_data').val(JSON.stringify(fields));
            return true;
        });

        // Delete CPT
        $(document).on('click', '.btn-delete-cpt', function() {
            const slug = $(this).data('slug');
            if (!confirm('Are you sure you want to delete this custom post type? ALL associated posts of this type will be deleted.')) return;

            $.ajax({
                type: 'POST',
                url: "{{ pathto('admin/cpt/delete') }}",
                data: { slug },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        flash('CPT deleted successfully!', 'danger');
                        $(`#row${slug}`).fadeOut(300, function() { $(this).remove(); });
                    } else {
                        flash('Failed to delete CPT', 'warning');
                    }
                },
                error: function() {
                    flash('Network / Server error', 'danger');
                }
            });
        });
    });
</script>
