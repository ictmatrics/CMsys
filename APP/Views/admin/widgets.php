{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <div class="col-md-12">
        <form action="{{ pathto('admin/widgets/save') }}" method="POST">
            <!-- Sidebar Layout Options -->
            <div class="card card-custom mb-4">
                <div class="card-header card-custom-header">
                    <h5 class="mb-0"><i class="fa-solid fa-columns me-2"></i> Sidebar Display Layout</h5>
                </div>
                <div class="card-body card-custom-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <label class="d-block card p-3 shadow-sm border layout-option-card <?= $settings['sidebar_layout'] === 'none' ? 'border-primary border-2 bg-light' : '' ?>" style="cursor:pointer;">
                                <input type="radio" name="sidebar_layout" value="none" class="d-none" <?= $settings['sidebar_layout'] === 'none' ? 'checked' : '' ?>>
                                <i class="fa-solid fa-maximize fa-3x mb-3 text-secondary"></i>
                                <span class="font-weight-bold d-block">No Sidebar</span>
                                <small class="text-muted">Full width content presentation</small>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="d-block card p-3 shadow-sm border layout-option-card <?= $settings['sidebar_layout'] === 'left' ? 'border-primary border-2 bg-light' : '' ?>" style="cursor:pointer;">
                                <input type="radio" name="sidebar_layout" value="left" class="d-none" <?= $settings['sidebar_layout'] === 'left' ? 'checked' : '' ?>>
                                <i class="fa-solid fa-square-poll-vertical fa-3x mb-3 text-secondary fa-flip-horizontal"></i>
                                <span class="font-weight-bold d-block">Left Sidebar</span>
                                <small class="text-muted">Sidebar on the left side of content</small>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="d-block card p-3 shadow-sm border layout-option-card <?= $settings['sidebar_layout'] === 'right' ? 'border-primary border-2 bg-light' : '' ?>" style="cursor:pointer;">
                                <input type="radio" name="sidebar_layout" value="right" class="d-none" <?= $settings['sidebar_layout'] === 'right' ? 'checked' : '' ?>>
                                <i class="fa-solid fa-square-poll-vertical fa-3x mb-3 text-secondary"></i>
                                <span class="font-weight-bold d-block">Right Sidebar</span>
                                <small class="text-muted">Sidebar on the right side of content</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Widgets Selection -->
            <div class="card card-custom mb-4">
                <div class="card-header card-custom-header">
                    <h5 class="mb-0"><i class="fa-solid fa-shapes me-2"></i> Sidebar Widgets Settings</h5>
                </div>
                <div class="card-body card-custom-body">
                    <p class="text-muted small mb-4">Drag and drop widgets to reorder them. Select which widgets will show in the sidebar wrapper.</p>
                    
                    <div class="row" id="sortableWidgets">
                        <?php 
                        $activeWidgets = $settings['sidebar_widgets'] ?? []; 
                        $availableWidgets = [
                            'search' => ['title' => 'Search Widget', 'desc' => 'Dynamic input keyword search box', 'icon' => 'fa-search'],
                            'recent_posts' => ['title' => 'Recent Posts', 'desc' => 'List of latest published posts', 'icon' => 'fa-clock'],
                            'trending_posts' => ['title' => 'Trending Posts', 'desc' => 'Time-decay trending published posts', 'icon' => 'fa-fire'],
                            'categories' => ['title' => 'Categories List', 'desc' => 'Vertical folders link list', 'icon' => 'fa-folder'],
                            'tags' => ['title' => 'Tags Cloud', 'desc' => 'Interactive tag search anchors', 'icon' => 'fa-tags'],
                            'pages' => ['title' => 'Pages list', 'desc' => 'Bullet-point navigation text list', 'icon' => 'fa-file']
                        ];
                        
                        $sortedAvailable = [];
                        foreach ($activeWidgets as $key) {
                            if (isset($availableWidgets[$key])) {
                                $sortedAvailable[$key] = $availableWidgets[$key];
                            }
                        }
                        foreach ($availableWidgets as $key => $w) {
                            if (!isset($sortedAvailable[$key])) {
                                $sortedAvailable[$key] = $w;
                            }
                        }
                        $availableWidgets = $sortedAvailable;
                        ?>

                        <?php foreach ($availableWidgets as $key => $w) { ?>
                            <div class="col-12 mb-3 widget-card-item">
                                <input type="hidden" name="widgets_order[]" value="{{ $key }}">
                                <div class="card p-3 shadow-sm border h-100 d-flex flex-row align-items-center">
                                    <div class="drag-handle text-muted me-3" style="cursor: grab;">
                                        <i class="fa-solid fa-grip-vertical fa-lg"></i>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input form-check-input-lg" type="checkbox" name="widgets[]" value="{{ $key }}" id="widget_{{ $key }}" <?= in_array($key, $activeWidgets, true) ? 'checked' : '' ?> style="transform: scale(1.3); cursor: pointer;">
                                    </div>
                                    <div class="icon-shape bg-light text-secondary rounded p-3 me-3">
                                        <i class="fa-solid {{ $w['icon'] }} fa-xl"></i>
                                    </div>
                                    <div>
                                        <label for="widget_{{ $key }}" class="form-label mb-0 font-weight-bold" style="cursor:pointer;">{{ $w['title'] }}</label>
                                        <p class="text-muted small mb-0">{{ $w['desc'] }}</p>
                                    </div>
                                    
                                    <?php if ($key === 'recent_posts' || $key === 'trending_posts') { ?>
                                        <div class="ms-auto d-flex align-items-center gap-2 limit-config-input" onclick="event.stopPropagation();">
                                            <span class="text-muted small fw-semibold">Display Limit:</span>
                                            <input type="number" name="{{ $key }}_limit" class="form-control form-control-sm" min="1" max="20" value="<?= htmlspecialchars((string)($settings[$key . '_limit'] ?? 5), ENT_QUOTES, 'UTF-8') ?>" style="width: 75px; display: inline-block;">
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success px-5 py-2"><i class="fa-solid fa-save me-2"></i> Save Widget Configurations</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Layout card visual selection toggle (Vanilla JS)
        document.querySelectorAll('.layout-option-card input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.layout-option-card').forEach(card => {
                    card.classList.remove('border-primary', 'border-2', 'bg-light');
                });
                if (this.checked) {
                    this.closest('.layout-option-card').classList.add('border-primary', 'border-2', 'bg-light');
                }
            });
        });

        // HTML5 drag and drop sorting logic (restricted to drag-handle)
        const container = document.getElementById('sortableWidgets');
        let dragEl = null;

        container.querySelectorAll('.widget-card-item').forEach(item => {
            const handle = item.querySelector('.drag-handle');
            if (handle) {
                handle.addEventListener('mousedown', () => {
                    item.setAttribute('draggable', 'true');
                });
                handle.addEventListener('mouseup', () => {
                    item.removeAttribute('draggable');
                });
            }

            item.addEventListener('dragstart', function(e) {
                dragEl = this;
                e.dataTransfer.effectAllowed = 'move';
                this.style.opacity = '0.5';
            });

            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                
                const rect = this.getBoundingClientRect();
                const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                container.insertBefore(dragEl, next ? this.nextSibling : this);
            });

            item.addEventListener('dragend', function() {
                this.style.opacity = '1';
                this.removeAttribute('draggable');
                dragEl = null;
            });
        });
    });
</script>

{{ $this->view('admin/layout/footer') }}
