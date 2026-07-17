<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ButterTteokSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Insert kategori (skip jika sudah ada)
        $exists = $db->table('categories')->where('name', 'Butter Tteok')->get()->getRow();
        if (!$exists) {
            $db->table('categories')->insert([
                'name'       => 'Butter Tteok',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Kategori 'Butter Tteok' ditambahkan.\n";
        } else {
            echo "Kategori 'Butter Tteok' sudah ada, skip.\n";
        }

        // 2. Insert produk
        $products = [
            [
                'name'        => 'Original Butterteok',
                'price'       => 15200,
                'category'    => 'Butter Tteok',
                'description' => 'Butter Tteok original dengan tekstur lembut dan rasa butter yang kaya. Dibuat dari tepung beras pilihan, dipanggang sempurna setiap hari.',
                'image'       => '',
                'stock'         => 50,
                'sold'          => 0,
                'rating'        => 0,
                'rating_count'  => 0,
                'is_best_seller'=> 0,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Chocolate Butterteok',
                'price'       => 16500,
                'category'    => 'Butter Tteok',
                'description' => 'Butter Tteok dengan isian cokelat premium yang meleleh di mulut. Perpaduan sempurna antara kelembutan tteok dan rasa cokelat yang intens.',
                'image'       => '',
                'stock'         => 50,
                'sold'          => 0,
                'rating'        => 0,
                'rating_count'  => 0,
                'is_best_seller'=> 0,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Matcha Butterteok',
                'price'       => 20700,
                'category'    => 'Butter Tteok',
                'description' => 'Butter Tteok dengan matcha premium grade A dari Jepang. Perpaduan unik antara earthy matcha dan lembutnya butter menciptakan cita rasa yang tak terlupakan.',
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

        echo "\nSelesai! Kategori dan produk Butter Tteok berhasil ditambahkan.\n";
    }
}
