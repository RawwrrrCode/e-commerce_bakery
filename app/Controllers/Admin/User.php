<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class User extends BaseController
{
    public function index()
    {
        $db      = \Config\Database::connect();
        $perPage = 20;
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));
        $search  = trim($this->request->getGet('search') ?? '');

        $builder = $db->table('users')->where('role', 'user');

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
            ->groupEnd();
        }

        $total = (clone $builder)->countAllResults();
        $users = (clone $builder)->orderBy('id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()->getResultArray();

        // Enrich with order count
        foreach ($users as &$u) {
            $u['order_count'] = $db->table('orders')->where('user_id', $u['id'])->countAllResults();
            $u['total_spent'] = $db->table('orders')->selectSum('total','total')
                ->where('user_id', $u['id'])->where('payment_status','paid')
                ->get()->getRow()->total ?? 0;
        }
        unset($u);

        return view('admin/users/index', [
            'users'       => $users,
            'total'       => $total,
            'perPage'     => $perPage,
            'currentPage' => $page,
            'totalPages'  => max(1, (int)ceil($total / $perPage)),
            'search'      => $search,
        ]);
    }
}
