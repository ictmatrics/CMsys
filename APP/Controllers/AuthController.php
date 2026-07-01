<?php
declare(strict_types=1);

namespace App\Controllers;

use System\Config\Controller;
use App\Models\UserModel;

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (isset($_SESSION['ICTM_Auth'])) {
            redirect('admin/dashboard');
            exit();
        }

        echo $this->view('login');
    }

    public function doLogin()
    {
        $username = validate_data($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            flash('error_msg', 'Both username and password are required!', 'alert alert-danger');
            redirect('login');
            exit();
        }

        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['ICTM_Auth'] = [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            ];
            
            // Redirect based on role
            if ($user->role === 'subscriber') {
                redirect('');
            } else {
                redirect('admin/dashboard');
            }
            exit();
        } else {
            flash('error_msg', 'Invalid username or password!', 'alert alert-danger');
            redirect('login');
            exit();
        }
    }

    public function logout()
    {
        unset($_SESSION['ICTM_Auth']);

        session_destroy();
        
        redirect('login');
        exit();
    }
}
