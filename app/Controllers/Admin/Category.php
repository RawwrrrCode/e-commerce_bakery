<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Category extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $categories = $db->table('categories')->orderBy('name', 'ASC')->get()->getResultArray();

        // Enrich with product count
        foreach ($categories as &$cat) {
            $cat['product_count'] = $db->table('products')
                ->where('category', $cat['name'])
                ->countAllResults();
        }
        unset($cat);

        return view('admin/categories/index', [
            'categories' => $categories,
        ]);
    }

    public function store()
    {
        $db   = \Config\Database::connect();
        $name = trim($this->request->getPost('name') ?? '');

        if ($name === '') {
            return redirect()->to('/admin/categories')->with('error', 'Nama kategori tidak boleh kosong.');
        }

        $exists = $db->table('categories')->where('name', $name)->countAllResults();
        if ($exists > 0) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori "' . $name . '" sudah ada.');
        }

        $db->table('categories')->insert(['name' => $name, 'created_at' => date('Y-m-d H:i:s')]);
        return redirect()->to('/admin/categories')->with('success', 'Kategori "' . $name . '" berhasil ditambahkan.');
    }

    public function delete($id)
    {
        $db  = \Config\Database::connect();
        $cat = $db->table('categories')->where('id', $id)->get()->getRow();

        if (!$cat) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan.');
        }

        $used = $db->table('products')->where('category', $cat->name)->countAllResults();
        if ($used > 0) {
            return redirect()->to('/admin/categories')
                ->with('error', 'Kategori "' . $cat->name . '" tidak dapat dihapus karena masih digunakan oleh ' . $used . ' produk.');
        }

        $db->table('categories')->where('id', $id)->delete();
        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil dihapus.');
    }
}
