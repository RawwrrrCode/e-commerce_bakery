<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
   public function register()
{
    return view('register');
}

    public function processRegister()
    {
        $name     = trim($this->request->getPost('name') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (strlen($name) < 2) {
            return redirect()->back()->withInput()->with('error', 'Nama lengkap minimal 2 karakter.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Format email tidak valid.');
        }
        if (strlen($password) < 6) {
            return redirect()->back()->withInput()->with('error', 'Password minimal 6 karakter.');
        }

        $model = new UserModel();
        if ($model->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar. Silakan masuk.');
        }

        $model->save([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'user',
        ]);

        return redirect()->to('/login')->with('success', 'Akun berhasil dibuat! Silakan masuk.');
    }

    public function login()
{
    return view('login');
}

    public function processLogin()
    {
        
        $model = new UserModel();

        $user = $model->where('email', $this->request->getPost('email'))->first();
        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
                session()->set([
    'id' => $user['id'],
    'email' => $user['email'],
    'role' => $user['role'],
    'logged_in' => true
]);

session()->regenerate(); // 🔥 penting


            if ($user['role'] == 'admin') {
                return redirect()->to('/admin/products');
            }

            return redirect()->to('/');
        }

        return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function profile()
    {
        $db     = \Config\Database::connect();
        $userId = session()->get('id');
        $user   = $db->table('users')->where('id', $userId)->get()->getRow();

        $totalOrders = $db->table('orders')->where('user_id', $userId)->countAllResults();
        $totalSpent  = $db->query(
            "SELECT COALESCE(SUM(total),0) AS val FROM orders WHERE user_id=? AND payment_status='paid'",
            [$userId]
        )->getRow()->val;
        $totalWishlist = $db->table('wishlists')->where('user_id', $userId)->countAllResults();

        return view('profile', [
            'user'          => $user,
            'totalOrders'   => $totalOrders,
            'totalSpent'    => $totalSpent,
            'totalWishlist' => $totalWishlist,
        ]);
    }

    public function updateProfile()
    {
        $db     = \Config\Database::connect();
        $userId = session()->get('id');
        $name   = trim($this->request->getPost('name') ?? '');
        $email  = trim($this->request->getPost('email') ?? '');

        if (empty($name) || empty($email)) {
            return redirect()->back()->with('error', 'Nama dan email tidak boleh kosong.');
        }

        $taken = $db->table('users')
            ->where('email', $email)
            ->where('id !=', $userId)
            ->get()->getRow();
        if ($taken) {
            return redirect()->back()->with('error', 'Email sudah digunakan akun lain.');
        }

        $phone   = trim($this->request->getPost('phone') ?? '');
        $address = trim($this->request->getPost('address') ?? '');
        $data    = ['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address];

        $newPass     = $this->request->getPost('new_password') ?? '';
        $confirmPass = $this->request->getPost('confirm_password') ?? '';
        $currentPass = $this->request->getPost('current_password') ?? '';

        if (!empty($newPass)) {
            $user = $db->table('users')->where('id', $userId)->get()->getRow();
            if (!password_verify($currentPass, $user->password)) {
                return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
            }
            if ($newPass !== $confirmPass) {
                return redirect()->back()->with('error', 'Konfirmasi password baru tidak cocok.');
            }
            if (strlen($newPass) < 6) {
                return redirect()->back()->with('error', 'Password baru minimal 6 karakter.');
            }
            $data['password'] = password_hash($newPass, PASSWORD_DEFAULT);
        }

        $db->table('users')->where('id', $userId)->update($data);
        session()->set('email', $email);

        return redirect()->to('/profile')->with('info', 'Profil berhasil diperbarui.');
    }
}