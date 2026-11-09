<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * MomopaFuSeeder — data katalog contoh untuk kategori "Pastry".
 *
 * Nama kategori sengaja memakai salah satu dari 4 kategori yang sudah
 * di-hardcode di ContentBasedFilter::$categories (Pillow Cake, Cheesecake,
 * Cookies, Pastry). Produk generik bakery (roti/kue/pastry) selaras dengan
 * profil PT. Mimosa Tarte Indonesia di proposal, bukan nama brand pihak lain.
 * (Nama kelas dipertahankan agar perintah `php spark db:seed` yang sudah
 * ada tetap jalan; isi datanya yang diubah.)
 */
class MomopaFuSeeder extends Seeder
{
    public function run()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // 1. Insert kategori
        $exists = $db->table('categories')->where('name', 'Pastry')->get()->getRow();
        if (!$exists) {
            $db->table('categories')->insert(['name' => 'Pastry', 'created_at' => $now]);
            echo "Kategori 'Pastry' ditambahkan.\n";
        } else {
            echo "Kategori 'Pastry' sudah ada, skip.\n";
        }

        // 2. Daftar produk
        $products = [
            // ── Mix Box (Best Seller) ───────────────────────────────────
            [
                'name'          => 'Mix Box Cream Puff 15pcs',
                'price'         => 68000,
                'description'   => 'Mix Box berisi 15 pcs Cream Puff: 5 Vanilla + 5 Chocolate + 5 Black Sesame. Pilihan terbaik untuk dicoba semua rasa! Best Seller kami.',
                'is_best_seller'=> 1,
            ],

            // ── Vanilla ─────────────────────────────────────────────────
            [
                'name'          => 'Cream Puff Vanilla - Bag 5pcs',
                'price'         => 25000,
                'description'   => 'Cream puff rasa vanilla klasik dalam kemasan bag 5 pcs. Crispy di luar, lembut dan creamy vanilla di dalam. Dipanggang fresh setiap hari.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Vanilla - Box 10pcs',
                'price'         => 48000,
                'description'   => 'Cream puff rasa vanilla klasik dalam kemasan box 10 pcs. Lebih hemat, cocok untuk oleh-oleh atau camilan keluarga.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Vanilla - Box 15pcs',
                'price'         => 68000,
                'description'   => 'Cream puff rasa vanilla klasik dalam kemasan box 15 pcs. Ukuran paling ekonomis, ideal untuk gathering atau hampers.',
                'is_best_seller'=> 0,
            ],

            // ── Chocolate ───────────────────────────────────────────────
            [
                'name'          => 'Cream Puff Chocolate - Bag 5pcs',
                'price'         => 25000,
                'description'   => 'Cream puff isian cokelat premium dalam kemasan bag 5 pcs. Lelehan cokelat yang kaya rasa berpadu dengan kulit crispy yang tipis.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Chocolate - Box 10pcs',
                'price'         => 48000,
                'description'   => 'Cream puff isian cokelat premium dalam kemasan box 10 pcs. Cocok untuk pecinta cokelat yang ingin menikmati lebih banyak.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Chocolate - Box 15pcs',
                'price'         => 68000,
                'description'   => 'Cream puff isian cokelat premium dalam kemasan box 15 pcs. Pilihan favorit untuk hadiah dan berbagi.',
                'is_best_seller'=> 0,
            ],

            // ── Black Sesame ────────────────────────────────────────────
            [
                'name'          => 'Cream Puff Black Sesame - Bag 5pcs',
                'price'         => 22000,
                'description'   => 'Cream puff isian pasta wijen hitam dalam kemasan bag 5 pcs. Rasa nutty yang unik dengan aroma wijen yang khas.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Black Sesame - Box 10pcs',
                'price'         => 45000,
                'description'   => 'Cream puff isian pasta wijen hitam dalam kemasan box 10 pcs. Varian paling unik yang wajib dicoba.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Black Sesame - Box 15pcs',
                'price'         => 65000,
                'description'   => 'Cream puff isian pasta wijen hitam dalam kemasan box 15 pcs. Pilihan ekonomis untuk penggemar rasa autentik.',
                'is_best_seller'=> 0,
            ],

            // ── Matcha ──────────────────────────────────────────────────
            [
                'name'          => 'Cream Puff Matcha - Bag 5pcs',
                'price'         => 28000,
                'description'   => 'Cream puff isian matcha premium grade A dalam kemasan bag 5 pcs. Perpaduan earthy matcha dan kulit crispy yang sempurna.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Matcha - Box 10pcs',
                'price'         => 50000,
                'description'   => 'Cream puff isian matcha premium dalam kemasan box 10 pcs. Cocok untuk pecinta matcha sejati.',
                'is_best_seller'=> 0,
            ],
            [
                'name'          => 'Cream Puff Matcha - Box 15pcs',
                'price'         => 70000,
                'description'   => 'Cream puff isian matcha premium dalam kemasan box 15 pcs. Ukuran terbesar untuk penggemar matcha yang sesungguhnya.',
                'is_best_seller'=> 0,
            ],
        ];

        foreach ($products as $p) {
            $exists = $db->table('products')->where('name', $p['name'])->get()->getRow();
            if ($exists) {
                echo "Skip (sudah ada): {$p['name']}\n";
                continue;
            }

            $db->table('products')->insert([
                'name'          => $p['name'],
                'price'         => $p['price'],
                'category'      => 'Pastry',
                'description'   => $p['description'],
                'image'         => '',
                'stock'         => 50,
                'sold'          => 0,
                'rating'        => 0,
                'rating_count'  => 0,
                'is_best_seller'=> $p['is_best_seller'],
                'created_at'    => $now,
            ]);

            $label = $p['is_best_seller'] ? ' ⭐ Best Seller' : '';
            echo "Ditambahkan{$label}: {$p['name']} — Rp " . number_format($p['price'], 0, ',', '.') . "\n";
        }

        echo "\nSelesai! Semua produk Cream Puff (Pastry) berhasil ditambahkan.\n";
    }
}
