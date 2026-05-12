<?php

namespace App\Libraries;

use Config\Database;

/**
 * Hybrid Recommender System
 *
 * Menggabungkan Content-Based Filtering (CBF) dan
 * Collaborative Filtering (CF) dengan skema Weighted Hybrid:
 *
 *   hybrid_score = (α × CF_score) + (β × CBF_score)
 *   default: α = 0.6, β = 0.4
 *
 * Cold-Start Handling:
 *   - User baru (< MIN_INTERACTIONS) → Popularity-Based Fallback
 *   - User dengan data terbatas (CF kosong) → CBF saja (β = 1.0)
 *   - User dengan data cukup → Hybrid penuh
 */
class HybridRecommender
{
    protected ContentBasedFilter  $cbf;
    protected CollaborativeFilter $cf;
    protected $db;

    // Bobot default (fallback jika config file tidak ada)
    const WEIGHT_CF_DEFAULT  = 0.6;
    const WEIGHT_CBF_DEFAULT = 0.4;

    // Batas minimum interaksi agar CF dipakai
    const MIN_INTERACTIONS = 2;

    // Jumlah rekomendasi yang dikembalikan
    const TOP_N = 8;

    protected float $weightCF;
    protected float $weightCBF;

    public function __construct()
    {
        $this->cbf = new ContentBasedFilter();
        $this->cf  = new CollaborativeFilter();
        $this->db  = Database::connect();

        // Baca bobot dari file config (bisa diubah admin)
        $configFile = WRITEPATH . 'hybrid_config.json';
        if (file_exists($configFile)) {
            $cfg = json_decode(file_get_contents($configFile), true);
            $this->weightCF  = (float)($cfg['weight_cf']  ?? $this->weightCF_DEFAULT);
            $this->weightCBF = (float)($cfg['weight_cbf'] ?? $this->weightCBF_DEFAULT);
        } else {
            $this->weightCF  = $this->weightCF_DEFAULT;
            $this->weightCBF = $this->weightCBF_DEFAULT;
        }
    }

    public function getWeights(): array
    {
        return ['cf' => $this->weightCF, 'cbf' => $this->weightCBF];
    }

    // ----------------------------------------------------------------
    // Rekomendasi utama untuk halaman home (personal per user)
    // ----------------------------------------------------------------

    /**
     * @param  int   $userId
     * @param  array $allProducts  hasil findAll() dari ProductModel
     * @return array produk dengan field tambahan: hybrid_score, cf_score, cbf_score, rec_method
     */
    public function recommend(int $userId, array $allProducts): array
    {
        if (empty($allProducts)) return [];

        // Hitung total interaksi user (beli + rating)
        $bought = $this->db->table('order_detail od')
            ->join('orders o', 'o.id = od.order_id')
            ->where('o.user_id', $userId)
            ->countAllResults();

        $rated = $this->db->table('ratings')
            ->where('user_id', $userId)
            ->countAllResults();

        $totalInteractions = $bought + $rated;

        // ── COLD START: user baru, pakai popularity ──────────────────
        if ($totalInteractions < self::MIN_INTERACTIONS) {
            $result = $this->popularityBased($allProducts);
            foreach ($result as &$p) {
                $p['rec_reason'] = '🔥 Produk paling banyak diminati';
            }
            unset($p);
            $this->logRecommendations($userId, $result, 'popularity');
            return $result;
        }

        // ── Hitung skor dari kedua metode ────────────────────────────
        $cbfScores = $this->cbf->getScores($userId, $allProducts);
        $cfScores  = $this->cf->getScores($userId, $allProducts);

        // Tentukan metode yang digunakan
        $hasCF     = !empty($cfScores);
        $method    = $hasCF ? 'hybrid' : 'cbf';

        // ── Gabungkan skor ───────────────────────────────────────────
        $allIds   = array_unique(array_merge(array_keys($cbfScores), array_keys($cfScores)));
        $combined = [];

        foreach ($allIds as $pid) {
            $cbf = $cbfScores[$pid] ?? 0.0;
            $cf  = $cfScores[$pid]  ?? 0.0;

            $combined[$pid] = $hasCF
                ? ($this->weightCF * $cf) + ($this->weightCBF * $cbf)
                : $cbf;
        }

        arsort($combined);
        $topIds = array_slice(array_keys($combined), 0, self::TOP_N, true);

        // Bangun product map
        $productMap = [];
        foreach ($allProducts as $p) {
            $productMap[$p['id']] = $p;
        }

        $topCategory = $this->getUserTopCategory($userId);

        $result = [];
        foreach ($topIds as $pid) {
            if (!isset($productMap[$pid])) continue;
            $p = $productMap[$pid];
            $p['hybrid_score'] = round($combined[$pid], 4);
            $p['cf_score']     = round($cfScores[$pid]  ?? 0.0, 4);
            $p['cbf_score']    = round($cbfScores[$pid] ?? 0.0, 4);
            $p['rec_method']   = $method;
            $p['rec_reason']   = $this->buildReason($method, $p['cf_score'], $p['cbf_score'], $topCategory);
            $result[] = $p;
        }

        $this->logRecommendations($userId, $result, $method);

        return $result;
    }

    // ----------------------------------------------------------------
    // Produk Serupa (halaman detail) – murni CBF
    // ----------------------------------------------------------------

    /**
     * @param  int   $productId
     * @param  array $allProducts
     * @param  int   $n
     * @return array
     */
    public function getSimilarProducts(int $productId, array $allProducts, int $n = 4): array
    {
        return $this->cbf->getSimilarProducts($productId, $allProducts, $n);
    }

    // ----------------------------------------------------------------
    // Fallback: Popularity-Based (sold × 0.5 + rating × 0.5)
    // ----------------------------------------------------------------

    protected function popularityBased(array $products): array
    {
        usort($products, function ($a, $b) {
            $sA = ($a['sold'] ?? 0) * 0.5 + ($a['rating'] ?? 0) * 0.5;
            $sB = ($b['sold'] ?? 0) * 0.5 + ($b['rating'] ?? 0) * 0.5;
            return $sB <=> $sA;
        });

        $result = [];
        foreach (array_slice($products, 0, self::TOP_N) as $p) {
            $p['hybrid_score'] = 0.0;
            $p['cf_score']     = 0.0;
            $p['cbf_score']    = 0.0;
            $p['rec_method']   = 'popularity';
            $result[] = $p;
        }
        return $result;
    }

    // ----------------------------------------------------------------
    // Log rekomendasi (untuk evaluasi admin & metrics)
    // ----------------------------------------------------------------

    protected function logRecommendations(int $userId, array $products, string $method): void
    {
        $logsModel = new \App\Models\RecommendationLogsModel();

        $position = 1;
        foreach ($products as $rank => $p) {
            $logsModel->logRecommendation(
                userId: $userId,
                productId: $p['id'],
                method: ucfirst($method),
                cfScore: $p['cf_score'] ?? 0.0,
                cbfScore: $p['cbf_score'] ?? 0.0,
                hybridScore: $p['hybrid_score'] ?? 0.0,
                rank: $rank + 1,
                position: $position
            );
            $position++;
        }
    }

    // ----------------------------------------------------------------
    // Evaluasi Metrik (untuk halaman admin)
    // ----------------------------------------------------------------

    /**
     * @return array ringkasan metrik rekomendasi
     */
    public function getEvaluationMetrics(array $allProducts): array
    {
        $totalProducts = count($allProducts);

        // --- RMSE & MAE (dari CF) ---
        $cfEval = $this->cf->evaluateRMSE();

        // --- Coverage: berapa % produk masuk ke recommendation_logs ---
        $distinctRecommended = $this->db->table('recommendation_logs')
            ->select('COUNT(DISTINCT product_id) as cnt', false)
            ->get()->getRow()->cnt ?? 0;

        $coverage = $totalProducts > 0
            ? round(($distinctRecommended / $totalProducts) * 100, 1)
            : 0.0;

        // --- Distribusi metode ---
        $methods = $this->db->table('recommendation_logs')
            ->select('method, COUNT(*) as total', false)
            ->groupBy('method')
            ->get()->getResultArray();

        $methodDist = [];
        foreach ($methods as $m) {
            $methodDist[$m['method']] = (int)$m['total'];
        }

        // --- CTR (Click-Through Rate) ---
        $totalLogged  = $this->db->table('recommendation_logs')->countAllResults();
        $totalClicked = $this->db->table('recommendation_logs')
            ->where('is_clicked', 1)->countAllResults();

        $ctr = $totalLogged > 0
            ? round(($totalClicked / $totalLogged) * 100, 2)
            : 0.0;

        // --- Precision@N (aprox: klik dianggap relevan) ---
        $precision = $totalLogged > 0
            ? round($totalClicked / $totalLogged, 4)
            : 0.0;

        // --- Recall@N & F1 (offline: ground truth = produk yang dibeli) ---
        $evalRows = $this->db->query("
            SELECT
                rl.user_id,
                COUNT(DISTINCT rl.product_id)  AS n_rec,
                COUNT(DISTINCT od.product_id)  AS n_rel,
                COUNT(DISTINCT CASE WHEN od.product_id = rl.product_id THEN rl.product_id END) AS n_hit
            FROM recommendation_logs rl
            LEFT JOIN orders o  ON o.user_id  = rl.user_id AND o.payment_status = 'paid'
            LEFT JOIN order_detail od ON od.order_id = o.id
            GROUP BY rl.user_id
        ")->getResultArray();

        $sumPurchPrec = 0;
        $sumRecall = 0;
        $nEvalUsers   = count($evalRows);
        foreach ($evalRows as $row) {
            $nRec = (int)$row['n_rec'];
            $nRel = (int)$row['n_rel'];
            $nHit = (int)$row['n_hit'];
            if ($nRec > 0) $sumPurchPrec += $nHit / $nRec;
            if ($nRel > 0) $sumRecall    += $nHit / $nRel;
        }
        $precisionBuy = $nEvalUsers > 0 ? round($sumPurchPrec / $nEvalUsers, 4) : 0.0;
        $recallN      = $nEvalUsers > 0 ? round($sumRecall    / $nEvalUsers, 4) : 0.0;
        $f1Score      = ($precisionBuy + $recallN) > 0
            ? round(2 * $precisionBuy * $recallN / ($precisionBuy + $recallN), 4)
            : 0.0;

        // --- Total users dengan rekomendasi ---
        $usersWithRec = $this->db->table('recommendation_logs')
            ->select('COUNT(DISTINCT user_id) as cnt', false)
            ->get()->getRow()->cnt ?? 0;

        // --- Top 5 produk paling direkomendasikan ---
        $topRec = $this->db->table('recommendation_logs rl')
            ->select('rl.product_id, p.name, COUNT(*) as freq', false)
            ->join('products p', 'p.id = rl.product_id')
            ->groupBy('rl.product_id')
            ->orderBy('freq', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return [
            'rmse'              => $cfEval['rmse'],
            'mae'               => $cfEval['mae'],
            'n_test'            => $cfEval['n_test'],
            'coverage'          => $coverage,
            'ctr'               => $ctr,
            'precision'         => $precision,
            'precision_buy'     => $precisionBuy,
            'recall'            => $recallN,
            'f1'                => $f1Score,
            'total_logged'      => $totalLogged,
            'total_clicked'     => $totalClicked,
            'users_with_rec'    => $usersWithRec,
            'method_dist'       => $methodDist,
            'top_recommended'   => $topRec,
            'total_products'    => $totalProducts,
            'distinct_rec'      => $distinctRecommended,
        ];
    }

    // ----------------------------------------------------------------
    // Helper: kategori favorit user (untuk alasan rekomendasi)
    // ----------------------------------------------------------------

    protected function getUserTopCategory(int $userId): ?string
    {
        $row = $this->db->query("
            SELECT p.category, COUNT(*) AS cnt
            FROM order_detail od
            JOIN orders o ON o.id = od.order_id
            JOIN products p ON p.id = od.product_id
            WHERE o.user_id = ?
            GROUP BY p.category
            ORDER BY cnt DESC
            LIMIT 1
        ", [$userId])->getRow();
        return $row ? $row->category : null;
    }

    protected function buildReason(string $method, float $cfScore, float $cbfScore, ?string $topCategory): string
    {
        if ($method === 'popularity') {
            return '🔥 Produk paling banyak diminati';
        }
        if ($method === 'cbf') {
            return $topCategory
                ? "📐 Mirip {$topCategory} yang pernah kamu beli"
                : "📐 Sesuai preferensi kategorimu";
        }
        // hybrid
        $parts = [];
        if ($cfScore >= $cbfScore && $cfScore > 0) {
            $parts[] = '👥 Disukai pengguna selera serupa';
        }
        if ($cbfScore > 0 && $topCategory) {
            $parts[] = "📐 mirip {$topCategory} favoritmu";
        } elseif ($cbfScore > 0) {
            $parts[] = '📐 sesuai kategori pilihanmu';
        }
        return implode(' & ', $parts) ?: '✨ Dipilih khusus untukmu';
    }

    // ----------------------------------------------------------------
    // Perbandingan CF vs CBF vs Hybrid (offline evaluation)
    // ----------------------------------------------------------------

    public function getComparisonMetrics(array $allProducts): array
    {
        $empty = ['precision' => 0.0, 'recall' => 0.0, 'f1' => 0.0, 'coverage' => 0.0, 'n_users' => 0];

        $users = $this->db->query("
            SELECT DISTINCT o.user_id
            FROM orders o
            JOIN order_detail od ON od.order_id = o.id
            WHERE o.payment_status = 'paid'
            LIMIT 100
        ")->getResultArray();

        if (empty($users)) {
            return ['cf' => $empty, 'cbf' => $empty, 'hybrid' => $empty];
        }

        $acc = [
            'cf'     => ['prec' => [], 'rec' => [], 'pids' => []],
            'cbf'    => ['prec' => [], 'rec' => [], 'pids' => []],
            'hybrid' => ['prec' => [], 'rec' => [], 'pids' => []],
        ];

        foreach ($users as $row) {
            $uid = (int)$row['user_id'];

            $purchased = $this->db->query("
                SELECT DISTINCT od.product_id
                FROM order_detail od
                JOIN orders o ON o.id = od.order_id
                WHERE o.user_id = ? AND o.payment_status = 'paid'
            ", [$uid])->getResultArray();

            if (empty($purchased)) continue;
            $purIds = array_map('intval', array_column($purchased, 'product_id'));
            $nRel   = count($purIds);

            $cbfScores = $this->cbf->getScores($uid, $allProducts);
            $cfScores  = $this->cf->getScores($uid, $allProducts);

            arsort($cfScores);
            $cfTop = array_slice(array_keys($cfScores), 0, self::TOP_N, true);

            arsort($cbfScores);
            $cbfTop = array_slice(array_keys($cbfScores), 0, self::TOP_N, true);

            $allIds    = array_unique(array_merge(array_keys($cbfScores), array_keys($cfScores)));
            $hybScores = [];
            foreach ($allIds as $pid) {
                $hybScores[$pid] = ($this->weightCF  * ($cfScores[$pid]  ?? 0))
                    + ($this->weightCBF * ($cbfScores[$pid] ?? 0));
            }
            arsort($hybScores);
            $hybTop = array_slice(array_keys($hybScores), 0, self::TOP_N, true);

            foreach (['cf' => $cfTop, 'cbf' => $cbfTop, 'hybrid' => $hybTop] as $m => $topIds) {
                $nRec  = count($topIds);
                $hits  = count(array_intersect($topIds, $purIds));
                $acc[$m]['prec'][] = $nRec > 0 ? $hits / $nRec : 0;
                $acc[$m]['rec'][]  = $nRel > 0 ? $hits / $nRel : 0;
                foreach ($topIds as $pid) {
                    $acc[$m]['pids'][$pid] = true;
                }
            }
        }

        $totalProducts = count($allProducts);
        $comparison    = [];

        foreach ($acc as $m => $data) {
            $n = count($data['prec']);
            if ($n === 0) {
                $comparison[$m] = $empty;
                continue;
            }

            $p   = array_sum($data['prec']) / $n;
            $r   = array_sum($data['rec'])  / $n;
            $f   = ($p + $r) > 0 ? 2 * $p * $r / ($p + $r) : 0;
            $cov = $totalProducts > 0 ? count($data['pids']) / $totalProducts : 0;

            $comparison[$m] = [
                'precision' => round($p, 4),
                'recall'    => round($r, 4),
                'f1'        => round($f, 4),
                'coverage'  => round($cov * 100, 1),
                'n_users'   => $n,
            ];
        }

        return $comparison;
    }
}
