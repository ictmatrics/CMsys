<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMsys Login</title>
    <link href="{{ pathto('css/bootstrap5.3.8.min.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .card-header {
            background: transparent;
            border-bottom: none;
            padding: 40px 40px 20px 40px;
            text-align: center;
        }
        .card-body {
            padding: 20px 40px 40px 40px;
        }
        .logo-text {
            font-size: 36px;
            font-weight: 800;
            color: #1e3c72;
            letter-spacing: -1px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.2);
            border-color: #1e3c72;
        }
        .btn-primary {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-header">
            <span class="logo-text">CMsys Login</span>
            <p class="text-muted mt-2 mb-0">Sign in to manage your content</p>
        </div>
        <div class="card-body">
            <?php flash('success_msg'); ?>
            <?php flash('error_msg'); ?>
            <form action="{{ pathto('login') }}" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label text-muted font-weight-bold">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required autocomplete="username">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label text-muted font-weight-bold">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary w-100 shadow-sm">Sign In</button>
            </form>
            <div class="text-center mt-3">
                <a href="{{ pathto('') }}" class="text-muted small">&larr; Back to homepage</a>
            </div>
        </div>
    </div>
</body>
</html>
