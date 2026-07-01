{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="card card-custom mb-4 wow animate__animated animate__fadeInUp">
    <div class="card-body card-custom-body py-3">
        <form action="{{ pathto('admin/menu/save') }}" method="POST" class="row align-items-center g-3">
            <div class="col-md-auto">
                <label for="select_menu" class="form-label mb-0 font-weight-bold">Select Menu:</label>
            </div>
            <div class="col-md-4">
                <select id="select_menu" class="form-select form-select-sm" onchange="location = this.value;">
                    <option value="{{ pathto('admin/menus') }}">-- Select Menu --</option>
                    <?php foreach ($menus as $m) { ?>
                        <option value="{{ pathto('admin/menus?menu=' . $m['id']) }}" <?= (int)$m['id'] === $active_menu_id ? 'selected' : '' ?>>{{ htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') }}</option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-auto">
                <span>or</span>
            </div>
            <div class="col-md-4">
                <input type="text" name="name" class="form-control form-control-sm" placeholder="Create new menu name" required>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i> Create Menu</button>
            </div>
        </form>
    </div>
</div>

<?php if ($active_menu_id > 0) { ?>
    <div class="row wow animate__animated animate__fadeInUp">
        <!-- Add Menu Items Panel -->
        <div class="col-md-4 mb-4">
            <div class="accordion card-custom" id="menuItemsAccordion">
                <!-- Custom Links -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingCustom">
                        <button class="accordion-button font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustom" aria-expanded="true" aria-controls="collapseCustom">
                            <i class="fa-solid fa-link me-2 text-primary"></i> Custom Links
                        </button>
                    </h2>
                    <div id="collapseCustom" class="accordion-collapse collapse show" aria-labelledby="headingCustom" data-bs-parent="#menuItemsAccordion">
                        <div class="accordion-body bg-light">
                            <div class="mb-3">
                                <label for="custom_url" class="form-label small">URL</label>
                                <input type="text" id="custom_url" class="form-control form-control-sm" placeholder="https://" value="https://">
                            </div>
                            <div class="mb-3">
                                <label for="custom_title" class="form-label small">Link Text</label>
                                <input type="text" id="custom_title" class="form-control form-control-sm" placeholder="Menu Item Label">
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100" id="btnAddCustomLink">Add to Menu</button>
                        </div>
                    </div>
                </div>

                <!-- Pages List -->
                <div class="accordion-item border-0 mt-3">
                    <h2 class="accordion-header" id="headingPages">
                        <button class="accordion-button collapsed font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <i class="fa-solid fa-file me-2 text-success"></i> Pages
                        </button>
                    </h2>
                    <div id="collapsePages" class="accordion-collapse collapse" aria-labelledby="headingPages" data-bs-parent="#menuItemsAccordion">
                        <div class="accordion-body bg-light" style="max-height: 200px; overflow-y: auto;">
                            <?php if (empty($pages)) { ?>
                                <p class="text-muted small">No published pages found.</p>
                            <?php } else { ?>
                                <?php foreach ($pages as $p) { ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input select-page-item" type="checkbox" value="{{ $p['id'] }}" data-title="{{ htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') }}" id="sel_page_{{ $p['id'] }}">
                                        <label class="form-check-label small" for="sel_page_{{ $p['id'] }}">{{ htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') }}</label>
                                    </div>
                                <?php } ?>
                                <button type="button" class="btn btn-sm btn-outline-success w-100 mt-2" id="btnAddPages">Add to Menu</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="accordion-item border-0 mt-3">
                    <h2 class="accordion-header" id="headingCats">
                        <button class="accordion-button collapsed font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCats" aria-expanded="false" aria-controls="collapseCats">
                            <i class="fa-solid fa-folder me-2 text-warning"></i> Categories
                        </button>
                    </h2>
                    <div id="collapseCats" class="accordion-collapse collapse" aria-labelledby="headingCats" data-bs-parent="#menuItemsAccordion">
                        <div class="accordion-body bg-light" style="max-height: 200px; overflow-y: auto;">
                            <?php if (empty($categories)) { ?>
                                <p class="text-muted small">No categories found.</p>
                            <?php } else { ?>
                                <?php foreach ($categories as $c) { ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input select-cat-item" type="checkbox" value="{{ $c['id'] }}" data-title="{{ htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') }}" id="sel_cat_{{ $c['id'] }}">
                                        <label class="form-check-label small" for="sel_cat_{{ $c['id'] }}">{{ htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') }}</label>
                                    </div>
                                <?php } ?>
                                <button type="button" class="btn btn-sm btn-outline-warning w-100 mt-2" id="btnAddCats">Add to Menu</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Structure Panel -->
        <div class="col-md-8 mb-4">
            <div class="card card-custom">
                <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-sitemap me-2"></i> Menu Structure</h5>
                    <form action="{{ pathto('admin/menu/delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this entire menu?');" class="m-0">
                        <input type="hidden" name="id" value="{{ $active_menu_id }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can me-1"></i> Delete Menu</button>
                    </form>
                </div>
                
                <div class="card-body card-custom-body">
                    <!-- Location Settings -->
                    <form id="menuStructureForm">
                        <input type="hidden" name="menu_id" value="{{ $active_menu_id }}">
                        
                        <?php 
                        // Find current menu details
                        $activeMenu = null;
                        foreach ($menus as $m) {
                            if ((int)$m['id'] === $active_menu_id) {
                                $activeMenu = $m;
                                break;
                            }
                        }
                        ?>

                        <div class="row align-items-center mb-4 p-3 bg-light rounded border border-light">
                            <div class="col-md-auto">
                                <label for="location" class="form-label mb-0 font-weight-bold small">Assign Menu Location:</label>
                            </div>
                            <div class="col-md-4">
                                <select name="location" id="menu_location_select" class="form-select form-select-sm">
                                    <option value="">Unassigned</option>
                                    <option value="main" <?= $activeMenu && $activeMenu['location'] === 'main' ? 'selected' : '' ?>>Header Navigation (Main)</option>
                                    <option value="footer" <?= $activeMenu && $activeMenu['location'] === 'footer' ? 'selected' : '' ?>>Footer Links</option>
                                </select>
                            </div>
                            <div class="col-md-auto ms-auto">
                                <small class="text-muted">Save the menu item structure below.</small>
                            </div>
                        </div>

                        <!-- Menu Items drag list -->
                        <div class="list-group mb-4" id="menu-items-list">
                            <?php if (empty($menu_items)) { ?>
                                <div class="text-center py-5 text-muted border rounded" id="no-items-placeholder">
                                    <i class="fa-solid fa-bars fa-3x mb-3"></i>
                                    <p>Menu is empty. Add items from the left side panel.</p>
                                </div>
                            <?php } else { ?>
                                <?php foreach ($menu_items as $item) { ?>
                                    <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 mb-2 rounded border item-node" 
                                         data-title="{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}" 
                                         data-type="{{ $item['type'] }}" 
                                         data-url="{{ htmlspecialchars($item['url'] ?? '', ENT_QUOTES, 'UTF-8') }}" 
                                         data-object-id="{{ $item['object_id'] ?? '' }}"
                                         style="cursor: grab;">
                                        <div>
                                            <i class="fa-solid fa-bars text-muted me-3 grip-handle"></i>
                                            <strong class="item-title">{{ htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') }}</strong>
                                            <span class="badge bg-secondary ms-2 small">{{ ucwords($item['type']) }}</span>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"><i class="fa-solid fa-times"></i></button>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-5 py-2"><i class="fa-solid fa-save me-2"></i> Save Menu Structure</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="card card-custom wow animate__animated animate__fadeInUp">
        <div class="card-body card-custom-body text-center py-5 text-muted">
            <i class="fa-solid fa-folder-open fa-4x mb-3 text-muted"></i>
            <h4>Choose a menu or create a new one to begin editing navigation structure.</h4>
        </div>
    </div>
<?php } ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let tempCounter = 1000;

        function checkPlaceholder() {
            if ($('#menu-items-list .item-node').length === 0) {
                if ($('#no-items-placeholder').length === 0) {
                    $('#menu-items-list').append(`
                        <div class="text-center py-5 text-muted border rounded" id="no-items-placeholder">
                            <i class="fa-solid fa-bars fa-3x mb-3"></i>
                            <p>Menu is empty. Add items from the left side panel.</p>
                        </div>
                    `);
                }
            } else {
                $('#no-items-placeholder').remove();
            }
        }

        // Add Custom Link
        $('#btnAddCustomLink').on('click', function() {
            const url = $('#custom_url').val();
            const title = $('#custom_title').val();

            if (!title) {
                alert('Please enter a label for the link.');
                return;
            }

            tempCounter++;
            const item = $(`
                <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 mb-2 rounded border item-node" 
                     data-title="${title}" 
                     data-type="custom" 
                     data-url="${url}" 
                     data-object-id="" 
                     data-temp-id="temp_${tempCounter}"
                     style="cursor: grab;">
                    <div>
                        <i class="fa-solid fa-bars text-muted me-3 grip-handle"></i>
                        <strong class="item-title">${title}</strong>
                        <span class="badge bg-secondary ms-2 small">Custom</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"><i class="fa-solid fa-times"></i></button>
                    </div>
                </div>
            `);

            $('#menu-items-list').append(item);
            $('#custom_title').val('');
            $('#custom_url').val('https://');
            checkPlaceholder();
            flash('Custom link added', 'success', 1000);
        });

        // Add Pages
        $('#btnAddPages').on('click', function() {
            const checked = $('.select-page-item:checked');
            if (checked.length === 0) return;

            checked.each(function() {
                const id = $(this).val();
                const title = $(this).data('title');
                tempCounter++;

                const item = $(`
                    <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 mb-2 rounded border item-node" 
                         data-title="${title}" 
                         data-type="page" 
                         data-url="" 
                         data-object-id="${id}" 
                         data-temp-id="temp_${tempCounter}"
                         style="cursor: grab;">
                        <div>
                            <i class="fa-solid fa-bars text-muted me-3 grip-handle"></i>
                            <strong class="item-title">${title}</strong>
                            <span class="badge bg-secondary ms-2 small">Page</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"><i class="fa-solid fa-times"></i></button>
                        </div>
                    </div>
                `);
                $('#menu-items-list').append(item);
            });

            checked.prop('checked', false);
            checkPlaceholder();
            flash('Pages added to menu', 'success', 1000);
        });

        // Add Categories
        $('#btnAddCats').on('click', function() {
            const checked = $('.select-cat-item:checked');
            if (checked.length === 0) return;

            checked.each(function() {
                const id = $(this).val();
                const title = $(this).data('title');
                tempCounter++;

                const item = $(`
                    <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 mb-2 rounded border item-node" 
                         data-title="${title}" 
                         data-type="category" 
                         data-url="" 
                         data-object-id="${id}" 
                         data-temp-id="temp_${tempCounter}"
                         style="cursor: grab;">
                        <div>
                            <i class="fa-solid fa-bars text-muted me-3 grip-handle"></i>
                            <strong class="item-title">${title}</strong>
                            <span class="badge bg-secondary ms-2 small">Category</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"><i class="fa-solid fa-times"></i></button>
                        </div>
                    </div>
                `);
                $('#menu-items-list').append(item);
            });

            checked.prop('checked', false);
            checkPlaceholder();
            flash('Categories added to menu', 'success', 1000);
        });

        // Remove menu item
        $(document).on('click', '.btn-remove-item', function() {
            $(this).closest('.item-node').remove();
            checkPlaceholder();
        });

        // Submit Menu Structure Form
        $('#menuStructureForm').on('submit', function(e) {
            e.preventDefault();

            // Enforce location update first if user changes assignment location
            const locSelect = document.getElementById('menu_location_select').value;

            // Serialize menu items order
            const items = [];
            $('#menu-items-list .item-node').each(function(index) {
                const $node = $(this);
                items.push({
                    temp_id: $node.data('temp-id') || '',
                    parent_temp_id: '', // flat hierarchy logic for simple drag list
                    title: $node.data('title'),
                    type: $node.data('type'),
                    url: $node.data('url'),
                    object_id: $node.data('object-id'),
                    menu_order: index
                });
            });

            $.ajax({
                type: 'POST',
                url: "{{ pathto('admin/menu/items/save') }}",
                data: {
                    menu_id: "{{ $active_menu_id }}",
                    menu_items_data: JSON.stringify(items)
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        // Update menu location assignment
                        $.ajax({
                            type: 'POST',
                            url: "{{ pathto('admin/menu/save') }}",
                            data: {
                                id: "{{ $active_menu_id }}",
                                name: "{{ $activeMenu ? $activeMenu['name'] : '' }}",
                                location: locSelect
                            },
                            success: function() {
                                flash('Menu structure saved successfully!', 'success');
                                setTimeout(() => { location.reload(); }, 800);
                            }
                        });
                    } else {
                        flash('Failed to save menu structure', 'warning');
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
