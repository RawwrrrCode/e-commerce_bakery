<?php

namespace App\Libraries;

use Config\Database;

/**
 * User-Based Collaborative Filtering
 *
 * Merekomendasikan produk berdasarkan kesamaan pola
 * pembelian/rating antara user target dengan user lain.
 *
 * Rating matrix dibangun dari:
 *   - Tabel `ratings`       → explicit feedback (1–5 bintang)
 *   - Tabel `order_detail`  → implicit feedback (nilai default 3.5)
 *
 * Kesamaan antar user diukur dengan Pearson Correlation.
 * Prediksi rating menggunakan formula Adjusted Cosine / Pearson CF.
 */
class CollaborativeFilter
{
    protected $db;

    const IMPLICIT_SCORE = 3.5; // nilai default untuk produk yang dibeli tapi belum dirating
    const K_NEIGHBORS    = 10;  // jumlah tetangga terdekat

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // ----------------------------------------------------------------
    // Bangun User-Item Matrix
    // ----------------------------------------------------------------

    /**
     * @return array [user_id => [product_id => rating]]
     */
    public function buildRatingMatrix(): array
    {
        $matrix = [];

        // Explicit ratings
        $ratings = $this->db->table('ratings')
            ->select('user_id, product_id, rating')
            ->get()->getResultArray();

        foreach ($ratings as $r) {
            $matrix[$r['user_id']][$r['product_id']] = (float)$r['rating'];
        }

        // Implicit feedback dari pembelian
        $purchases = $this->db->table('order_detail od')
            ->select('o.user_id, od.product_id')
            ->join('orders o', 'o.id = od.order_id')
            ->get()->getResultArray();

        foreach ($purchases as $p) {
            if (!isset($matrix[$p['user_id']][$p['product_id']])) {
                $matrix[$p['user_id']][$p['product_id']] = self::IMPLICIT_SCORE;
            }
        }

        return $matrix;
    }

    // ----------------------------------------------------------------
    // Pearson Correlation antara dua user
    // ----------------------------------------------------------------

    /**
     * @param  array $ratingsA  [product_id => rating] milik user A
     * @param  array $ratingsB  [product_id => rating] milik user B
     * @return float  korelasi Pearson (-1 s/d 1)
     */
    public function pearsonCorrelation(array $ratingsA, array $ratingsB): float
    {
        // Hanya hitung pada item yang keduanya sudah pernah beri nilai
        $common = array_intersect_key($ratingsA, $ratingsB);
        $n      = count($common);

        if ($n < 2) return 0.0;

        $keys  = array_keys($common);
        $meanA = array_sum(array_intersect_key($ratingsA, $common)) / $n;
        $meanB = array_sum(array_intersect_key($ratingsB, $common)) / $n;

        $num = $denA = $denB = 0.0;

        foreach ($keys as $k) {
            $dA  = $ratingsA[$k] - $meanA;
            $dB  = $ratingsB[$k] - $meanB;
            $num  += $dA * $dB;
            $denA += $dA * $dA;
            $denB += $dB * $dB;
        }

        $den = sqrt($denA * $denB);
        return $den == 0.0 ? 0.0 : $num / $den;
    }

    // ----------------------------------------------------------------
    // Prediksi rating satu item untuk satu user
    // ----------------------------------------------------------------

    /**
     * Dipakai untuk evaluasi RMSE.
     */
    public function predictRating(int $userId, int $productId, array $matrix): ?float
    {
        if (!isset($matrix[$userId])) return null;

        $userRatings = $matrix[$userId];
        $userMean    = count($userRatings) ? array_sum($userRatings) / count($userRatings) : 3.0;

        $sims = [];
        foreach ($matrix as $uid => $uRatings) {
            if ($uid == $userId)                   continue;
            if (!isset($uRatings[$productId]))     continue;
            $sim = $this->pearsonCorrelation($userRatings, $uRatings);
            if ($sim > 0) $sims[$uid] = $sim;
        }

        if (empty($sims)) return null;

        arsort($sims);
        $topK = array_slice($sims, 0, self::K_NEIGHBORS, true);

        $num = $den = 0.0;
        foreach ($topK as $uid => $sim) {
            $uRatings  = $matrix[$uid];
            $uMean     = count($uRatings) ? array_sum($uRatings) / count($uRatings) : 3.0;
            $num += $sim * ($uRatings[$productId] - $uMean);
            $den += abs($sim);
        }

        if ($den == 0.0) return null;

        return max(1.0, min(5.0, $userMean + $num / $den));
    }

    // ----------------------------------------------------------------
    // Hitung skor CF untuk semua produk yang belum dilihat user
    // ----------------------------------------------------------------

    /**
     * @param  int   $userId
     * @param  array $allProducts
     * @return array [product_id => cf_score]  (0.0 – 1.0)
     */
    public function getScores(int $userId, array $allProducts): array
    {
        $matrix = $this->buildRatingMatrix();

        if (empty($matrix[$userId])) return [];

        $userRatings = $matrix[$userId];
        $userMean    = array_sum($userRatings) / count($userRatings);

        // Cari K tetangga terdekat
        $sims = [];
        foreach ($matrix as $uid => $uRatings) {
            if ($uid == $userId) continue;
            $sim = $this->pearsonCorrelation($userRatings, $uRatings);
            if ($sim > 0) $sims[$uid] = $sim;
        }

        arsort($sims);
        $neighbors = array_slice($sims, 0, self::K_NEIGHBORS, true);

        if (empty($neighbors)) return [];

        // Produk yang belum pernah diinteraksi user
        $seenIds = array_keys($userRatings);
        $scores  = [];

        foreach ($allProducts as $p) {
            $pid = (int)$p['id'];
            if (in_array($pid, $seenIds)) continue;

            $num = $den = 0.0;
            foreach ($neighbors as $uid => $sim) {
                if (!isset($matrix[$uid][$pid])) continue;
                $uMean = array_sum($matrix[$uid]) / count($matrix[$uid]);
                $num  += $sim * ($matrix[$uid][$pid] - $uMean);
                $den  += abs($sim);
            }

            if ($den == 0.0) continue;

            $predicted = $userMean + $num / $den;
            // Normalisasi ke 0-1
            $scores[$pid] = max(0.0, min(1.0, ($predicted - 1.0) / 4.0));
        }

        return $scores;
    }

    // ----------------------------------------------------------------
    // Evaluasi: RMSE (Root Mean Square Error)
    // ----------------------------------------------------------------

    /**
     * Hitung RMSE antara prediksi CF vs rating aktual.
     * Gunakan 20% data rating sebagai test set (holdout).
     *
     * @return array ['rmse' => float, 'n_test' => int, 'mae' => float]
     */
    public function evaluateRMSE(): array
    {
        $matrix = $this->buildRatingMatrix();

        $allRatings = $this->db->table('ratings')
            ->select('user_id, product_id, rating')
            ->get()->getResultArray();

        if (count($allRatings) < 5) {
            return ['rmse' => 0.0, 'mae' => 0.0, 'n_test' => 0];
        }

        // Ambil 20% sebagai test set (holdout)
        shuffle($allRatings);
        $testSize = max(1, (int)floor(count($allRatings) * 0.2));
        $testSet  = array_slice($allRatings, 0, $testSize);

        $sumSE = $sumAE = 0.0;
        $count = 0;

        foreach ($testSet as $t) {
            $uid = (int)$t['user_id'];
            $pid = (int)$t['product_id'];
            $actual = (float)$t['rating'];

            // Sembunyikan item ini dari matrix sementara
            $tempMatrix = $matrix;
            unset($tempMatrix[$uid][$pid]);

            $predicted = $this->predictRating($uid, $pid, $tempMatrix);
            if ($predicted === null) continue;

            $sumSE += pow($predicted - $actual, 2);
            $sumAE += abs($predicted - $actual);
            $count++;
        }

        if ($count == 0) return ['rmse' => 0.0, 'mae' => 0.0, 'n_test' => 0];

        return [
            'rmse'   => round(sqrt($sumSE / $count), 4),
            'mae'    => round($sumAE / $count, 4),
            'n_test' => $count,
        ];
    }
}
