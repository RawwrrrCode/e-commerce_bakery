<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Review extends BaseController
{
    public function index()
    {
        $db      = \Config\Database::connect();
        $perPage = 20;
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));
        $search  = trim($this->request->getGet('search') ?? '');

        $builder = $db->table('reviews r')
            ->select('r.*, u.name as user_name, u.email as user_email, p.name as product_name, p.image as product_image', false)
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('products p', 'p.id = r.product_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('u.name', $search)
                ->orLike('p.name', $search)
                ->orLike('r.review', $search)
            ->groupEnd();
        }

        $total   = (clone $builder)->countAllResults();
        $reviews = (clone $builder)->orderBy('r.id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()->getResultArray();

        return view('admin/reviews/index', [
            'reviews'     => $reviews,
            'total'       => $total,
            'perPage'     => $perPage,
            'currentPage' => $page,
            'totalPages'  => max(1, (int)ceil($total / $perPage)),
            'search'      => $search,
        ]);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->table('reviews')->where('id', $id)->delete();
        return redirect()->to('/admin/reviews')->with('success', 'Ulasan berhasil dihapus.');
    }
}
