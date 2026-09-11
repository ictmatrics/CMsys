<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMsys Installation & Deployment</title>
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            width: 100%;
            max-width: 520px;
            background: #ffffff;
        }
        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 35px 35px 25px;
            text-align: center;
        }
        .card-body {
            padding: 35px;
        }
        .btn-gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            color: #ffffff;
            padding: 13px;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .btn-gradient-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: #ffffff;
        }
        .btn-gradient-danger {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border: none;
            color: #ffffff;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .btn-gradient-danger:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: #ffffff;
        }
        .logo-text {
            font-size: 30px;
            font-weight: 800;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        .form-control {
            border-radius: 10px;
            padding: 11px 15px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
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
    <div class="card">
        <div class="card-header">
            <span class="logo-text"><i class="fa-solid fa-shapes me-2"></i>CMsys</span>
            <p class="text-muted mt-2 mb-0 small">Content Management System Setup</p>
        </div>
        <div class="card-body">
            <?php flash('error_msg'); ?>
            <?php flash('success_msg'); ?>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3">
                    <div class="d-flex gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-danger mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Database Connection Error</strong>
                            <small class="text-muted">{{ htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') }}</small>
                            <div class="mt-2 pt-2 border-top border-danger-subtle small text-muted">
                                Verify your database credentials in <code>APP/.env</code> and ensure your database server is running.
                            </div>
                            <div class="mt-3">
                                <?php redirectto('install', 'Retry Connection', 'btn btn-outline-danger btn-sm'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } elseif (!empty($is_deployed)) { ?>
                <!-- Deployment Complete: Interactive Purge Action Control -->
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-circle-check fa-2x"></i>
                    </div>
                    <span class="security-badge bg-warning-subtle text-warning border border-warning-subtle mb-2">
                        <i class="fa-solid fa-lock"></i> System Locked
                    </span>
                    <h5 class="fw-bold text-dark mt-2 mb-1">Deployment Successful</h5>
                    <p class="text-muted small mb-0">System tables and administrator account have been initialized.</p>
                </div>

                <div class="alert alert-warning border-0 bg-warning-subtle rounded-3 p-3 mb-4">
                    <div class="d-flex gap-2">
                        <i class="fa-solid fa-shield-halved text-warning mt-1 fs-5"></i>
                        <div class="small">
                            <strong class="text-dark d-block mb-1">Security Isolation Enforced</strong>
                            <span class="text-secondary">Access to both frontend views and the admin control panel is strictly restricted until setup assets (<code>/app/views/install</code>) are purged from disk.</span>
                        </div>
                    </div>
                </div>

                <form action="{{ pathto('install/purge') }}" method="POST" onsubmit="return confirm('Purge setup resources (/app/views/install) now? This action cannot be undone and will immediately unlock system access.');">
                    <?php if (function_exists('csrf_field')) { ?>
                        {{ csrf_field() }}
                    <?php } ?>
                    <button type="submit" class="btn btn-gradient-danger w-100 shadow-sm">
                        <i class="fa-solid fa-trash-can me-2"></i>Purge Setup Resources &amp; Unlock System
                    </button>
                </form>
            <?php } else { ?>
                <!-- Fresh Installation Setup Form -->
                <form action="{{ pathto('install') }}" method="POST">
                    <?php if (function_exists('csrf_field')) { ?>
                        {{ csrf_field() }}
                    <?php } ?>

                    <?php if (!empty($env_missing)) { ?>
                        <div class="alert alert-info border-0 bg-info-subtle rounded-3 p-3 mb-3">
                            <div class="d-flex gap-2">
                                <i class="fa-solid fa-circle-info text-info mt-1"></i>
                                <div class="small">
                                    <strong class="text-dark d-block mb-1">Environment File (APP/.env) Not Found</strong>
                                    <span class="text-secondary">Configure your database credentials below to automatically generate <code>APP/.env</code>.</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="db_connection" class="form-label small fw-bold text-secondary">Database Engine</label>
                            <select name="db_connection" id="db_connection" class="form-control">
                                <option value="mysql" selected>MySQL / MariaDB</option>
                                <option value="sqlite">SQLite</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="db_host" class="form-label small fw-bold text-secondary">Database Host</label>
                                <input type="text" name="db_host" id="db_host" class="form-control" value="localhost" required>
                            </div>
                            <div class="col-6">
                                <label for="db_name" class="form-label small fw-bold text-secondary">Database Name</label>
                                <input type="text" name="db_name" id="db_name" class="form-control" value="cmsys" required>
                            </div>
                            <div class="col-6">
                                <label for="db_user" class="form-label small fw-bold text-secondary">Database User</label>
                                <input type="text" name="db_user" id="db_user" class="form-control" value="root" required>
                            </div>
                            <div class="col-6">
                                <label for="db_pass" class="form-label small fw-bold text-secondary">Database Password</label>
                                <input type="password" name="db_pass" id="db_pass" class="form-control" placeholder="Empty by default">
                            </div>
                        </div>
                        <hr class="my-3 text-muted">
                    <?php } ?>

                    <div class="mb-3">
                        <label for="admin_user" class="form-label small fw-bold text-secondary">Admin Username</label>
                        <input type="text" name="admin_user" id="admin_user" class="form-control" placeholder="admin" required value="admin">
                    </div>
                    <div class="mb-3">
                        <label for="admin_email" class="form-label small fw-bold text-secondary">Admin Email Address</label>
                        <input type="email" name="admin_email" id="admin_email" class="form-control" placeholder="admin@example.com" required value="admin@example.com">
                    </div>
                    <div class="mb-4">
                        <label for="admin_pass" class="form-label small fw-bold text-secondary">Admin Password</label>
                        <input type="password" name="admin_pass" id="admin_pass" class="form-control" required placeholder="Enter password" value="password">
                    </div>
                    <button type="submit" class="btn btn-gradient-primary w-100 shadow-sm">
                        <i class="fa-solid fa-bolt me-2"></i>Deploy &amp; Install CMsys
                    </button>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>
