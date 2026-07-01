{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Add Theme</h1>
        </div>
        <div>
            <button class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" id="btn-toggle-upload">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Theme
            </button>
        </div>
    </div>

    <!-- Upload Zip Theme Card (toggled by button) -->
    <div class="card shadow-sm border-0 mb-4 rounded-3" id="upload-theme-container" style="display: none;">
        <div class="card-body p-4 text-center">
            <p class="text-muted mb-4 small fw-medium">
                To install or update a theme from a .zip file, you can conveniently upload it using the provided interface.
            </p>
            <div class="mx-auto" style="max-width: 600px;">
                <form action="{{ pathto('admin/theme/upload') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-center justify-content-center border p-4 rounded-3 bg-light">
                    <div class="col-12 col-md-auto">
                        <label for="theme_zip" class="form-label mb-0 fw-semibold text-secondary me-2 small">Theme zip file</label>
                    </div>
                    <div class="col-12 col-md-6">
                        <input type="file" name="theme_zip" id="theme_zip" class="form-control" accept=".zip" required>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Install Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Section Header (Hi, welcome back!) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="text-primary mb-1 fw-bold">Hi, welcome back!</h4>
            <p class="text-muted mb-0 small fw-medium">Your business dashboard template</p>
        </div>
        <div style="min-width: 280px;">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="search-themes-input" class="form-control border-0 ps-0 py-2 small" placeholder="Search Themes...">
            </div>
        </div>
    </div>

    <!-- Installed Themes Section -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
            <h5 class="mb-0 fw-bold text-gray-800"><i class="fa-solid fa-palette me-2 text-primary"></i>Installed Themes</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="themes-list-table">
                    <thead class="table-light text-uppercase fs-7 fw-bold text-muted">
                        <tr>
                            <th scope="col" class="ps-4" style="width: 120px;">Preview</th>
                            <th scope="col">Theme Name</th>
                            <th scope="col">Author</th>
                            <th scope="col">Version</th>
                            <th scope="col">Scope</th>
                            <th scope="col" class="text-center" style="width: 200px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($themes as $theme) { ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="theme-preview-box rounded border bg-light shadow-sm d-flex align-items-center justify-content-center text-muted" style="width: 90px; height: 55px; transition: all 0.3s ease;">
                                        <i class="fa-solid fa-palette fa-2x text-primary opacity-50"></i>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">{{ htmlspecialchars($theme['name'], ENT_QUOTES, 'UTF-8') }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary small fw-medium">{{ htmlspecialchars($theme['author'], ENT_QUOTES, 'UTF-8') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fs-7 fw-semibold">v{{ $theme['version'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-soft text-secondary px-3 py-1-5 rounded-pill fs-7 fw-bold text-uppercase">{{ $theme['scope'] }}</span>
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="{{ pathto('admin/theme/customize/' . $theme['name']) }}" class="btn btn-sm btn-outline-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Customize Theme">
                                            <i class="fa-solid fa-sliders"></i>
                                        </a>
                                        
                                        <?php if ($theme['is_active'] === 1) { ?>
                                            <button class="btn btn-sm btn-success border-0 px-4 py-1-5 rounded-pill shadow-sm fw-bold fs-7" disabled>
                                                <i class="fa-solid fa-circle-check me-1"></i>Activated
                                            </button>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-outline-secondary px-4 py-1-5 rounded-pill btn-inactive-status fw-bold fs-7" onclick="activate_theme('{{ $theme['name'] }}', '{{ $theme['scope'] }}')">
                                                <span class="status-text text-muted"><i class="fa-solid fa-circle-xmark me-1 text-muted"></i>Inactive</span>
                                                <span class="hover-text text-white"><i class="fa-solid fa-power-off me-1"></i>Activate</span>
                                            </button>
                                            
                                            <?php if ($theme['name'] !== 'classic' && $theme['name'] !== 'admin') { ?>
                                                <form action="{{ pathto('admin/theme/delete') }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this theme?');">
                                                    <input type="hidden" name="name" value="{{ $theme['name'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete Theme"><i class="fa-solid fa-trash-can"></i></button>
                                                </form>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for softer badges */
    .bg-secondary-soft {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    .fs-7 {
        font-size: 0.8rem !important;
    }
    
    .py-1-5 {
        padding-top: 0.35rem !important;
        padding-bottom: 0.35rem !important;
    }

    /* Inactive button hover styling */
    .btn-inactive-status {
        position: relative;
        overflow: hidden;
        min-width: 110px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-inactive-status .hover-text {
        display: none;
    }
    
    .btn-inactive-status:hover {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.4);
    }
    
    .btn-inactive-status:hover .status-text {
        display: none;
    }
    
    .btn-inactive-status:hover .hover-text {
        display: inline-block;
    }

    /* Preview box zoom effect on row hover */
    tr:hover .theme-preview-box {
        transform: scale(1.05);
        border-color: #0d6efd !important;
    }
</style>

<script>
    // Theme activation function
    function activate_theme(name, scope) {
        if (typeof flash === 'function') {
            flash('Activating theme: ' + name + '...', 'info');
        }
        window.location.href = '{{ pathto("admin/theme/activate") }}/' + encodeURIComponent(name) + '/' + encodeURIComponent(scope);
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Toggle ZIP upload area
        $('#btn-toggle-upload').on('click', function() {
            $('#upload-theme-container').slideToggle(300);
        });

        // Live search filter
        $('#search-themes-input').on('input', function() {
            var val = $(this).val().toLowerCase();
            $('#themes-list-table tbody tr').filter(function() {
                var name = $(this).find('td:nth-child(2)').text().toLowerCase();
                var author = $(this).find('td:nth-child(3)').text().toLowerCase();
                $(this).toggle(name.indexOf(val) > -1 || author.indexOf(val) > -1);
            });
        });
    });
</script>

{{ $this->view('admin/layout/footer') }}
