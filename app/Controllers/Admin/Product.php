<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductImagesModel;

class Product extends BaseController
{
    public function index()
    {
        $model    = new ProductModel();
        $products = $model->orderBy('category', 'ASC')->orderBy('name', 'ASC')->findAll();

        $grouped = [];
        foreach ($products as $p) {
            $grouped[$p['category'] ?? 'Lainnya'][] = $p;
        }

        return view('admin/products/index', ['grouped' => $grouped]);
    }

    public function create()
    {
        $db         = \Config\Database::connect();
        $categories = $db->table('categories')->orderBy('name', 'ASC')->get()->getResultArray();
        return view('admin/products/create', ['categories' => $categories]);
    }

    public function store()
    {
        $model = new ProductModel();

        $filename = '';
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filename = $file->getRandomName();
            $file->move('uploads/', $filename);
        }

        $model->save([
            'name'        => $this->request->getPost('name'),
            'price'       => $this->request->getPost('price'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'image'       => $filename,
        ]);

        $productId = $model->getInsertID();
        $this->saveGalleryImages($productId);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $model      = new ProductModel();
        $imgModel   = new ProductImagesModel();
        $db         = \Config\Database::connect();
        $categories = $db->table('categories')->orderBy('name', 'ASC')->get()->getResultArray();

        return view('admin/products/edit', [
            'product'    => $model->find($id),
            'categories' => $categories,
            'gallery'    => $imgModel->where('product_id', $id)->orderBy('sort_order', 'ASC')->findAll(),
        ]);
    }

    public function update($id)
    {
        $model = new ProductModel();

        $data = [
            'name'        => $this->request->getPost('name'),
            'price'       => $this->request->getPost('price'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
        ];

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName    = $file->getRandomName();
            $file->move('uploads', $newName);
            $data['image'] = $newName;
        }

        $model->update($id, $data);
        $this->saveGalleryImages($id);

        return redirect()->to('/admin/products/edit/' . $id)->with('success', 'Produk berhasil diupdate');
    }

    public function delete($id)
    {
        $model    = new ProductModel();
        $imgModel = new ProductImagesModel();

        $product = $model->find($id);
        if ($product && !empty($product['image']) && file_exists('uploads/' . $product['image'])) {
            unlink('uploads/' . $product['image']);
        }

        // delete gallery images
        $gallery = $imgModel->where('product_id', $id)->findAll();
        foreach ($gallery as $g) {
            if (!empty($g['image']) && file_exists('uploads/' . $g['image'])) {
                unlink('uploads/' . $g['image']);
            }
        }
        $imgModel->where('product_id', $id)->delete();

        $model->delete($id);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil dihapus');
    }

    // Delete a single gallery image (AJAX POST)
    public function deleteImage($imageId)
    {
        $imgModel = new ProductImagesModel();
        $img      = $imgModel->find($imageId);

        if (!$img) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gambar tidak ditemukan']);
        }

        if (!empty($img['image']) && file_exists('uploads/' . $img['image'])) {
            unlink('uploads/' . $img['image']);
        }
        $imgModel->delete($imageId);

        return $this->response->setJSON(['status' => 'ok']);
    }

    // ── helper: save multiple gallery files ───────────────────────
    private function saveGalleryImages(int $productId): void
    {
        $imgModel = new ProductImagesModel();
        $files    = $this->request->getFileMultiple('gallery');

        if (!$files) return;

        $order = (int) ($imgModel->where('product_id', $productId)->countAllResults());

        foreach ($files as $file) {
            if (!$file->isValid() || $file->hasMoved()) continue;
            $name = $file->getRandomName();
            $file->move('uploads/', $name);
            $imgModel->save([
                'product_id' => $productId,
                'image'      => $name,
                'sort_order' => $order++,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function rate()
    {
        $db         = \Config\Database::connect();
        $product_id = $this->request->getPost('product_id');
        $rating     = $this->request->getPost('rating');

        $product = $db->table('products')->where('id', $product_id)->get()->getRow();
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produk tidak ditemukan']);
        }

        $total_rating = ($product->rating * $product->rating_count) + $rating;
        $new_count    = $product->rating_count + 1;

        $db->table('products')->where('id', $product_id)->update([
            'rating'       => $total_rating / $new_count,
            'rating_count' => $new_count,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Rating berhasil ⭐']);
    }
}
