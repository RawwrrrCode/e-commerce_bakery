<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Libraries\HybridRecommender;

class Recommendation extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $allProducts  = $productModel->findAll();
        $recommender  = new HybridRecommender();

        $metrics    = $recommender->getEvaluationMetrics($allProducts);
        $comparison = $recommender->getComparisonMetrics($allProducts);

        // Recent logs
        $db = \Config\Database::connect();
        $recentLogs = $db->table('recommendation_logs rl')
            ->select('rl.*, u.name as user_name, p.name as product_name', false)
            ->join('users u', 'u.id = rl.user_id', 'left')
            ->join('products p', 'p.id = rl.product_id', 'left')
            ->orderBy('rl.created_at', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        // Jumlah rating tersedia (untuk info matrix CF)
        $totalRatings = $db->table('ratings')->countAllResults();
        $totalUsers   = $db->table('users')->countAllResults();

        return view('admin/recommendation', [
            'metrics'      => $metrics,
            'comparison'   => $comparison,
            'recentLogs'   => $recentLogs,
            'totalRatings' => $totalRatings,
            'totalUsers'   => $totalUsers,
        ]);
    }

    /**
     * Export data evaluasi ke CSV atau JSON
     */
    public function export()
    {
        $format   = strtoupper($this->request->getPost('format') ?? 'CSV');
        if (!in_array($format, ['CSV', 'JSON'])) $format = 'CSV';

        $exporter = new \App\Libraries\DataExporter();
        $result   = $exporter->exportRecommendationLogs(null, null, null, $format);

        if (!$result['success']) {
            return $this->response->setJSON(['success' => false, 'message' => $result['message'] ?? 'Tidak ada data']);
        }

        return $this->response->setJSON([
            'success'       => true,
            'file_name'     => $result['file_name'],
            'download_link' => $exporter->getDownloadLink($result['file_name']),
            'row_count'     => $result['row_count'],
        ]);
    }

    /**
     * Halaman laporan PDF (buka di tab baru, print/save as PDF dari browser)
     */
    public function exportPdf()
    {
        $productModel = new ProductModel();
        $allProducts  = $productModel->findAll();
        $recommender  = new HybridRecommender();

        $metrics    = $recommender->getEvaluationMetrics($allProducts);
        $comparison = $recommender->getComparisonMetrics($allProducts);

        $db           = \Config\Database::connect();
        $totalRatings = $db->table('ratings')->countAllResults();
        $totalUsers   = $db->table('users')->countAllResults();

        return view('admin/recommendation_pdf', [
            'metrics'      => $metrics,
            'comparison'   => $comparison,
            'totalRatings' => $totalRatings,
            'totalUsers'   => $totalUsers,
            'generatedAt'  => date('d F Y, H:i'),
        ]);
    }

    /**
     * Halaman Data Input CF — rating matrix & interaksi user
     */
    public function dataInput()
    {
        $db = \Config\Database::connect();

        // Data rating lengkap
        $ratings = $db->table('ratings r')
            ->select('r.id, u.name as user_name, p.name as product_name, p.category, r.rating, r.created_at', false)
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('products p', 'p.id = r.product_id', 'left')
            ->orderBy('r.created_at', 'DESC')
            ->limit(100)
            ->get()->getResultArray();

        // Data interaksi (purchase)
        $purchases = $db->query("
            SELECT u.name as user_name, p.name as product_name, p.category,
                   od.qty, o.created_at, o.payment_status
            FROM order_detail od
            JOIN orders o ON o.id = od.order_id
            JOIN users u ON u.id = o.user_id
            JOIN products p ON p.id = od.product_id
            WHERE o.payment_status = 'paid'
            ORDER BY o.created_at DESC
            LIMIT 100
        ")->getResultArray();

        // Statistik
        $totalRatings   = $db->table('ratings')->countAllResults();
        $avgRating      = $db->query("SELECT ROUND(AVG(rating),2) as avg FROM ratings")->getRow()->avg ?? 0;
        $totalPurchases = $db->query("
            SELECT COUNT(*) as cnt FROM order_detail od
            JOIN orders o ON o.id = od.order_id WHERE o.payment_status = 'paid'
        ")->getRow()->cnt ?? 0;
        $totalUsers     = $db->query("
            SELECT COUNT(DISTINCT user_id) as cnt FROM ratings
        ")->getRow()->cnt ?? 0;

        // Distribusi rating (1–5)
        $ratingDist = $db->query("
            SELECT rating, COUNT(*) as total FROM ratings GROUP BY rating ORDER BY rating
        ")->getResultArray();

        // Top produk paling banyak dirating
        $topRated = $db->query("
            SELECT p.name, p.category, COUNT(*) as jml_rating, ROUND(AVG(r.rating),2) as avg_rating
            FROM ratings r JOIN products p ON p.id = r.product_id
            GROUP BY r.product_id ORDER BY jml_rating DESC LIMIT 10
        ")->getResultArray();

        // Bobot hybrid saat ini
        $recommender = new HybridRecommender();
        $weights = $recommender->getWeights();

        return view('admin/recommendation_data', [
            'ratings'        => $ratings,
            'purchases'      => $purchases,
            'totalRatings'   => $totalRatings,
            'avgRating'      => $avgRating,
            'totalPurchases' => $totalPurchases,
            'totalUsers'     => $totalUsers,
            'ratingDist'     => $ratingDist,
            'topRated'       => $topRated,
            'weights'        => $weights,
        ]);
    }

    /**
     * Simpan konfigurasi bobot hybrid dari admin
     */
    public function saveConfig()
    {
        $weightCF = (float)$this->request->getPost('weight_cf');
        $weightCF = max(0.1, min(0.9, $weightCF));
        $weightCBF = round(1 - $weightCF, 2);

        $config = json_encode([
            'weight_cf'  => round($weightCF, 2),
            'weight_cbf' => $weightCBF,
        ]);

        file_put_contents(WRITEPATH . 'hybrid_config.json', $config);

        return $this->response->setJSON([
            'success'    => true,
            'weight_cf'  => round($weightCF, 2),
            'weight_cbf' => $weightCBF,
        ]);
    }

    /**
     * Tandai rekomendasi sebagai "diklik" (dipanggil via AJAX)
     */
    public function trackClick()
    {
        $userId    = session()->get('user_id');
        $productId = $this->request->getPost('product_id');

        if ($userId && $productId) {
            $db = \Config\Database::connect();
            $db->table('recommendation_logs')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->update(['is_clicked' => 1]);
        }

        return $this->response->setJSON(['ok' => true]);
    }
}
