<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMsys Installation</title>
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f0f0f0;
            padding: 30px;
            text-align: center;
        }
        .card-body {
            padding: 40px;
            background-color: #ffffff;
        }
        .btn-primary {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .logo-text {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(to right, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <span class="logo-text">CMsys Installer</span>
            <p class="text-muted mt-2 mb-0">Set up your Content Management System</p>
        </div>
        <div class="card-body">
            <?php flash('error_msg'); ?>
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger shadow-sm">
                    <strong>Database Connection Error:</strong><br>
                    {{ $error }}<br><br>
                    <small>Please check your database configuration in <code>APP/Config/config.php</code> and ensure the MySQL server is running.</small>
                </div>
            <?php } else { ?>
                <form action="{{ pathto('install') }}" method="POST">
                    <div class="mb-3">
                        <label for="admin_user" class="form-label font-weight-bold">Admin Username</label>
                        <input type="text" name="admin_user" id="admin_user" class="form-control" placeholder="admin" required value="admin">
                    </div>
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Admin Email Address</label>
                        <input type="email" name="admin_email" id="admin_email" class="form-control" placeholder="admin@example.com" required value="admin@example.com">
                    </div>
                    <div class="mb-4">
                        <label for="admin_pass" class="form-label">Admin Password</label>
                        <input type="password" name="admin_pass" id="admin_pass" class="form-control" required placeholder="Enter password" value="password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">Install CMsys</button>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>
