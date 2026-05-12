<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlists';
    protected $allowedFields = ['user_id', 'product_id'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cek apakah produk ada di wishlist user
     */
    public function isInWishlist(int $userId, int $productId): bool
    {
        return $this->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first() !== null;
    }

    /**
     * Ambil semua wishlist items user dengan detail produk
     */
    public function getUserWishlist(int $userId): array
    {
        return $this->select('wishlists.*, products.name, products.price, products.image, products.category, products.rating')
            ->join('products', 'products.id = wishlists.product_id')
            ->where('wishlists.user_id', $userId)
            ->orderBy('wishlists.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Hitung jumlah item di wishlist user
     */
    public function getWishlistCount(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    /**
     * Tambah produk ke wishlist
     */
    public function addToWishlist(int $userId, int $productId): bool
    {
        if ($this->isInWishlist($userId, $productId)) {
            return false; // Sudah ada
        }

        return $this->insert([
            'user_id' => $userId,
            'product_id' => $productId,
        ]) !== false;
    }

    /**
     * Hapus produk dari wishlist
     */
    public function removeFromWishlist(int $userId, int $productId): bool
    {
        return $this->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }
}
