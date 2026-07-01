{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <div class="col-md-9 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-shapes me-2"></i> Page Builder Layout Canvas</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectElement"><i class="fa-solid fa-plus me-1"></i> Add Element</button>
                </div>
            </div>
            
            <div class="card-body card-custom-body">
                <div class="list-group" id="canvas-blocks-list" style="min-height: 200px;">
                    <?php if (empty($blocks)) { ?>
                        <div class="text-center py-5 text-muted border rounded" id="no-blocks-placeholder">
                            <i class="fa-solid fa-cubes fa-3x mb-3"></i>
                            <p>Your layout canvas is empty. Add a layout block or content element to get started.</p>
                        </div>
                    <?php } else { ?>
                        <?php foreach ($blocks as $idx => $b) { ?>
                            <div class="list-group-item list-group-item-action mb-3 rounded border block-node" data-type="{{ $b['type'] }}">
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                    <div>
                                        <i class="fa-solid fa-bars text-muted me-3" style="cursor:grab;"></i>
                                        <strong class="text-primary">{{ ucwords($b['type']) }} Element</strong>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-block"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                </div>
                                
                                <div class="block-editor-fields">
                                    <?php if ($b['type'] === 'heading') { ?>
                                        <div class="mb-2">
                                            <input type="text" class="form-control form-control-sm block-data-title" placeholder="Heading Text" value="{{ htmlspecialchars($b['title'] ?? '', ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-select form-select-sm block-data-size">
                                                    <option value="h1" <?= ($b['size'] ?? '') === 'h1' ? 'selected' : '' ?>>H1 (Large)</option>
                                                    <option value="h2" <?= ($b['size'] ?? '') === 'h2' ? 'selected' : '' ?>>H2 (Medium)</option>
                                                    <option value="h3" <?= ($b['size'] ?? '') === 'h3' ? 'selected' : '' ?>>H3 (Small)</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } elseif ($b['type'] === 'text') { ?>
                                        <div class="mb-2">
                                            <textarea class="form-control form-control-sm block-data-content" rows="4" placeholder="Enter paragraph content..."><?= htmlspecialchars($b['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                        </div>
                                    <?php } elseif ($b['type'] === 'image') { ?>
                                        <div class="mb-2">
                                            <input type="text" class="form-control form-control-sm block-data-url mb-2 select-image-field" placeholder="Image URL / Path" value="{{ htmlspecialchars($b['url'] ?? '', ENT_QUOTES, 'UTF-8') }}">
                                            <input type="text" class="form-control form-control-sm block-data-alt" placeholder="Alt text description" value="{{ htmlspecialchars($b['alt'] ?? '', ENT_QUOTES, 'UTF-8') }}">
                                        </div>
                                    <?php } elseif ($b['type'] === 'columns') { ?>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control form-control-sm block-data-col1 mb-2" placeholder="Left Column content" value="{{ htmlspecialchars($b['col1'] ?? '', ENT_QUOTES, 'UTF-8') }}">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control form-control-sm block-data-col2 mb-2" placeholder="Right Column content" value="{{ htmlspecialchars($b['col2'] ?? '', ENT_QUOTES, 'UTF-8') }}">
                                            </div>
                                        </div>
                                    <?php } elseif ($b['type'] === 'php') { ?>
                                        <div class="mb-2">
                                            <textarea class="form-control form-control-sm block-data-code font-monospace" rows="6" placeholder="<?php echo 'echo \'Hello World\';'; ?>"><?= htmlspecialchars($b['code'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                            <div class="form-text small"><i class="fa-solid fa-circle-exclamation me-1 text-warning"></i> Executes as dynamic PHP code. Always validate outputs.</div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Panel -->
    <div class="col-md-3 mb-4">
        <div class="card card-custom mb-4">
            <div class="card-header card-custom-header">
                <h6 class="mb-0"><i class="fa-solid fa-paper-plane me-2"></i> Page Builder Status</h6>
            </div>
            <div class="card-body card-custom-body">
                <p class="text-muted small">Construct your layout and save to publish block elements.</p>
                <button type="button" class="btn btn-success w-100" id="btnSaveBuilder"><i class="fa-solid fa-save me-2"></i> Save Layout</button>
                <a href="{{ pathto('admin/pages') }}" class="btn btn-outline-secondary w-100 mt-2">Back to Pages</a>
            </div>
        </div>
    </div>
</div>

<!-- Element Selection Modal -->
<div class="modal fade" id="elementSelectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-cubes"></i> Choose Element Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="row g-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary w-100 p-4 h-100 select-block-btn" data-type="heading">
                            <i class="fa-solid fa-heading fa-2x mb-2 d-block"></i> Heading
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success w-100 p-4 h-100 select-block-btn" data-type="text">
                            <i class="fa-solid fa-paragraph fa-2x mb-2 d-block"></i> Rich Text
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning w-100 p-4 h-100 select-block-btn" data-type="image">
                            <i class="fa-solid fa-image fa-2x mb-2 d-block"></i> Image
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info w-100 p-4 h-100 select-block-btn" data-type="columns">
                            <i class="fa-solid fa-columns fa-2x mb-2 d-block"></i> Two Columns
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-danger w-100 p-4 h-100 select-block-btn" data-type="php">
                            <i class="fa-solid fa-code fa-2x mb-2 d-block"></i> Custom PHP Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = new bootstrap.Modal(document.getElementById('elementSelectModal'));

        $('#btnSelectElement').on('click', function() {
            modal.show();
        });

        // Add a block to canvas
        $('.select-block-btn').on('click', function() {
            const type = $(this).data('type');
            modal.hide();
            $('#no-blocks-placeholder').remove();

            let fieldsHtml = '';
            if (type === 'heading') {
                fieldsHtml = `
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm block-data-title" placeholder="Heading Text" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select form-select-sm block-data-size">
                                <option value="h1">H1 (Large)</option>
                                <option value="h2" selected>H2 (Medium)</option>
                                <option value="h3">H3 (Small)</option>
                            </select>
                        </div>
                    </div>
                `;
            } else if (type === 'text') {
                fieldsHtml = `
                    <div class="mb-2">
                        <textarea class="form-control form-control-sm block-data-content" rows="4" placeholder="Enter paragraph content..." required></textarea>
                    </div>
                `;
            } else if (type === 'image') {
                fieldsHtml = `
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm block-data-url mb-2 select-image-field" placeholder="Image URL / Path" required>
                        <input type="text" class="form-control form-control-sm block-data-alt" placeholder="Alt text description">
                    </div>
                `;
            } else if (type === 'columns') {
                fieldsHtml = `
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control form-control-sm block-data-col1 mb-2" placeholder="Left Column content">
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control form-control-sm block-data-col2 mb-2" placeholder="Right Column content">
                        </div>
                    </div>
                `;
            } else if (type === 'php') {
                fieldsHtml = `
                    <div class="mb-2">
                        <textarea class="form-control form-control-sm block-data-code font-monospace" rows="6" placeholder="<?php echo 'echo \'Hello World\';'; ?>" required></textarea>
                        <div class="form-text small"><i class="fa-solid fa-circle-exclamation me-1 text-warning"></i> Executes as dynamic PHP code. Always validate outputs.</div>
                    </div>
                `;
            }

            const block = $(`
                <div class="list-group-item list-group-item-action mb-3 rounded border block-node animate__animated animate__fadeInUp" data-type="${type}">
                    <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                        <div>
                            <i class="fa-solid fa-bars text-muted me-3" style="cursor:grab;"></i>
                            <strong class="text-primary">${type.toUpperCase()} Element</strong>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-block"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>
                    <div class="block-editor-fields">
                        ${fieldsHtml}
                    </div>
                </div>
            `);

            $('#canvas-blocks-list').append(block);
            flash('Element added to layout', 'success', 1000);
        });

        // Remove a block
        $(document).on('click', '.btn-remove-block', function() {
            $(this).closest('.block-node').remove();
            if ($('#canvas-blocks-list .block-node').length === 0) {
                $('#canvas-blocks-list').append(`
                    <div class="text-center py-5 text-muted border rounded" id="no-blocks-placeholder">
                        <i class="fa-solid fa-cubes fa-3x mb-3"></i>
                        <p>Your layout canvas is empty. Add a layout block or content element to get started.</p>
                    </div>
                `);
            }
        });

        // Save layout canvas
        $('#btnSaveBuilder').on('click', function() {
            const blocks = [];
            $('#canvas-blocks-list .block-node').each(function() {
                const $node = $(this);
                const type = $node.data('type');
                const data = { type: type };

                if (type === 'heading') {
                    data.title = $node.find('.block-data-title').val();
                    data.size = $node.find('.block-data-size').val();
                } else if (type === 'text') {
                    data.content = $node.find('.block-data-content').val();
                } else if (type === 'image') {
                    data.url = $node.find('.block-data-url').val();
                    data.alt = $node.find('.block-data-alt').val();
                } else if (type === 'columns') {
                    data.col1 = $node.find('.block-data-col1').val();
                    data.col2 = $node.find('.block-data-col2').val();
                } else if (type === 'php') {
                    data.code = $node.find('.block-data-code').val();
                }

                blocks.push(data);
            });

            $.ajax({
                type: 'POST',
                url: "{{ pathto('admin/page-editor/save') }}",
                data: {
                    page_id: "{{ $page->id }}",
                    blocks_data: JSON.stringify(blocks)
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        flash('Layout builder saved successfully!', 'success');
                    } else {
                        flash('Save failed: ' + res.message, 'warning');
                    }
                },
                error: function() {
                    flash('Network / Server error', 'danger');
                }
            });
        });
    });
</script>
