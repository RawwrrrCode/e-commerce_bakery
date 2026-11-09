<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ButterTteokSeeder — data katalog contoh untuk kategori "Cookies".
 *
 * Nama kategori sengaja memakai salah satu dari 4 kategori yang sudah
 * di-hardcode di ContentBasedFilter::$categories (Pillow Cake, Cheesecake,
 * Cookies, Pastry). Produk generik bakery (roti/kue/pastry) selaras dengan
 * profil PT. Mimosa Tarte Indonesia di proposal, bukan nama brand pihak lain.
 * (Nama kelas dipertahankan agar perintah `php spark db:seed` yang sudah
 * ada tetap jalan; isi datanya yang diubah.)
 */
class ButterTteokSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Insert kategori (skip jika sudah ada)
        $exists = $db->table('categories')->where('name', 'Cookies')->get()->getRow();
        if (!$exists) {
            $db->table('categories')->insert([
                'name'       => 'Cookies',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Kategori 'Cookies' ditambahkan.\n";
        } else {
            echo "Kategori 'Cookies' sudah ada, skip.\n";
        }

        // 2. Insert produk
        $products = [
            [
                'name'        => 'Butter Cookies Original',
                'price'       => 15200,
                'category'    => 'Cookies',
                'description' => 'Butter cookies original dengan tekstur lembut dan rasa butter yang kaya. Dibuat dari tepung pilihan, dipanggang sempurna setiap hari.',
                'image'       => '',
                'stock'         => 50,
                'sold'          => 0,
                'rating'        => 0,
                'rating_count'  => 0,
                'is_best_seller'=> 0,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Butter Cookies Chocolate',
                'price'       => 16500,
                'category'    => 'Cookies',
                'description' => 'Butter cookies dengan isian cokelat premium yang meleleh di mulut. Perpaduan sempurna antara kelembutan cookies dan rasa cokelat yang intens.',
                'image'       => '',
                'stock'         => 50,
                'sold'          => 0,
                'rating'        => 0,
                'rating_count'  => 0,
                'is_best_seller'=> 0,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Butter Cookies Matcha',
                'price'       => 20700,
                'category'    => 'Cookies',
                'description' => 'Butter cookies dengan matcha premium grade A. Perpaduan unik antara earthy matcha dan lembutnya butter menciptakan cita rasa yang tak terlupakan.',
                'image'       => '',
                'stock'         => 50,
                'sold'          => 0,
                'rating'        => 0,
                'rating_count'  => 0,
                'is_best_seller'=> 0,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($products as $product) {
            $exists = $db->table('products')->where('name', $product['name'])->get()->getRow();
            if (!$exists) {
                $db->table('products')->insert($product);
                echo "Produk '{$product['name']}' ditambahkan (Rp " . number_format($product['price'], 0, ',', '.') . ").\n";
            } else {
                echo "Produk '{$product['name']}' sudah ada, skip.\n";
            }
        }

        echo "\nSelesai! Kategori dan produk Cookies berhasil ditambahkan.\n";
    }
}
