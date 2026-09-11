{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row">
    <!-- Stat Cards -->
    <div class="col-md-3 col-sm-6 mb-4 wow animate__animated animate__fadeInUp" data-wow-delay="0.1s">
        <div class="card card-custom h-100 border-start border-primary border-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase mb-2">Total Posts</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $post_count }}</h3>
                    </div>
                    <div class="icon-shape bg-primary-light text-primary rounded-circle p-3">
                        <i class="fa-solid fa-file-lines fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4 wow animate__animated animate__fadeInUp" data-wow-delay="0.2s">
        <div class="card card-custom h-100 border-start border-success border-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase mb-2">Total Pages</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $page_count }}</h3>
                    </div>
                    <div class="icon-shape bg-success-light text-success rounded-circle p-3">
                        <i class="fa-solid fa-file fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 wow animate__animated animate__fadeInUp" data-wow-delay="0.3s">
        <div class="card card-custom h-100 border-start border-warning border-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase mb-2">Comments</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $comment_count }}</h3>
                    </div>
                    <div class="icon-shape bg-warning-light text-warning rounded-circle p-3">
                        <i class="fa-solid fa-comments fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 wow animate__animated animate__fadeInUp" data-wow-delay="0.4s">
        <div class="card card-custom h-100 border-start border-danger border-4">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase mb-2">Users</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $user_count }}</h3>
                    </div>
                    <div class="icon-shape bg-danger-light text-danger rounded-circle p-3">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Posts table -->
    <div class="col-md-8 mb-4 wow animate__animated animate__fadeInLeft" data-wow-delay="0.5s">
        <div class="card card-custom h-100">
            <div class="card-header card-custom-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-clock me-2"></i> Recent Content</h5>
                <a href="{{ pathto('admin/posts') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body card-custom-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_posts)) { ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No content found. Get started by adding a post!</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($recent_posts as $post) { ?>
                                    <tr>
                                        <td class="ps-4">
                                            <strong>{{ htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucwords($post['type']) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $post['status'] === 'published' ? 'success' : 'warning' ?>">
                                                {{ ucwords($post['status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $post['created_at'] }}
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

    <!-- Quick Draft/Links -->
    <div class="col-md-4 mb-4 wow animate__animated animate__fadeInRight" data-wow-delay="0.6s">
        <div class="card card-custom h-100">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-bolt me-2"></i> Quick Actions</h5>
            </div>
            <div class="card-body card-custom-body">
                <div class="d-grid gap-3">
                    <a href="{{ pathto('admin/post/add') }}" class="btn btn-outline-primary text-start py-3"><i class="fa-solid fa-plus me-2"></i> Write a New Post</a>
                    <a href="{{ pathto('admin/page/add') }}" class="btn btn-outline-success text-start py-3"><i class="fa-solid fa-file-circle-plus me-2"></i> Add a New Page</a>
                    <a href="{{ pathto('admin/media') }}" class="btn btn-outline-info text-start py-3"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload Media File</a>
                    <?php if ($_SESSION['ICTM_Auth']['role'] === 'admin') { ?>
                        <a href="{{ pathto('admin/settings') }}" class="btn btn-outline-secondary text-start py-3"><i class="fa-solid fa-sliders me-2"></i> Site Configuration</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: rgba(43, 123, 245, 0.1); }
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
</style>

{{ $this->view('admin/layout/footer') }}
