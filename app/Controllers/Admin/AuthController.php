<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\UserAdmin;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (!empty($_SESSION['admin_id'])) {
            $this->redirect('admin/dashboard');
        }
        $this->view('admin/auth/login', [
            'error' => $_SESSION['login_error'] ?? null,
        ], 'minimal');
        unset($_SESSION['login_error']);
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $model = new UserAdmin();
        $admin = $model->findByUsername($username);

        if ($admin && $model->verifyPassword($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']      = $admin['id_user'];
            $_SESSION['admin_nama']    = $admin['nama_petugas'];
            $_SESSION['admin_jabatan'] = $admin['jabatan'];
            $_SESSION['admin_username']= $admin['username'];

            Logger::info('Admin login berhasil', [
                'admin_id' => $admin['id_user'],
                'username' => $username,
                'jabatan'  => $admin['jabatan'],
            ]);

            $this->redirect('admin/dashboard');
        } else {
            Logger::warning("Login admin gagal untuk username: $username", [
                'username' => $username,
                'reason'   => $admin ? 'password_salah' : 'username_tidak_ditemukan',
            ]);
            $_SESSION['login_error'] = 'Username atau password salah.';
            $this->redirect('admin/login');
        }
    }

    public function logout(): void
    {
        Logger::info('Admin logout', ['admin_id' => $_SESSION['admin_id'] ?? null]);
        session_destroy();
        $this->redirect('admin/login');
    }
}
