{{ $this->view('admin/layout/header', ['title' => $title]) }}

<form id="postForm" class="wow animate__animated animate__fadeInUp">
    <input type="hidden" name="id" value="<?= $post ? $post->id : '' ?>">
    <div class="row">
        <!-- Main Form Column -->
        <div class="col-md-9 mb-4">
            <div class="card card-custom mb-4">
                <div class="card-body card-custom-body">
                    <div class="mb-3">
                        <label for="title" class="form-label font-weight-bold">Post Title</label>
                        <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="Enter title here" value="<?= $post ? htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8') : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Slug (Leave blank to generate automatically)</label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="e.g. my-first-post" value="<?= $post ? htmlspecialchars($post->slug, ENT_QUOTES, 'UTF-8') : '' ?>">
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label">Post Content</label>
                        <textarea name="content" id="content" class="editor"><?= $post ? $post->content : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt / Summary</label>
                        <textarea name="excerpt" id="excerpt" class="form-control" rows="2" placeholder="Write a short summary..."><?= $post ? htmlspecialchars($post->excerpt ?? '', ENT_QUOTES, 'UTF-8') : '' ?></textarea>
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
                        <input type="text" name="seo_title" id="seo_title" class="form-control" placeholder="Defaults to post title" value="<?= isset($meta['seo_title']) ? htmlspecialchars($meta['seo_title'], ENT_QUOTES, 'UTF-8') : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="seo_description" class="form-label">Meta Description</label>
                        <textarea name="seo_description" id="seo_description" class="form-control" rows="3" placeholder="Description for Google search results..."><?= isset($meta['seo_description']) ? htmlspecialchars($meta['seo_description'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
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
                            <option value="draft" <?= $post && $post->status === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $post && $post->status === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="expired" <?= $post && $post->status === 'expired' ? 'selected' : '' ?>>Expired</option>
                            <option value="scheduled" <?= $post && $post->status === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                        </select>
                    </div>

                    <div class="mb-4" id="scheduled_date_wrapper" style="<?= $post && $post->status === 'scheduled' ? '' : 'display:none;' ?>">
                        <label for="publish_date" class="form-label">Publish Date & Time</label>
                        <input type="datetime-local" name="publish_date" id="publish_date" class="form-control" value="<?= $post && $post->publish_date ? date('Y-m-d\TH:i', strtotime($post->publish_date)) : '' ?>">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="fa-solid fa-save me-2"></i> Save Post</button>
                    <a href="{{ pathto('admin/posts') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                </div>
            </div>

            <!-- Categories -->
            <div class="card card-custom mb-4">
                <div class="card-header card-custom-header">
                    <h6 class="mb-0"><i class="fa-solid fa-folder me-2"></i> Categories</h6>
                </div>
                <div class="card-body card-custom-body" style="max-height: 200px; overflow-y: auto;">
                    <?php if (empty($categories)) { ?>
                        <p class="text-muted small mb-0">No categories found. <a href="{{ pathto('admin/categories') }}">Add one here</a>.</p>
                    <?php } else { ?>
                        <?php foreach ($categories as $cat) { ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $cat['id'] }}" id="cat_{{ $cat['id'] }}" <?= in_array((int)$cat['id'], $post_categories, true) ? 'checked' : '' ?>>
                                <label class="form-check-input-label" for="cat_{{ $cat['id'] }}">{{ htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') }}</label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <!-- Tags -->
            <div class="card card-custom mb-4">
                <div class="card-header card-custom-header">
                    <h6 class="mb-0"><i class="fa-solid fa-tags me-2"></i> Tags</h6>
                </div>
                <div class="card-body card-custom-body">
                    <input type="text" name="tags" id="tags_input" class="form-control" placeholder="Add tag, hit Enter" value="{{ $post_tags }}">
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card card-custom">
                <div class="card-header card-custom-header">
                    <h6 class="mb-0"><i class="fa-solid fa-image me-2"></i> Featured Image</h6>
                </div>
                <div class="card-body card-custom-body">
                    <input type="text" name="featured_image" id="featured_image" class="form-control mb-2" placeholder="Image URL path" value="<?= isset($meta['featured_image']) ? htmlspecialchars($meta['featured_image'], ENT_QUOTES, 'UTF-8') : '' ?>">
                    <div class="text-center">
                        <?php if (isset($meta['featured_image']) && !empty($meta['featured_image'])) { ?>
                            <img id="featured_image_preview" src="{{ pathto($meta['featured_image']) }}" class="img-fluid rounded mb-2" style="max-height: 150px;">
                        <?php } else { ?>
                            <img id="featured_image_preview" src="" class="img-fluid rounded mb-2" style="display:none; max-height: 150px;">
                        <?php } ?>
                        <button type="button" class="btn btn-sm btn-outline-primary w-100" id="btnSelectMedia"><i class="fa-solid fa-images me-1"></i> Choose from Library</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Media Selection Modal -->
<div class="modal fade" id="mediaSelectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-images"></i> Select Featured Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 450px; overflow-y: auto;">
                <div class="row row-cols-3 row-cols-md-4 g-3" id="modalMediaList">
                    <!-- Media items populated dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle Schedule date input
        $('#status').on('change', function() {
            if ($(this).val() === 'scheduled') {
                $('#scheduled_date_wrapper').slideDown(250);
            } else {
                $('#scheduled_date_wrapper').slideUp(250);
            }
        });

        // Initialize Tagify for post keywords
        const tagsInputEl = document.getElementById('tags_input');
        if (tagsInputEl && typeof Tagify !== 'undefined') {
            new Tagify(tagsInputEl, {
                originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
            });
        }

        // Handle Media Selector
        $('#btnSelectMedia').on('click', function() {
            $.ajax({
                url: "{{ pathto('admin/media') }}",
                type: 'GET',
                success: function(html) {
                    // Extract items by temporary parsing or fetching JSON lists
                    // For simplicity, we query a separate dynamic JSON endpoint or query the DOM
                    // Let's call a simplified media list API
                    $.ajax({
                        url: "{{ pathto('admin/media') }}",
                        headers: { 'Accept': 'application/json' },
                        dataType: 'html',
                        success: function(data) {
                            // Let's scrape the media cards from administrative list
                            const $temp = $('<div>').append($.parseHTML(data));
                            const $images = $temp.find('.media-item-card');
                            
                            $('#modalMediaList').empty();
                            if ($images.length === 0) {
                                $('#modalMediaList').html('<div class="col-12 text-center text-muted">No media elements inside library. Upload some in the Media Library tab first.</div>');
                            } else {
                                $images.each(function() {
                                    const src = $(this).data('path');
                                    const filename = $(this).data('name');
                                    const $col = $(`
                                        <div class="col text-center">
                                            <div class="card h-100 shadow-sm border select-media-card" data-path="${src}" style="cursor:pointer;">
                                                <img src="${src}" class="card-img-top p-1" style="height:100px; object-fit:cover;">
                                                <div class="card-footer p-1 bg-light">
                                                    <span class="text-truncate d-block small" style="max-width:100%;">${filename}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                    $('#modalMediaList').append($col);
                                });
                            }
                            
                            const myModal = new bootstrap.Modal(document.getElementById('mediaSelectModal'));
                            myModal.show();
                        }
                    });
                }
            });
        });

        // Select Image from modal
        $(document).on('click', '.select-media-card', function() {
            const path = $(this).data('path');
            // Remove full BASE_URL if absolute to keep it relative
            const cleanedPath = path.replace("{{ pathto('') }}", "");
            $('#featured_image').val(cleanedPath);
            $('#featured_image_preview').attr('src', path).show();
            bootstrap.Modal.getInstance(document.getElementById('mediaSelectModal')).hide();
        });

        // Live preview on input change
        $('#featured_image').on('input', function() {
            const val = $(this).val();
            if (val) {
                const fullPath = val.startsWith('http') ? val : "{{ pathto('') }}" + val;
                $('#featured_image_preview').attr('src', fullPath).show();
            } else {
                $('#featured_image_preview').hide();
            }
        });

        // Handle AJAX submit
        $('#postForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: "{{ pathto('admin/post/save') }}",
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        flash('Post saved successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = "{{ pathto('admin/posts') }}";
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
