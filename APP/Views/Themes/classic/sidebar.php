<div class="sidebar-wrapper">
    <?php 
    $activeWidgets = $widget_settings['sidebar_widgets'] ?? [];
    foreach ($activeWidgets as $widgetKey) {
        if ($widgetKey === 'search') { ?>
            <!-- Search Widget -->
            <div class="widget-card">
                <h5 class="widget-title">Search</h5>
                <form action="{{ pathto('') }}" method="GET" class="input-group">
                    <input type="text" name="s" class="form-control form-control-sm" placeholder="Search blog..." required>
                    <button class="btn btn-sm btn-primary" type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
            </div>
        <?php } elseif ($widgetKey === 'recent_posts') { ?>
            <!-- Recent Posts Widget -->
            <div class="widget-card">
                <h5 class="widget-title">Recent Posts</h5>
                <ul class="list-unstyled mb-0">
                    <?php if (empty($widget_recent_posts)) { ?>
                        <li class="small text-muted">No recent posts.</li>
                    <?php } else { ?>
                        <?php foreach ($widget_recent_posts as $post) { ?>
                            <li class="mb-3">
                                <a href="{{ pathto($post['slug']) }}" class="text-decoration-none font-weight-bold d-block text-dark small">{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}</a>
                                <small class="text-muted" style="font-size:11px;">{{ date('M d, Y', strtotime($post['created_at'])) }}</small>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        <?php } elseif ($widgetKey === 'categories') { ?>
            <!-- Categories Widget -->
            <div class="widget-card">
                <h5 class="widget-title">Categories</h5>
                <ul class="list-unstyled mb-0">
                    <?php if (empty($widget_categories)) { ?>
                        <li class="small text-muted">No categories.</li>
                    <?php } else { ?>
                        <?php foreach ($widget_categories as $cat) { ?>
                            <li class="mb-2 d-flex justify-content-between align-items-center">
                                <a href="{{ pathto('category/' . $cat['slug']) }}" class="text-decoration-none small text-dark"><i class="fa-solid fa-folder me-2 text-primary"></i> {{ htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') }}</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        <?php } elseif ($widgetKey === 'tags') { ?>
            <!-- Tags Widget -->
            <div class="widget-card">
                <h5 class="widget-title">Tags</h5>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (empty($widget_tags)) { ?>
                        <span class="small text-muted">No tags.</span>
                    <?php } else { ?>
                        <?php foreach ($widget_tags as $tag) { ?>
                            <a href="{{ pathto('tag/' . $tag['slug']) }}" class="btn btn-sm btn-light border text-decoration-none" style="font-size: 12px;"><i class="fa-solid fa-tag me-1 text-muted"></i> {{ htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8') }}</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        <?php } elseif ($widgetKey === 'pages') { ?>
            <!-- Pages Widget -->
            <div class="widget-card">
                <h5 class="widget-title">Pages</h5>
                <ul class="list-unstyled mb-0">
                    <?php if (empty($widget_pages)) { ?>
                        <li class="small text-muted">No pages.</li>
                    <?php } else { ?>
                        <?php foreach ($widget_pages as $page) { ?>
                            <li class="mb-2">
                                <a href="{{ pathto($page['slug']) }}" class="text-decoration-none small text-dark"><i class="fa-solid fa-circle-chevron-right me-2 text-success"></i> {{ htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') }}</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        <?php }
    } ?>
</div>
