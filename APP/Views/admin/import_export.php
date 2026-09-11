{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <!-- Export Panel -->
    <div class="col-md-6 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-file-export me-2"></i> Database Export / Backup</h5>
            </div>
            <div class="card-body card-custom-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-muted">Download a complete backup of all database tables (posts, pages, options, comments, users, media logs) as a standard SQL file.</p>
                    <div class="alert alert-info small shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Use this database dump to restore the system status or migrate to another server.
                    </div>
                </div>
                <form action="{{ pathto('admin/export') }}" method="POST" class="m-0">
                    <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold"><i class="fa-solid fa-download me-2"></i> Export Database Backup</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Panel -->
    <div class="col-md-6 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-file-import me-2"></i> Database Import / Restore</h5>
            </div>
            <div class="card-body card-custom-body d-flex flex-column justify-content-between">
                <div>
                    <p class="text-muted">Select a CMsys database dump (.sql) file from your local computer to restore database tables.</p>
                    <div class="alert alert-warning small shadow-sm">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>WARNING:</strong> Importing SQL files will overwrite current tables and may log you out!
                    </div>
                </div>
                <form action="{{ pathto('admin/import') }}" method="POST" enctype="multipart/form-data" class="m-0" onsubmit="return confirm('Importing SQL will replace all database contents. Are you sure you want to continue?');">
                    <div class="mb-3">
                        <input type="file" name="import_file" class="form-control" accept=".sql" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-3 font-weight-bold"><i class="fa-solid fa-upload me-2"></i> Import & Restore Database</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{ $this->view('admin/layout/footer') }}
