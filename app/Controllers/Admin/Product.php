<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Product extends BaseController
{
    public function index()
    {
        $model = new ProductModel();
        return view('admin/products/index', [
            'products' => $model->findAll()
        ]);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $categories = $db->table('categories')->orderBy('name', 'ASC')->get()->getResultArray();
        return view('admin/products/create', ['categories' => $categories]);
    }

    public function store()
    {
        $model = new ProductModel();

        $file = $this->request->getFile('image');
        $filename = $file->getRandomName();
        $file->move('uploads/', $filename);

        $model->save([
            'name'        => $this->request->getPost('name'),
            'price'       => $this->request->getPost('price'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'image'       => $filename,
        ]);

        return redirect()->to('/admin/products');
    }

    public function edit($id)
    {
        $model = new \App\Models\ProductModel();
        $db    = \Config\Database::connect();
        $categories = $db->table('categories')->orderBy('name', 'ASC')->get()->getResultArray();

        return view('admin/products/edit', [
            'product'    => $model->find($id),
            'categories' => $categories,
        ]);
    }

        public function update($id)
    {
        $model = new \App\Models\ProductModel();

        $data = [
            'name'        => $this->request->getPost('name'),
            'price'       => $this->request->getPost('price'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
        ];

        // cek kalau upload gambar baru
        $file = $this->request->getFile('image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads', $newName);

            $data['image'] = $newName;
        }

        $model->update($id, $data);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil diupdate');
    }

        public function delete($id)
    {
        $model = new \App\Models\ProductModel();

        // ambil data dulu (buat hapus gambar)
        $product = $model->find($id);

        if ($product && file_exists('uploads/' . $product['image'])) {
            unlink('uploads/' . $product['image']);
        }

        $model->delete($id);

        return redirect()->to('/admin/products')
                        ->with('success', 'Produk berhasil dihapus');
    }

 public function rate()
{
    $db = \Config\Database::connect();

    $product_id = $this->request->getPost('product_id');
    $rating = $this->request->getPost('rating');

    // ambil data sekarang
    $product = $db->table('products')
        ->where('id', $product_id)
        ->get()
        ->getRow();

    if (!$product) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Produk tidak ditemukan'
        ]);
    }

    // hitung rata-rata baru
    $total_rating = ($product->rating * $product->rating_count) + $rating;
    $new_count = $product->rating_count + 1;
    $new_rating = $total_rating / $new_count;

    // update ke DB
    $db->table('products')
        ->where('id', $product_id)
        ->update([
            'rating' => $new_rating,
            'rating_count' => $new_count
        ]);

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Rating berhasil ⭐'
    ]);
}


}