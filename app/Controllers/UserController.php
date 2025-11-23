<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new \App\Models\UserModel();
    }

    public function index()
    {
        if (!has_role(['admin', 'super-admin'])) {
            return redirect()->to('/');
        }

        $data['user'] = $this->userModel->findAll();

        return view("pages/user/index", $data);
    }

    /**
     * Menampilkan detail user
     *
     * @param int $id
     * @return string|ResponseInterface
     */
    public function detail(int $id)
    {
        $data['user'] = $this->userModel->find($id);

        return view('pages/user/detail', $data);
    }

    /**
     * Membuat user baru
     *
     * @return string|ResponseInterface
     */
    public function create()
    {
        if (!has_role(['admin', 'super-admin'])) {
            return redirect()->to('/');
        }

        if ($this->request->getMethod() === 'POST') {
            $input = [
                'nama' => $this->request->getPost('nama'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'role' => $this->request->getPost('role')
            ];

            if ($this->userModel->insert($input)) {
                return $this->response->setJSON(['success' => true, 'message' => 'User created successfully', 'url' => '/user']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => $this->userModel->errors()]);
            }
        }
        return view('pages/user/create');
    }

    /**
     * Edit data user
     *
     * @param int $id User ID
     * @return string|ResponseInterface
     */
    public function edit(int $id)
    {
        $userData['user'] = $this->userModel->find($id);
        if (!$userData['user']) {
            // User tidak ditemukan
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'User tidak ditemukan']);
            }
            return redirect()->to('/user');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [];

            $data['nama'] = $this->request->getPost('nama');
            $data['role'] = $this->request->getPost('role');

            // Input Phone - validate uniqueness if changed
            $inputphone = $this->request->getPost('telepon');
            if ($userData['user']['telepon'] !== $inputphone) {
                // Phone changed, check if already exists
                $existing = $this->userModel->where('telepon', $inputphone)
                    ->where('id_user !=', $id)
                    ->first();
                
                if ($existing) {
                    return $this->response->setJSON(['success' => false, 'message' => ['telepon' => 'Nomor telepon sudah terdaftar']]);
                }
                
                $data['telepon'] = $inputphone;
            }

            // Input Email - validate uniqueness if changed
            $inputemail = $this->request->getPost('email');
            if ($userData['user']['email'] !== $inputemail) {
                // Email changed, check if already exists
                $existing = $this->userModel->where('email', $inputemail)
                    ->where('id_user !=', $id)
                    ->first();
                
                if ($existing) {
                    return $this->response->setJSON(['success' => false, 'message' => ['email' => 'Email sudah terdaftar']]);
                }
                
                $data['email'] = $inputemail;
            }

            // Input Password
            $inputPassword = $this->request->getPost('password');
            if (!empty($inputPassword)) {
                $data['password'] = $inputPassword;
            }

            // Update the user
            if ($this->userModel->update($id, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully', 'url' => '/user']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => $this->userModel->errors()]);
            }
        }

        return view("pages/user/edit", $userData);
    }

    /**
     * Hapus user
     *
     * @param int $id User ID
     * @return ResponseInterface
     */
    public function delete(int $id)
    {
        if (!has_role(['admin', 'super-admin'])) {
            return redirect()->to('/');
        }

        if ($this->userModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => $this->userModel->errors()]);
        }
    }

    public function login(){
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        // Batasi frekuensi login untuk mencegah brute force
        $maxAttempts = 5;
        $lockoutTime = 300; // 5 menit dalam detik
        $ipAddress = $this->request->getIPAddress();
        $attempts = session()->get("login_attempts_{$ipAddress}") ?? 0;
        $lastAttempt = session()->get("login_last_attempt_{$ipAddress}") ?? 0;

        if ($attempts >= $maxAttempts && time() - $lastAttempt < $lockoutTime) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.'
            ]);
        }

        if ($this->request->getMethod() === 'POST') {
            // Validasi input
            $email = filter_var($this->request->getPost('email'), FILTER_VALIDATE_EMAIL);
            $password = $this->request->getPost('password');

            // Sanitasi input untuk mencegah XSS
            if (!$email || empty($password)) {
                session()->set("login_attempts_{$ipAddress}", $attempts + 1);
                session()->set("login_last_attempt_{$ipAddress}", time());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email atau password tidak valid.'
                ]);
            }

            // Cari user berdasarkan email
            $user = $this->userModel->where('email', $email)->first();

            if ($user && password_verify($password, $user['password'])) {
                // Reset percobaan login jika berhasil
                session()->remove("login_attempts_{$ipAddress}");
                session()->remove("login_last_attempt_{$ipAddress}");

                session()->set(['user' => $user, 'isLoggedIn' => true]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Selamat datang, ' . esc($user['nama']),
                    'url' => '/'
                ]);
            } else {
                // Tambahkan jumlah percobaan
                session()->set("login_attempts_{$ipAddress}", $attempts + 1);
                session()->set("login_last_attempt_{$ipAddress}", time());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email atau password salah!'
                ]);
            }
        }

        return view('pages/auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function forgotPassword()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        if ($this->request->getMethod() === 'POST') {
            $email = filter_var($this->request->getPost('email'), FILTER_VALIDATE_EMAIL);

            if (!$email) {
                return redirect()->back()->with('error', 'Email tidak valid.');
            }

            // Cek apakah email terdaftar
            $user = $this->userModel->where('email', $email)->first();

            if ($user) {
                // Generate token reset password
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Simpan token ke database (untuk implementasi lengkap, buat tabel password_resets)
                // Sementara simpan di session sebagai demo
                session()->setTempdata('reset_token_' . $token, $user['id_user'], 3600);
                session()->setTempdata('reset_email_' . $token, $email, 3600);

                // Dalam implementasi production, kirim email dengan link reset
                // Untuk demo, tampilkan pesan sukses
                $resetLink = site_url("/reset-password/{$token}");
                
                return redirect()->back()->with('success', 
                    "Link reset password telah dibuat. Untuk demo: <a href='{$resetLink}'>Klik di sini untuk reset password</a>");
            } else {
                // Jangan beritahu apakah email terdaftar atau tidak (security best practice)
                return redirect()->back()->with('success', 
                    'Jika email terdaftar, link reset password akan dikirim ke email Anda.');
            }
        }

        return view('pages/auth/forgot_password');
    }

    public function resetPassword($token = null)
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        if (!$token) {
            return redirect()->to('/login')->with('error', 'Token tidak valid.');
        }

        // Cek token validity
        $userId = session()->getTempdata('reset_token_' . $token);
        $email = session()->getTempdata('reset_email_' . $token);

        if (!$userId || !$email) {
            return redirect()->to('/login')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        if ($this->request->getMethod() === 'POST') {
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password');

            if ($password !== $confirmPassword) {
                return redirect()->back()->with('error', 'Password tidak cocok.');
            }

            if (strlen($password) < 6) {
                return redirect()->back()->with('error', 'Password minimal 6 karakter.');
            }

            // Update password
            $this->userModel->update($userId, [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Hapus token
            session()->removeTempdata('reset_token_' . $token);
            session()->removeTempdata('reset_email_' . $token);

            return redirect()->to('/login')->with('success', 'Password berhasil diubah. Silakan login.');
        }

        return view('pages/auth/reset_password', ['token' => $token, 'email' => $email]);
    }
}