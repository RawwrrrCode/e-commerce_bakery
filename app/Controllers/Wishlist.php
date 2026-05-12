<?php

namespace App\Controllers;

use App\Models\WishlistModel;
use App\Models\ProductModel;

class Wishlist extends BaseController
{
    protected $wishlistModel;
    protected $productModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
        $this->productModel = new ProductModel();
    }

    /**
     * POST /wishlist/add — Tambah produk ke wishlist (AJAX)
     */
    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $userId = session()->get('id');
        $productId = (int)$this->request->getPost('product_id');

        // Validasi produk ada
        if (!$this->productModel->find($productId)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Produk tidak ditemukan']);
        }

        // Cek apakah sudah ada di wishlist
        if ($this->wishlistModel->isInWishlist($userId, $productId)) {
            return $this->response->setStatusCode(409)->setJSON(['error' => 'Sudah ada di wishlist', 'inWishlist' => true]);
        }

        // Tambah ke wishlist
        if ($this->wishlistModel->addToWishlist($userId, $productId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Produk ditambahkan ke wishlist',
                'inWishlist' => true,
                'count' => $this->wishlistModel->getWishlistCount($userId),
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal menambahkan ke wishlist']);
    }

    /**
     * POST /wishlist/remove — Hapus produk dari wishlist (AJAX)
     */
    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $userId = session()->get('id');
        $productId = (int)$this->request->getPost('product_id');

        if ($this->wishlistModel->removeFromWishlist($userId, $productId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Produk dihapus dari wishlist',
                'inWishlist' => false,
                'count' => $this->wishlistModel->getWishlistCount($userId),
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal menghapus dari wishlist']);
    }

    /**
     * GET /wishlist/check/:id — Cek apakah produk ada di wishlist user (AJAX)
     */
    public function check($productId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $userId = session()->get('id');
        $isInWishlist = $this->wishlistModel->isInWishlist($userId, $productId);

        return $this->response->setJSON([
            'inWishlist' => $isInWishlist,
        ]);
    }

}
