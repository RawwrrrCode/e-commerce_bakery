<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\HybridRecommender;
use App\Models\ProductModel;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // ── 1. Pastikan ada produk ────────────────────────────────────
        $products = $db->table('products')->get()->getResultArray();
        if (empty($products)) {
            echo "ERROR: Tidak ada produk di database. Tambahkan produk terlebih dahulu.\n";
            return;
        }

        $productIds = array_column($products, 'id');
        $productMap = array_column($products, null, 'id');
        $total      = count($products);
        echo "Ditemukan {$total} produk.\n";

        // ── 2. Buat 20 dummy users ────────────────────────────────────
        $dummyUsers = [
            ['Budi Santoso',    'budi.santoso@dummymail.com'],
            ['Siti Rahayu',     'siti.rahayu@dummymail.com'],
            ['Ahmad Fauzi',     'ahmad.fauzi@dummymail.com'],
            ['Dewi Lestari',    'dewi.lestari@dummymail.com'],
            ['Riko Pratama',    'riko.pratama@dummymail.com'],
            ['Ayu Maharani',    'ayu.maharani@dummymail.com'],
            ['Doni Kurniawan',  'doni.kurniawan@dummymail.com'],
            ['Rina Wulandari',  'rina.wulandari@dummymail.com'],
            ['Hendra Setiawan', 'hendra.s@dummymail.com'],
            ['Maya Putri',      'maya.putri@dummymail.com'],
            ['Fajar Nugroho',   'fajar.nugroho@dummymail.com'],
            ['Laila Sari',      'laila.sari@dummymail.com'],
            ['Agus Hermawan',   'agus.h@dummymail.com'],
            ['Yuni Astuti',     'yuni.a@dummymail.com'],
            ['Rizal Maulana',   'rizal.m@dummymail.com'],
            ['Nadia Permata',   'nadia.p@dummymail.com'],
            ['Eko Saputro',     'eko.s@dummymail.com'],
            ['Fitri Handayani', 'fitri.h@dummymail.com'],
            ['Bayu Anggara',    'bayu.a@dummymail.com'],
            ['Citra Dewi',      'citra.d@dummymail.com'],
        ];

        $pass    = password_hash('password123', PASSWORD_DEFAULT);
        $userIds = [];

        echo "\nMembuat dummy users...\n";
        foreach ($dummyUsers as [$name, $email]) {
            $row = $db->table('users')->where('email', $email)->get()->getRow();
            if ($row) {
                $userIds[] = (int)$row->id;
                echo "  Skip (sudah ada): {$email}\n";
                continue;
            }
            $db->table('users')->insert([
                'name'     => $name,
                'email'    => $email,
                'password' => $pass,
                'role'     => 'user',
            ]);
            $uid       = $db->insertID();
            $userIds[] = $uid;
            echo "  Dibuat: {$name} (ID: {$uid})\n";
        }

        // ── 3. Orders, order_detail, ratings ─────────────────────────
        // "Populer" = 40% produk pertama — sering dibeli banyak user
        // Ini memastikan cukup banyak overlap antar user untuk CF bekerja
        $popCount = max(2, (int)ceil($total * 0.4));
        $popIds   = array_slice($productIds, 0, $popCount);

        echo "\nMembuat orders, order_detail, dan ratings...\n";

        foreach ($userIds as $uid) {
            if ($db->table('orders')->where('user_id', $uid)->countAllResults() > 0) {
                echo "  Skip (sudah ada orders): user {$uid}\n";
                continue;
            }

            $numOrders = rand(2, 4);
            $boughtIds = [];

            for ($o = 0; $o < $numOrders; $o++) {
                // Pilih produk populer (wajib 1-2) + produk acak (opsional)
                $shuffledPop = $popIds;
                shuffle($shuffledPop);
                $selected = array_slice($shuffledPop, 0, rand(1, min(2, count($shuffledPop))));

                $others = array_values(array_diff($productIds, $selected));
                if (!empty($others) && rand(0, 1)) {
                    shuffle($others);
                    $selected = array_merge($selected, array_slice($others, 0, rand(1, min(2, count($others)))));
                }

                $selected   = array_unique($selected);
                $orderTotal = 0;
                $items      = [];

                foreach ($selected as $pid) {
                    $qty   = rand(1, 3);
                    $price = (int)($productMap[$pid]['price'] ?? 15000);
                    $items[$pid]  = ['qty' => $qty, 'price' => $price];
                    $orderTotal  += $qty * $price;
                }

                $daysAgo = rand(1, 90);
                $db->table('orders')->insert([
                    'user_id'        => $uid,
                    'total'          => $orderTotal,
                    'status'         => 'selesai',
                    'payment_status' => 'paid',
                    'created_at'     => date('Y-m-d H:i:s', strtotime("-{$daysAgo} days")),
                ]);
                $orderId = $db->insertID();

                foreach ($items as $pid => $item) {
                    $db->table('order_detail')->insert([
                        'order_id'   => $orderId,
                        'product_id' => $pid,
                        'qty'        => $item['qty'],
                        'price'      => $item['price'],
                    ]);
                    $boughtIds[] = $pid;

                    // Update sold count
                    $db->table('products')
                        ->set('sold', 'sold + ' . $item['qty'], false)
                        ->where('id', $pid)->update();
                }
            }

            // Ratings (70% peluang per produk yang dibeli)
            $uniqueBought = array_unique($boughtIds);
            $ratedCount   = 0;

            foreach ($uniqueBought as $pid) {
                if (rand(1, 10) > 3) {
                    $star = rand(3, 5);

                    $db->table('ratings')->insert([
                        'user_id'    => $uid,
                        'product_id' => $pid,
                        'rating'     => $star,
                    ]);
                    $ratedCount++;

                    // Update rata-rata rating produk
                    $prod = $db->table('products')->where('id', $pid)->get()->getRow();
                    if ($prod) {
                        $nc = $prod->rating_count + 1;
                        $nr = (($prod->rating * $prod->rating_count) + $star) / $nc;
                        $db->table('products')->where('id', $pid)->update([
                            'rating'       => round($nr, 2),
                            'rating_count' => $nc,
                        ]);
                    }
                }
            }

            echo "  User {$uid}: {$numOrders} pesanan | "
               . count($uniqueBought) . " produk dibeli | {$ratedCount} rating\n";
        }

        // ── 4. Jalankan rekomendasi → isi recommendation_log ─────────
        echo "\nMenjalankan HybridRecommender untuk setiap user...\n";

        try {
            $allProducts = (new ProductModel())->findAll();
            $recommender = new HybridRecommender();

            foreach ($userIds as $uid) {
                $recommender->recommend($uid, $allProducts);
                echo "  User {$uid}: rekomendasi OK\n";
            }

            // Simulasi klik ~40% dari log yang ada
            $logs    = $db->table('recommendation_logs')->get()->getResultArray();
            $clicked = 0;
            foreach ($logs as $log) {
                if (rand(1, 10) <= 4) {
                    $db->table('recommendation_logs')
                        ->where('id', $log['id'])
                        ->update(['is_clicked' => 1]);
                    $clicked++;
                }
            }
            echo "  Simulasi klik: {$clicked} dari " . count($logs) . " log rekomendasi\n";

        } catch (\Exception $e) {
            echo "  Warning saat rekomendasi: " . $e->getMessage() . "\n";
        }

        // ── Ringkasan ─────────────────────────────────────────────────
        $totalUsers   = $db->table('users')->where('role', 'user')->countAllResults();
        $totalOrders  = $db->table('orders')->where('payment_status', 'paid')->countAllResults();
        $totalRatings = $db->table('ratings')->countAllResults();

        echo "\n✅ Selesai!\n";
        echo "   Total user   : {$totalUsers}\n";
        echo "   Paid orders  : {$totalOrders}\n";
        echo "   Ratings      : {$totalRatings}\n";
        echo "\nLogin dummy user:\n";
        echo "   Email   : budi.santoso@dummymail.com\n";
        echo "   Password: password123\n";
        echo "\nBuka /admin/recommendation untuk melihat metrik.\n";
    }
}
