<?php

class AuthController extends Controller {

    public function login() {
        Auth::init();

        // Jika user sudah login, langsung arahkan ke dashboard
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $errors = [];

        // Menangani form submit (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            $validator = new Validator();
            
            // Validasi input dasar
            if ($validator->validate($_POST, ['email' => 'required|email', 'password' => 'required'])) {
                
                // Panggil model User
                $userModel = $this->model('User');
                $user = $userModel->findByEmail($email);

                // Verifikasi keberadaan user dan kecocokan hash password
                if ($user && password_verify($password, $user['password_hash'])) {
                    
                    // Set Session Login
                    Auth::login($user);
                    
                    // Update last login timestamp
                    $userModel->updateLastLogin($user['id']);

                    // Redirect ke URL yang dituju sebelumnya (jika ada), atau ke dashboard
                    $redirectUrl = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'dashboard';
                    unset($_SESSION['redirect_url']); // Hapus dari session setelah dipakai
                    
                    $this->redirect($redirectUrl);
                } else {
                    $errors['auth'] = 'Email atau password salah, atau akun Anda tidak aktif.';
                }
            } else {
                $errors = $validator->getErrors();
            }
        }

        // Jika GET atau validasi gagal, tampilkan halaman form login
        // Render menggunakan view 'auth/login' dan layout 'auth'
        $this->view('auth/login', ['errors' => $errors], 'auth');
    }

    public function logout() {
        Auth::logout();
        $this->redirect('auth/login');
    }
}