{{ $this->view('admin/layout/header', ['title' => $title]) }}

<form id="cptEntryForm" class="wow animate__animated animate__fadeInUp">
    <input type="hidden" name="id" value="<?= $entry ? $entry->id : '' ?>">
    <input type="hidden" name="type" value="{{ $cpt['name'] }}">
    
    <div class="row">
        <!-- Main Form Column -->
        <div class="col-md-9 mb-4">
            <div class="card card-custom mb-4">
                <div class="card-body card-custom-body">
                    <div class="mb-3">
                        <label for="title" class="form-label font-weight-bold">{{ $cpt['label'] }} Title</label>
                        <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="Enter title here" value="<?= $entry ? htmlspecialchars($entry->title, ENT_QUOTES, 'UTF-8') : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Slug (Leave blank to generate automatically)</label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="e.g. entry-slug" value="<?= $entry ? htmlspecialchars($entry->slug, ENT_QUOTES, 'UTF-8') : '' ?>">
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label">Description / Content</label>
                        <textarea name="content" id="content" class="editor"><?= $entry ? $entry->content : '' ?></textarea>
                    </div>

                    <!-- Custom fields dynamically loaded based on schema -->
                    <?php if (!empty($cpt['fields'])) { ?>
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fa-solid fa-shapes text-primary me-2"></i> Custom Fields Data</h5>
                        
                        <?php foreach ($cpt['fields'] as $field) { 
                            $val = isset($meta[$field['name']]) ? $meta[$field['name']] : '';
                            ?>
                            <div class="mb-3">
                                <label for="field_{{ $field['name'] }}" class="form-label font-weight-bold">{{ ucwords($field['name']) }}</label>
                                <?php if ($field['type'] === 'textarea') { ?>
                                    <textarea name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="form-control" rows="3"><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php } elseif ($field['type'] === 'number') { ?>
                                    <input type="number" name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="form-control" value="{{ htmlspecialchars($val, ENT_QUOTES, 'UTF-8') }}">
                                <?php } else { ?>
                                    <input type="text" name="{{ $field['name'] }}" id="field_{{ $field['name'] }}" class="form-control" value="{{ htmlspecialchars($val, ENT_QUOTES, 'UTF-8') }}">
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Settings Column -->
        <div class="col-md-3 mb-4">
            <!-- Publish Options -->
            <div class="card card-custom mb-4">
                <div class="card-header card-custom-header">
                    <h6 class="mb-0"><i class="fa-solid fa-paper-plane me-2"></i> Publish Settings</h6>
                </div>
                <div class="card-body card-custom-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="draft" <?= $entry && $entry->status === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $entry && $entry->status === 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="fa-solid fa-save me-2"></i> Save Entry</button>
                    <a href="{{ pathto('admin/cpt/entries/' . $cpt['name']) }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#cptEntryForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: "{{ pathto('admin/cpt/entry/save') }}",
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        flash('Entry saved successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = "{{ pathto('admin/cpt/entries/' . $cpt['name']) }}";
                        }, 1000);
                    } else {
                        flash(res.message || 'Save failed', 'warning');
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
