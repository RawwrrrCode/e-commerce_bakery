<?php

namespace App\Libraries;

use Config\Database;

/**
 * Content-Based Filtering
 *
 * Merekomendasikan produk berdasarkan kesamaan fitur produk
 * dengan produk yang pernah dibeli/dirating oleh user.
 *
 * Fitur vektor tiap produk:
 *   [onehot_category × 4] + [price_norm] + [rating_norm]  = 6 dimensi
 *
 * Kesamaan diukur dengan Cosine Similarity.
 */
class ContentBasedFilter
{
    protected $db;

    // Daftar kategori (one-hot encoding)
    protected array $categories = ['Pillow Cake', 'Cheesecake', 'Cookies', 'Pastry'];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // ----------------------------------------------------------------
    // PUBLIC: dipakai juga oleh HybridRecommender
    // ----------------------------------------------------------------

    /**
     * Bangun feature vector 6-dimensi untuk sebuah produk.
     * [cat_1, cat_2, cat_3, cat_4, price_norm, rating_norm]
     */
    public function buildFeatureVector(array $product, float $maxPrice): array
    {
        $vec = [];

        // One-hot category
        foreach ($this->categories as $cat) {
            $vec[] = ($product['category'] === $cat) ? 1.0 : 0.0;
        }

        // Price: invert & normalize (murah = skor lebih tinggi)
        $vec[] = $maxPrice > 0 ? 1.0 - ($product['price'] / $maxPrice) : 0.5;

        // Rating normalized 0-1
        $vec[] = isset($product['rating']) ? (float)$product['rating'] / 5.0 : 0.0;

        return $vec;
    }

    /**
     * Cosine Similarity antara dua vektor numerik.
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        $dot = $normA = $normB = 0.0;

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $dot   += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0.0 || $normB == 0.0) return 0.0;

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    // ----------------------------------------------------------------
    // Hitung skor CBF untuk semua produk yang belum pernah dibeli user
    // ----------------------------------------------------------------

    /**
     * @param  int   $userId
     * @param  array $allProducts  hasil findAll() dari ProductModel
     * @return array [product_id => cbf_score]  (0.0 – 1.0)
     */
    public function getScores(int $userId, array $allProducts): array
    {
        if (empty($allProducts)) return [];

        // --- Kumpulkan produk yang sudah pernah dibeli / dirating ---
        $boughtIds = array_column(
            $this->db->table('order_detail od')
                ->select('od.product_id')
                ->join('orders o', 'o.id = od.order_id')
                ->where('o.user_id', $userId)
                ->get()->getResultArray(),
            'product_id'
        );

        $ratedRows = $this->db->table('ratings')
            ->select('product_id, rating')
            ->where('user_id', $userId)
            ->get()->getResultArray();

        $ratingMap    = array_column($ratedRows, 'rating', 'product_id');
        $ratedIds     = array_column($ratedRows, 'product_id');
        $interactedIds = array_unique(array_merge($boughtIds, $ratedIds));

        if (empty($interactedIds)) return [];

        // --- Bangun semua vektor produk ---
        $maxPrice = max(array_column($allProducts, 'price')) ?: 1;
        $vectors  = [];
        foreach ($allProducts as $p) {
            $vectors[$p['id']] = $this->buildFeatureVector($p, $maxPrice);
        }

        // --- Bangun user profile vector (rata-rata berbobot rating) ---
        $dim     = count($this->categories) + 2;
        $profile = array_fill(0, $dim, 0.0);
        $totalW  = 0.0;

        foreach ($interactedIds as $pid) {
            if (!isset($vectors[$pid])) continue;
            $w = isset($ratingMap[$pid]) ? (float)$ratingMap[$pid] : 3.5;
            foreach ($vectors[$pid] as $i => $v) {
                $profile[$i] += $v * $w;
            }
            $totalW += $w;
        }

        if ($totalW > 0) {
            foreach ($profile as &$v) $v /= $totalW;
        }

        // --- Hitung cosine similarity untuk produk yang belum diinteraksi ---
        $scores = [];
        foreach ($allProducts as $p) {
            if (in_array($p['id'], $interactedIds)) continue;
            $scores[$p['id']] = $this->cosineSimilarity($profile, $vectors[$p['id']]);
        }

        return $scores;
    }

    // ----------------------------------------------------------------
    // Produk Serupa (untuk halaman detail)
    // ----------------------------------------------------------------

    /**
     * @param  int   $productId   ID produk yang sedang dilihat
     * @param  array $allProducts
     * @param  int   $n           jumlah rekomendasi
     * @return array produk dengan field tambahan 'cbf_similarity'
     */
    public function getSimilarProducts(int $productId, array $allProducts, int $n = 4): array
    {
        $target = null;
        foreach ($allProducts as $p) {
            if ((int)$p['id'] === $productId) { $target = $p; break; }
        }
        if (!$target) return [];

        $maxPrice  = max(array_column($allProducts, 'price')) ?: 1;
        $targetVec = $this->buildFeatureVector($target, $maxPrice);

        $sims = [];
        foreach ($allProducts as $p) {
            if ((int)$p['id'] === $productId) continue;
            $vec = $this->buildFeatureVector($p, $maxPrice);
            $sims[$p['id']] = $this->cosineSimilarity($targetVec, $vec);
        }

        arsort($sims);
        $topIds = array_slice(array_keys($sims), 0, $n, true);

        $result = [];
        foreach ($allProducts as $p) {
            if (in_array($p['id'], $topIds)) {
                $p['cbf_similarity'] = round($sims[$p['id']], 4);
                $result[] = $p;
            }
        }

        // Urutkan sesuai similarity
        usort($result, fn($a, $b) => $b['cbf_similarity'] <=> $a['cbf_similarity']);

        return $result;
    }
}
