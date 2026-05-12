<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // ✅ cek login berdasarkan id
        if (!session()->get('id')) {
            return redirect()->to('/login');
        }

        // ✅ cek role
        if ($arguments) {
            if (session()->get('role') != $arguments[0]) {
                return redirect()->to('/');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // kosong
    }
}