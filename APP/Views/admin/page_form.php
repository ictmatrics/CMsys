{{ $this->view('admin/layout/header', ['title' => $title]) }}

<form id="pageForm" class="wow animate__animated animate__fadeInUp">
    <input type="hidden" name="id" value="{{ $page ? $page->id : '' }}">
    <div class="row">
        <!-- Main Form Column -->
        <div class="col-md-9 mb-4">
            <div class="card card-custom mb-4">
                <div class="card-body card-custom-body">
                    <div class="mb-3">
                        <label for="title" class="form-label font-weight-bold">Page Title</label>
                        <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="Enter title here" value="{{ $page ? htmlspecialchars($page->title, ENT_QUOTES, 'UTF-8') : '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Slug <span class="text-muted small">(Leave blank to generate automatically)</span></label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="e.g. about-us" value="{{ $page ? htmlspecialchars($page->slug, ENT_QUOTES, 'UTF-8') : '' }}">
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label">Page Content</label>
                        <!-- class="editor" triggers Summernote init in layout/footer.php -->
                        <textarea name="content" id="content" class="editor">{{ $page ? $page->content : '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SEO & Meta Card -->
            <div class="card card-custom">
                <div class="card-header card-custom-header">
                    <h6 class="mb-0"><i class="fa-solid fa-search me-2"></i> Search Engine Optimization (SEO)</h6>
                </div>
                <div class="card-body card-custom-body">
                    <div class="mb-3">
                        <label for="seo_title" class="form-label">SEO Title</label>
                        <input type="text" name="seo_title" id="seo_title" class="form-control" placeholder="Defaults to page title" value="{{ isset($meta['seo_title']) ? htmlspecialchars($meta['seo_title'], ENT_QUOTES, 'UTF-8') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="seo_description" class="form-label">Meta Description</label>
                        <textarea name="seo_description" id="seo_description" class="form-control" rows="3" placeholder="Description for Google search results...">{{ isset($meta['seo_description']) ? htmlspecialchars($meta['seo_description'], ENT_QUOTES, 'UTF-8') : '' }}</textarea>
                    </div>
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
                            <option value="draft" {{ $page && $page->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $page && $page->status === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="page_layout" class="form-label">Page Layout</label>
                        <select name="page_layout" id="page_layout" class="form-select">
                            <option value="full_width"    {{ isset($meta['page_layout']) && $meta['page_layout'] === 'full_width'    ? 'selected' : '' }}>Full Width</option>
                            <option value="sidebar_left"  {{ isset($meta['page_layout']) && $meta['page_layout'] === 'sidebar_left'  ? 'selected' : '' }}>Left Sidebar</option>
                            <option value="sidebar_right" {{ isset($meta['page_layout']) && $meta['page_layout'] === 'sidebar_right' ? 'selected' : '' }}>Right Sidebar</option>
                        </select>
                    </div>

                    <button type="submit" id="btnSavePage" class="btn btn-primary w-100 py-2">
                        <i class="fa-solid fa-save me-2"></i> Save Page
                    </button>
                    <a href="{{ pathto('admin/pages') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card card-custom">
                <div class="card-header card-custom-header">
                    <h6 class="mb-0"><i class="fa-solid fa-image me-2"></i> Featured Image</h6>
                </div>
                <div class="card-body card-custom-body">
                    <input type="text" name="featured_image" id="featured_image" class="form-control mb-2" placeholder="Image URL path" value="{{ isset($meta['featured_image']) ? htmlspecialchars($meta['featured_image'], ENT_QUOTES, 'UTF-8') : '' }}">
                    <div class="text-center">
                        <?php if (isset($meta['featured_image']) && !empty($meta['featured_image'])) { ?>
                            <img id="featured_image_preview" src="{{ pathto($meta['featured_image']) }}" class="img-fluid rounded mb-2" style="max-height:150px;">
                        <?php } else { ?>
                            <img id="featured_image_preview" src="" class="img-fluid rounded mb-2" style="display:none; max-height:150px;">
                        <?php } ?>
                        <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-select-media" id="btnSelectMedia" data-target="featured_image">
                            <i class="fa-solid fa-images me-1"></i> Choose from Library
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
        // Live preview on manual URL input
        $('#featured_image').on('input', function () {
            const val = $(this).val();
            if (val) {
                const fullPath = val.startsWith('http') ? val : "{{ pathto('') }}" + val;
                $('#featured_image_preview').attr('src', fullPath).show();
            } else {
                $('#featured_image_preview').hide();
            }
        });

    // ── 2. AJAX Save — sync Summernote content FIRST ─────────────────────────────
    $('#pageForm').on('submit', function (e) {
        e.preventDefault();

        // Flush Summernote HTML into the underlying textarea before serialize()
        if ($.fn.summernote) {
            $('#content').val($('#content').summernote('code'));
        }

        $.ajax({
            type: 'POST',
            url: "{{ pathto('admin/page/save') }}",
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    flash('Page saved successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = "{{ pathto('admin/pages') }}";
                    }, 1200);
                } else {
                    flash(res.message || 'Save failed', 'warning');
                }
            },
            error: function () {
                flash('Network / Server error', 'danger');
            }
        });
        return false;
    });
});
</script>

{{ $this->view('admin/layout/footer') }}
