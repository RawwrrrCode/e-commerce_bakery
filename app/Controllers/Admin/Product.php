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
        $user_id    = session()->get('id');
        $product_id = $this->request->getPost('product_id');
        $rating     = (int) $this->request->getPost('rating');

        if (!$user_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Harus login untuk memberi rating']);
        }

        if ($rating < 1 || $rating > 5) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rating tidak valid']);
        }

        $product = $db->table('products')->where('id', $product_id)->get()->getRow();
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produk tidak ditemukan']);
        }

        // Rating hanya boleh diberikan oleh pembeli produk ini (selaras Tabel 4.8 use case)
        $sudahBeli = $db->table('orders')
            ->join('order_detail', 'order_detail.order_id = orders.id')
            ->where('orders.user_id', $user_id)
            ->where('order_detail.product_id', $product_id)
            ->countAllResults();

        if ($sudahBeli == 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda belum membeli produk ini']);
        }

        $this->upsertRating($db, (int) $user_id, (int) $product_id, $rating);

        return $this->response->setJSON(['success' => true, 'message' => 'Rating berhasil ⭐']);
    }

    /**
     * Simpan/perbarui rating user di tabel `ratings` (sumber data untuk
     * CollaborativeFilter::buildRatingMatrix()), lalu hitung ulang
     * products.rating/rating_count dari tabel `ratings` itu sendiri
     * (bukan increment manual) agar re-rating oleh user yang sama tidak
     * menggandakan rating_count.
     */
    private function upsertRating($db, int $userId, int $productId, int $rating): void
    {
        $existing = $db->table('ratings')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->get()->getRow();

        if ($existing) {
            $db->table('ratings')->where('id', $existing->id)->update(['rating' => $rating]);
        } else {
            $db->table('ratings')->insert([
                'user_id'    => $userId,
                'product_id' => $productId,
                'rating'     => $rating,
            ]);
        }

        $agg = $db->table('ratings')
            ->selectAvg('rating', 'avg_rating')
            ->selectCount('rating', 'cnt')
            ->where('product_id', $productId)
            ->get()->getRow();

        $db->table('products')->where('id', $productId)->update([
            'rating'       => round((float) $agg->avg_rating, 2),
            'rating_count' => (int) $agg->cnt,
        ]);
    }
}
