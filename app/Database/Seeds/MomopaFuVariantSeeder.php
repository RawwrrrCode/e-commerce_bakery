<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MomopaFuVariantSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Delete the separate Box 10pcs and Box 15pcs products for each flavor
        $toDelete = [
            'Vanilla Momopafu - Box 10pcs',
            'Vanilla Momopafu - Box 15pcs',
            'Chocolate Momopafu - Box 10pcs',
            'Chocolate Momopafu - Box 15pcs',
            'Black Sesame Momopafu - Box 10pcs',
            'Black Sesame Momopafu - Box 15pcs',
            'Matcha Momopafu - Box 10pcs',
            'Matcha Momopafu - Box 15pcs',
        ];

        foreach ($toDelete as $name) {
            $row = $db->table('products')->where('name', $name)->get()->getRow();
            if ($row) {
                // also remove from cart/order_detail referencing this product
                $db->table('cart')->where('product_id', $row->id)->delete();
                $db->table('products')->where('id', $row->id)->delete();
                echo "Dihapus: {$name}\n";
            } else {
                echo "Skip (tidak ditemukan): {$name}\n";
            }
        }

        // 2. Rename Bag 5pcs products → plain flavor name + set variants JSON
        $flavors = [
            [
                'old_name' => 'Vanilla Momopafu - Bag 5pcs',
                'new_name' => 'Vanilla Momopafu',
                'price'    => 25000,
                'variants' => [
                    ['label' => 'Bag 5pcs',  'price' => 25000],
                    ['label' => 'Box 10pcs', 'price' => 48000],
                    ['label' => 'Box 15pcs', 'price' => 68000],
                ],
            ],
            [
                'old_name' => 'Chocolate Momopafu - Bag 5pcs',
                'new_name' => 'Chocolate Momopafu',
                'price'    => 25000,
                'variants' => [
                    ['label' => 'Bag 5pcs',  'price' => 25000],
                    ['label' => 'Box 10pcs', 'price' => 48000],
                    ['label' => 'Box 15pcs', 'price' => 68000],
                ],
            ],
            [
                'old_name' => 'Black Sesame Momopafu - Bag 5pcs',
                'new_name' => 'Black Sesame Momopafu',
                'price'    => 22000,
                'variants' => [
                    ['label' => 'Bag 5pcs',  'price' => 22000],
                    ['label' => 'Box 10pcs', 'price' => 45000],
                    ['label' => 'Box 15pcs', 'price' => 65000],
                ],
            ],
            [
                'old_name' => 'Matcha Momopafu - Bag 5pcs',
                'new_name' => 'Matcha Momopafu',
                'price'    => 28000,
                'variants' => [
                    ['label' => 'Bag 5pcs',  'price' => 28000],
                    ['label' => 'Box 10pcs', 'price' => 50000],
                    ['label' => 'Box 15pcs', 'price' => 70000],
                ],
            ],
        ];

        foreach ($flavors as $f) {
            $row = $db->table('products')->where('name', $f['old_name'])->get()->getRow();
            if (!$row) {
                // try by new name (already renamed in a previous run)
                $row = $db->table('products')->where('name', $f['new_name'])->get()->getRow();
            }
            if ($row) {
                $db->table('products')->where('id', $row->id)->update([
                    'name'     => $f['new_name'],
                    'price'    => $f['price'],
                    'variants' => json_encode($f['variants']),
                ]);
                echo "Updated: {$f['new_name']} dengan " . count($f['variants']) . " varian\n";
            } else {
                echo "Skip (tidak ditemukan): {$f['old_name']}\n";
            }
        }

        echo "\nSelesai! Momopafu sekarang 5 produk (1 Mix Box + 4 rasa dengan varian ukuran).\n";
    }
}
