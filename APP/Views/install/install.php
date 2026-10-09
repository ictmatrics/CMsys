<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMsys Setup Wizard &amp; Deployment</title>
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --danger-gradient: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            --card-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 30px 15px;
            color: #1e293b;
        }
        .wizard-container {
            width: 100%;
            max-width: 660px;
        }
        .card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            background: #ffffff;
        }
        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 32px 36px 24px;
            text-align: center;
        }
        .card-body {
            padding: 36px;
        }
        .logo-text {
            font-size: 30px;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        /* Multi-Step Wizard Progress Stepper */
        .stepper-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            margin-bottom: 35px;
            padding: 0 10px;
        }
        .stepper-line {
            position: absolute;
            top: 20px;
            left: 50px;
            right: 50px;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }
        .stepper-line-progress {
            height: 100%;
            width: 0%;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
        }
        .step-node {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: transparent;
            text-decoration: none;
        }
        .step-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.25s ease;
        }
        .step-node.active .step-circle {
            border-color: #6366f1;
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.2);
        }
        .step-node.completed .step-circle {
            border-color: #10b981;
            background: #10b981;
            color: #ffffff;
        }
        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            margin-top: 8px;
            white-space: nowrap;
            transition: color 0.25s ease;
        }
        .step-node.active .step-label {
            color: #4f46e5;
            font-weight: 700;
        }
        .step-node.completed .step-label {
            color: #10b981;
        }
        /* Form Controls */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }
        .btn-gradient-primary {
            background: var(--primary-gradient);
            border: none;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .btn-gradient-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: #ffffff;
            box-shadow: 0 8px 20px -6px rgba(79, 70, 229, 0.45);
        }
        .btn-gradient-danger {
            background: var(--danger-gradient);
            border: none;
            color: #ffffff;
            padding: 14px 26px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .btn-gradient-danger:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: #ffffff;
            box-shadow: 0 8px 20px -6px rgba(220, 38, 38, 0.45);
        }
        .btn-light-nav {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .btn-light-nav:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .step-card {
            display: none;
        }
        .step-card.active {
            display: block;
            animation: fadeIn 0.25s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Review Grid */
        .review-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 16px;
        }
        .review-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 13px;
        }
        .review-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .review-key {
            color: #64748b;
            font-weight: 600;
        }
        .review-val {
            color: #0f172a;
            font-weight: 700;
            word-break: break-all;
            text-align: right;
            max-width: 65%;
        }
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="wizard-container">
        <div class="card">
            <div class="card-header">
                <span class="logo-text"><i class="fa-solid fa-shapes me-2"></i>CMsys</span>
                <p class="text-muted mt-2 mb-0 small fw-medium">Content Management System Deployment Wizard</p>
            </div>
            <div class="card-body">
                <?php flash('error_msg'); ?>
                <?php flash('success_msg'); ?>

                <?php if (!empty($error)) { ?>
                    <!-- Database / System Failure Alert -->
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3">
                        <div class="d-flex gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-danger mt-1 fs-5"></i>
                            <div>
                                <strong class="d-block mb-1">Database Connectivity Error</strong>
                                <small class="text-muted">{{ htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') }}</small>
                                <div class="mt-2 pt-2 border-top border-danger-subtle small text-muted">
                                    Please check your database service and parameters before retrying.
                                </div>
                                <div class="mt-3">
                                    <?php redirectto('install', 'Retry Setup', 'btn btn-outline-danger btn-sm'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } elseif (!empty($is_deployed)) { ?>
                    <!-- Deployment Completed: Interactive Action Control to Purge Setup Resources -->
                    <div class="text-center mb-4">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success" style="width: 72px; height: 72px;">
                            <i class="fa-solid fa-circle-check fa-2x"></i>
                        </div>
                        <span class="security-badge bg-success-subtle text-success border border-success-subtle mb-2">
                            <i class="fa-solid fa-check-circle"></i> Setup Finalized
                        </span>
                        <h4 class="fw-bold text-dark mt-2 mb-1">System is Operational</h4>
                        <p class="text-muted small mb-0">The database architecture and administrator credentials are fully active.</p>
                    </div>

                    <div class="alert alert-success border-0 bg-success-subtle rounded-3 p-3 mb-4">
                        <div class="d-flex gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1 fs-5"></i>
                            <div class="small">
                                <strong class="text-dark d-block mb-1">Setup Successfully Completed</strong>
                                <span class="text-secondary">Your site is live and all public and administrative interfaces are fully accessible.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation and Action Controls -->
                    <div class="d-grid gap-2 mb-3">
                        <a href="{{ pathto('login') }}" class="btn btn-gradient-primary shadow-sm py-2">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Proceed to Login
                        </a>
                        <a href="{{ pathto('') }}" class="btn btn-outline-secondary shadow-sm py-2">
                            <i class="fa-solid fa-globe me-2"></i>Visit Website Frontend
                        </a>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-link text-danger text-decoration-none btn-sm" id="btnOpenPurgeModal">
                            <i class="fa-solid fa-trash-can me-1"></i>Clean Up Installation Assets
                        </button>
                    </div>

                    <!-- Interactive Confirmation Modal -->
                    <div class="modal fade" id="purgeConfirmModal" tabindex="-1" aria-labelledby="purgeModalTitle" aria-hidden="true" style="display: none; background: rgba(15, 23, 42, 0.75);">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4 p-2">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger" id="purgeModalTitle">
                                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Confirm Resource Purge
                                    </h5>
                                    <button type="button" class="btn-close" id="btnClosePurgeModal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <p class="text-dark mb-2 fw-semibold">Are you ready to purge all installation assets?</p>
                                    <p class="text-muted small mb-3">
                                        Executing this action will permanently delete:
                                    </p>
                                    <ul class="text-muted small mb-3 ps-3">
                                        <li>Setup view directory: <code>APP/Views/install/</code></li>
                                        <li>Setup controller: <code>APP/Controllers/InstallController.php</code></li>
                                        <li>Database installation schema files</li>
                                    </ul>
                                    <div class="alert alert-danger-subtle border-0 rounded-3 p-2 small text-danger">
                                        <i class="fa-solid fa-lock-open me-1"></i> Once purged, full access to frontend pages and admin control panel will unlock immediately.
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light-nav btn-sm px-3" id="btnCancelPurgeModal">Cancel</button>
                                    <form action="{{ pathto('install/purge') }}" method="POST" id="purgeForm" class="d-inline m-0">
                                        <?php if (function_exists('csrf_field')) { ?>
                                            {{ csrf_field() }}
                                        <?php } ?>
                                        <button type="submit" class="btn btn-gradient-danger btn-sm px-4" id="btnExecutePurge">
                                            <i class="fa-solid fa-trash-can me-1"></i> Confirm &amp; Delete Setup Assets
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        const openPurgeBtn = document.getElementById('btnOpenPurgeModal');
                        const closePurgeBtn = document.getElementById('btnClosePurgeModal');
                        const cancelPurgeBtn = document.getElementById('btnCancelPurgeModal');
                        const purgeModal = document.getElementById('purgeConfirmModal');
                        const purgeForm = document.getElementById('purgeForm');
                        const executePurgeBtn = document.getElementById('btnExecutePurge');

                        if (openPurgeBtn && purgeModal) {
                            openPurgeBtn.addEventListener('click', () => {
                                purgeModal.style.display = 'block';
                                purgeModal.classList.add('show');
                            });
                        }
                        const closeModal = () => {
                            if (purgeModal) {
                                purgeModal.style.display = 'none';
                                purgeModal.classList.remove('show');
                            }
                        };
                        if (closePurgeBtn) closePurgeBtn.addEventListener('click', closeModal);
                        if (cancelPurgeBtn) cancelPurgeBtn.addEventListener('click', closeModal);

                        if (purgeForm && executePurgeBtn) {
                            purgeForm.addEventListener('submit', () => {
                                executePurgeBtn.disabled = true;
                                executePurgeBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Purging Assets...';
                            });
                        }
                    </script>

                <?php } else { ?>
                    <!-- Multi-Step Form Wizard -->
                    <!-- Stepper Indicator -->
                    <div class="stepper-nav">
                        <div class="stepper-line">
                            <div class="stepper-line-progress" id="stepperProgress"></div>
                        </div>
                        <div class="step-node active" id="node-1">
                            <div class="step-circle" id="circle-1"><i class="fa-solid fa-database"></i></div>
                            <span class="step-label">Database</span>
                        </div>
                        <div class="step-node" id="node-2">
                            <div class="step-circle" id="circle-2"><i class="fa-solid fa-user-shield"></i></div>
                            <span class="step-label">Admin Profile</span>
                        </div>
                        <div class="step-node" id="node-3">
                            <div class="step-circle" id="circle-3"><i class="fa-solid fa-circle-check"></i></div>
                            <span class="step-label">Confirmation</span>
                        </div>
                    </div>

                    <form action="{{ pathto('install') }}" method="POST" id="wizardForm" novalidate>
                        <?php if (function_exists('csrf_field')) { ?>
                            {{ csrf_field() }}
                        <?php } ?>

                        <!-- STEP 1: Database & System Environment Configuration -->
                        <div class="step-card active" id="step-1">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary-subtle text-primary p-2 me-2 d-inline-flex">
                                    <i class="fa-solid fa-server"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Database &amp; Environment</h6>
                                    <small class="text-muted">Specify storage connectivity and domain parameters</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="db_connection" class="form-label">Database Engine</label>
                                <select name="db_connection" id="db_connection" class="form-select">
                                    <option value="mysql">MySQL / MariaDB</option>
                                    <option value="sqlite">SQLite (File-based)</option>
                                </select>
                            </div>

                            <div id="mysql_fields">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="db_host" class="form-label">Database Host</label>
                                        <input type="text" name="db_host" id="db_host" class="form-control" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="db_name" class="form-label">Database Name</label>
                                        <input type="text" name="db_name" id="db_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="db_user" class="form-label">Database User</label>
                                        <input type="text" name="db_user" id="db_user" class="form-control" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="db_pass" class="form-label">Database Password</label>
                                        <input type="password" name="db_pass" id="db_pass" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="db_prefix" class="form-label">Table Prefix</label>
                                <input type="text" name="db_prefix" id="db_prefix" class="form-control" required>
                                <small class="text-muted d-block mt-1">Specify prefix for database tables to support multi-instance architecture.</small>
                            </div>

                            <div class="mb-4">
                                <label for="app_url" class="form-label">Application Base URL</label>
                                <input type="text" name="app_url" id="app_url" class="form-control" required>
                                <small class="text-muted d-block mt-1">Specify base URL for deployment across root domain or subdirectories.</small>
                            </div>

                            <div class="d-flex justify-content-end pt-2 border-top">
                                <button type="button" class="btn btn-gradient-primary" id="btnStep1Next">
                                    Next: Administrator Setup <i class="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2: Administrator Profile Configuration -->
                        <div class="step-card" id="step-2">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary-subtle text-primary p-2 me-2 d-inline-flex">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Administrator Account Setup</h6>
                                    <small class="text-muted">Create primary super-administrator credentials</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="admin_user" class="form-label">Admin Username</label>
                                <input type="text" name="admin_user" id="admin_user" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Admin Email Address</label>
                                <input type="email" name="admin_email" id="admin_email" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label for="admin_pass" class="form-label">Admin Password</label>
                                <input type="password" name="admin_pass" id="admin_pass" class="form-control" required>
                            </div>

                            <div class="d-flex justify-content-between pt-2 border-top">
                                <button type="button" class="btn btn-light-nav" id="btnStep2Prev">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Back
                                </button>
                                <button type="button" class="btn btn-gradient-primary" id="btnStep2Next">
                                    Next: Review &amp; Deploy <i class="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: Review & Launch Deployment -->
                        <div class="step-card" id="step-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary-subtle text-primary p-2 me-2 d-inline-flex">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Review &amp; Confirm Deployment</h6>
                                    <small class="text-muted">Verify entered configuration before system provisioning</small>
                                </div>
                            </div>

                            <!-- Summary Card 1: Database -->
                            <div class="review-card">
                                <div class="d-flex align-items-center mb-2 text-primary fw-bold small">
                                    <i class="fa-solid fa-database me-2"></i> Database &amp; System Configuration
                                </div>
                                <div class="review-item">
                                    <span class="review-key">Engine:</span>
                                    <span class="review-val" id="rev_engine">MySQL / MariaDB</span>
                                </div>
                                <div class="review-item" id="rev_mysql_host_row">
                                    <span class="review-key">Host:</span>
                                    <span class="review-val" id="rev_host">-</span>
                                </div>
                                <div class="review-item" id="rev_mysql_db_row">
                                    <span class="review-key">Database:</span>
                                    <span class="review-val" id="rev_name">-</span>
                                </div>
                                <div class="review-item" id="rev_mysql_user_row">
                                    <span class="review-key">User:</span>
                                    <span class="review-val" id="rev_user">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-key">Table Prefix:</span>
                                    <span class="review-val" id="rev_prefix">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-key">Base URL:</span>
                                    <span class="review-val" id="rev_url">-</span>
                                </div>
                            </div>

                            <!-- Summary Card 2: Administrator Profile -->
                            <div class="review-card">
                                <div class="d-flex align-items-center mb-2 text-primary fw-bold small">
                                    <i class="fa-solid fa-user-gear me-2"></i> Administrator Credentials
                                </div>
                                <div class="review-item">
                                    <span class="review-key">Username:</span>
                                    <span class="review-val" id="rev_admin_user">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-key">Email:</span>
                                    <span class="review-val" id="rev_admin_email">-</span>
                                </div>
                                <div class="review-item">
                                    <span class="review-key">Password:</span>
                                    <span class="review-val text-muted">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 bg-info-subtle rounded-3 p-3 mb-4">
                                <div class="d-flex gap-2">
                                    <i class="fa-solid fa-info-circle text-info mt-1"></i>
                                    <div class="small">
                                        <strong class="text-dark d-block mb-1">Deployment Confirmation</strong>
                                        <span class="text-secondary">Submitting will initialize system tables, seed settings, and register your administrator profile.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between pt-2 border-top">
                                <button type="button" class="btn btn-light-nav" id="btnStep3Prev">
                                    <i class="fa-solid fa-arrow-left me-2"></i> Back
                                </button>
                                <button type="submit" class="btn btn-gradient-primary" id="btnDeploySubmit">
                                    <i class="fa-solid fa-bolt me-2"></i> Deploy &amp; Install CMsys
                                </button>
                            </div>
                        </div>
                    </form>

                    <script>
                        (function() {
                            let currentStep = 1;
                            const totalSteps = 3;

                            const stepCards = {
                                1: document.getElementById('step-1'),
                                2: document.getElementById('step-2'),
                                3: document.getElementById('step-3')
                            };

                            const stepNodes = {
                                1: document.getElementById('node-1'),
                                2: document.getElementById('node-2'),
                                3: document.getElementById('node-3')
                            };

                            const stepCircles = {
                                1: document.getElementById('circle-1'),
                                2: document.getElementById('circle-2'),
                                3: document.getElementById('circle-3')
                            };

                            const stepperProgress = document.getElementById('stepperProgress');

                            const dbSelect = document.getElementById('db_connection');
                            const mysqlFields = document.getElementById('mysql_fields');
                            const dbHost = document.getElementById('db_host');
                            const dbName = document.getElementById('db_name');
                            const dbUser = document.getElementById('db_user');
                            const dbPass = document.getElementById('db_pass');
                            const dbPrefix = document.getElementById('db_prefix');
                            const appUrl = document.getElementById('app_url');

                            const adminUser = document.getElementById('admin_user');
                            const adminEmail = document.getElementById('admin_email');
                            const adminPass = document.getElementById('admin_pass');

                            const revEngine = document.getElementById('rev_engine');
                            const revHost = document.getElementById('rev_host');
                            const revName = document.getElementById('rev_name');
                            const revUser = document.getElementById('rev_user');
                            const revPrefix = document.getElementById('rev_prefix');
                            const revUrl = document.getElementById('rev_url');
                            const revAdminUser = document.getElementById('rev_admin_user');
                            const revAdminEmail = document.getElementById('rev_admin_email');

                            const revHostRow = document.getElementById('rev_mysql_host_row');
                            const revDbRow = document.getElementById('rev_mysql_db_row');
                            const revUserRow = document.getElementById('rev_mysql_user_row');

                            const updateDbEngineUI = () => {
                                const isSqlite = dbSelect.value === 'sqlite';
                                if (mysqlFields) {
                                    mysqlFields.style.display = isSqlite ? 'none' : 'block';
                                }
                                if (dbHost) dbHost.required = !isSqlite;
                                if (dbName) dbName.required = !isSqlite;
                                if (dbUser) dbUser.required = !isSqlite;
                            };

                            if (dbSelect) {
                                dbSelect.addEventListener('change', updateDbEngineUI);
                                updateDbEngineUI();
                            }

                            const updateStepper = (step) => {
                                for (let s = 1; s <= totalSteps; s++) {
                                    const node = stepNodes[s];
                                    const card = stepCards[s];
                                    const circle = stepCircles[s];

                                    if (s === step) {
                                        card.classList.add('active');
                                        node.classList.add('active');
                                        node.classList.remove('completed');
                                        circle.innerHTML = s === 1 ? '<i class="fa-solid fa-database"></i>' : (s === 2 ? '<i class="fa-solid fa-user-shield"></i>' : '<i class="fa-solid fa-circle-check"></i>');
                                    } else if (s < step) {
                                        card.classList.remove('active');
                                        node.classList.remove('active');
                                        node.classList.add('completed');
                                        circle.innerHTML = '<i class="fa-solid fa-check"></i>';
                                    } else {
                                        card.classList.remove('active');
                                        node.classList.remove('active');
                                        node.classList.remove('completed');
                                        circle.innerHTML = s === 1 ? '<i class="fa-solid fa-database"></i>' : (s === 2 ? '<i class="fa-solid fa-user-shield"></i>' : '<i class="fa-solid fa-circle-check"></i>');
                                    }
                                }

                                if (stepperProgress) {
                                    const percent = ((step - 1) / (totalSteps - 1)) * 100;
                                    stepperProgress.style.width = percent + '%';
                                }
                            };

                            const validateStep1 = () => {
                                const isSqlite = dbSelect.value === 'sqlite';
                                if (!isSqlite) {
                                    if (!dbHost.checkValidity()) { dbHost.reportValidity(); return false; }
                                    if (!dbName.checkValidity()) { dbName.reportValidity(); return false; }
                                    if (!dbUser.checkValidity()) { dbUser.reportValidity(); return false; }
                                }
                                if (!dbPrefix.checkValidity()) { dbPrefix.reportValidity(); return false; }
                                if (!appUrl.checkValidity()) { appUrl.reportValidity(); return false; }
                                return true;
                            };

                            const validateStep2 = () => {
                                if (!adminUser.checkValidity()) { adminUser.reportValidity(); return false; }
                                if (!adminEmail.checkValidity()) { adminEmail.reportValidity(); return false; }
                                if (!adminPass.checkValidity()) { adminPass.reportValidity(); return false; }
                                return true;
                            };

                            const populateReview = () => {
                                const isSqlite = dbSelect.value === 'sqlite';
                                revEngine.textContent = isSqlite ? 'SQLite (File-based)' : 'MySQL / MariaDB';

                                if (isSqlite) {
                                    if (revHostRow) revHostRow.style.display = 'none';
                                    if (revDbRow) revDbRow.style.display = 'none';
                                    if (revUserRow) revUserRow.style.display = 'none';
                                } else {
                                    if (revHostRow) { revHostRow.style.display = 'flex'; revHost.textContent = dbHost.value || '-'; }
                                    if (revDbRow) { revDbRow.style.display = 'flex'; revName.textContent = dbName.value || '-'; }
                                    if (revUserRow) { revUserRow.style.display = 'flex'; revUser.textContent = dbUser.value || '-'; }
                                }

                                revPrefix.textContent = dbPrefix.value || '-';
                                revUrl.textContent = appUrl.value || '-';
                                revAdminUser.textContent = adminUser.value || '-';
                                revAdminEmail.textContent = adminEmail.value || '-';
                            };

                            const btnStep1Next = document.getElementById('btnStep1Next');
                            const btnStep2Prev = document.getElementById('btnStep2Prev');
                            const btnStep2Next = document.getElementById('btnStep2Next');
                            const btnStep3Prev = document.getElementById('btnStep3Prev');
                            const wizardForm = document.getElementById('wizardForm');
                            const btnDeploySubmit = document.getElementById('btnDeploySubmit');

                            if (btnStep1Next) {
                                btnStep1Next.addEventListener('click', () => {
                                    if (validateStep1()) {
                                        currentStep = 2;
                                        updateStepper(currentStep);
                                    }
                                });
                            }

                            if (btnStep2Prev) {
                                btnStep2Prev.addEventListener('click', () => {
                                    currentStep = 1;
                                    updateStepper(currentStep);
                                });
                            }

                            if (btnStep2Next) {
                                btnStep2Next.addEventListener('click', () => {
                                    if (validateStep2()) {
                                        populateReview();
                                        currentStep = 3;
                                        updateStepper(currentStep);
                                    }
                                });
                            }

                            if (btnStep3Prev) {
                                btnStep3Prev.addEventListener('click', () => {
                                    currentStep = 2;
                                    updateStepper(currentStep);
                                });
                            }

                            if (wizardForm && btnDeploySubmit) {
                                wizardForm.addEventListener('submit', (e) => {
                                    if (!validateStep1() || !validateStep2()) {
                                        e.preventDefault();
                                        return false;
                                    }
                                    btnDeploySubmit.disabled = true;
                                    btnDeploySubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Deploying CMsys...';
                                });
                            }

                            updateStepper(1);
                        })();
                    </script>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
