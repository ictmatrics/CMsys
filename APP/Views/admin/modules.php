{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <!-- Install Module Zip Form -->
    <div class="col-md-4 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Install New Module</h5>
            </div>
            <div class="card-body card-custom-body">
                <form action="{{ pathto('admin/module/upload') }}" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="module_zip" class="form-label small">Module ZIP Archive (.zip)</label>
                        <input type="file" name="module_zip" id="module_zip" class="form-control" accept=".zip" required>
                        <div class="form-text small">Upload module archive containing <code>manifest.json</code> in root.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-upload me-2"></i> Upload & Install</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Installed Modules list -->
    <div class="col-md-8 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-puzzle-piece me-2"></i> Installed Modules</h5>
            </div>
            <div class="card-body card-custom-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Module Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($modules)) { ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No custom modules installed yet.</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($modules as $mod) { ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <strong class="d-block text-dark">{{ htmlspecialchars($mod['name'], ENT_QUOTES, 'UTF-8') }}</strong>
                                                <span class="badge bg-light text-secondary border fs-8" title="Located in /app/vendors/modules/{{ htmlspecialchars($mod['name'], ENT_QUOTES, 'UTF-8') }}"><i class="fa-solid fa-cube me-1 text-primary"></i>Vendor Module</span>
                                            </div>
                                            <small class="text-muted">Version {{ $mod['version'] }} by {{ htmlspecialchars($mod['author'], ENT_QUOTES, 'UTF-8') }}</small>
                                        </td>
                                        <td>
                                            <p class="mb-0 small text-muted" style="max-width: 300px;">{{ htmlspecialchars($mod['description'], ENT_QUOTES, 'UTF-8') }}</p>
                                        </td>
                                        <td>
                                            <?php if ($mod['is_active'] === 1) { ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <?php do_action('admin_module_actions', $mod); ?>
                                                <?php if ($mod['is_active'] === 1) { ?>
                                                    <a href="{{ pathto('admin/module/toggle/' . $mod['name']) }}" class="btn btn-sm btn-outline-secondary me-2">Deactivate</a>
                                                <?php } else { ?>
                                                    <a href="{{ pathto('admin/module/toggle/' . $mod['name']) }}" class="btn btn-sm btn-success me-2">Activate</a>
                                                <?php } ?>
                                                <form action="{{ pathto('admin/module/delete') }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to uninstall and delete this module? This will clean up its database files.');">
                                                    <input type="hidden" name="name" value="{{ $mod['name'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Uninstall Module"><i class="fa-solid fa-trash-can"></i></button>
                                                </form>
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

{{ $this->view('admin/layout/footer') }}