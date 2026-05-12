<?php

namespace App\Libraries;

use App\Models\RecommendationLogsModel;
use App\Models\RecommendationInteractionsModel;
use CodeIgniter\Database\BaseConnection;

class MetricsCalculator
{
    protected RecommendationLogsModel $logsModel;
    protected RecommendationInteractionsModel $interactionsModel;
    protected BaseConnection $db;

    public function __construct()
    {
        $this->logsModel = new RecommendationLogsModel();
        $this->interactionsModel = new RecommendationInteractionsModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Precision = Recommended & Clicked / Total Recommendations
     * Mengukur seberapa akurat rekomendasi (dari rekomendasi yang diberikan, berapa yang diklik)
     */
    public function calculatePrecision(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        $builder = $this->db->table('recommendation_logs');

        if ($method && $method !== 'Overall') {
            $builder->where('method', $method);
        }

        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        $totalRecs = $builder->countAllResults();

        if ($totalRecs == 0) {
            return 0.0;
        }

        $clicked = $this->db->table('recommendation_logs')
            ->where('is_clicked', true);

        if ($method && $method !== 'Overall') {
            $clicked->where('method', $method);
        }

        if ($dateFrom) {
            $clicked->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $clicked->where('DATE(created_at) <=', $dateTo);
        }

        $clickedCount = $clicked->countAllResults();

        return round(($clickedCount / $totalRecs) * 100, 2);
    }

    /**
     * Recall = Recommended & Purchased / Total Purchased
     * Mengukur cakupan rekomendasi (dari produk yang dibeli user, berapa yang sebelumnya direkomendasi)
     */
    public function calculateRecall(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        $builder = $this->db->table('recommendation_logs')
            ->join('recommendation_interactions ri', 'recommendation_logs.id = ri.recommendation_id', 'left')
            ->where('ri.action', 'purchase');

        if ($method && $method !== 'Overall') {
            $builder->where('recommendation_logs.method', $method);
        }

        if ($dateFrom) {
            $builder->where('DATE(recommendation_logs.created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(recommendation_logs.created_at) <=', $dateTo);
        }

        $recommendedAndPurchased = $builder->countAllResults();

        // Total produk yang dibeli (regardless of recommendation)
        $totalPurchasesBuilder = $this->db->table('recommendation_interactions')
            ->where('action', 'purchase');

        if ($dateFrom) {
            $totalPurchasesBuilder->where('DATE(action_timestamp) >=', $dateFrom);
        }

        if ($dateTo) {
            $totalPurchasesBuilder->where('DATE(action_timestamp) <=', $dateTo);
        }

        $totalPurchases = $totalPurchasesBuilder->countAllResults();

        if ($totalPurchases == 0) {
            return 0.0;
        }

        return round(($recommendedAndPurchased / $totalPurchases) * 100, 2);
    }

    /**
     * RMSE = sqrt(sum((predicted_score - actual_score)^2) / n)
     * Mengukur akurasi prediksi rating/relevance score
     * Menggunakan hybrid_score sebagai predicted dan click status sebagai actual
     */
    public function calculateRMSE(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        $builder = $this->db->table('recommendation_logs');

        if ($method && $method !== 'Overall') {
            $builder->where('method', $method);
        }

        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        $logs = $builder->get()->getResultArray();

        if (empty($logs)) {
            return 0.0;
        }

        $sumSquaredError = 0;
        foreach ($logs as $log) {
            $predicted = $log['hybrid_score'] ?? 0;
            $actual = $log['is_clicked'] ? 1.0 : 0.0;
            $error = $predicted - $actual;
            $sumSquaredError += $error * $error;
        }

        $rmse = sqrt($sumSquaredError / count($logs));

        return round($rmse, 4);
    }

    /**
     * MAE = sum(|predicted_score - actual_score|) / n
     */
    public function calculateMAE(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        $builder = $this->db->table('recommendation_logs');

        if ($method && $method !== 'Overall') {
            $builder->where('method', $method);
        }

        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        $logs = $builder->get()->getResultArray();

        if (empty($logs)) {
            return 0.0;
        }

        $sumAbsError = 0;
        foreach ($logs as $log) {
            $predicted = $log['hybrid_score'] ?? 0;
            $actual = $log['is_clicked'] ? 1.0 : 0.0;
            $error = abs($predicted - $actual);
            $sumAbsError += $error;
        }

        $mae = $sumAbsError / count($logs);

        return round($mae, 4);
    }

    /**
     * Coverage = Unique Products Recommended / Total Products
     * Mengukur seberapa banyak produk yang pernah direkomendasi
     */
    public function calculateCoverage(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        $builder = $this->db->table('recommendation_logs');

        if ($method && $method !== 'Overall') {
            $builder->where('method', $method);
        }

        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        $uniqueRecommended = $builder->selectCount('DISTINCT product_id', 'count')->get()->getRow()->count;

        // Total products in catalog
        $totalProducts = $this->db->table('products')->countAllResults();

        if ($totalProducts == 0) {
            return 0.0;
        }

        return round(($uniqueRecommended / $totalProducts) * 100, 2);
    }

    /**
     * CTR = Clicks / Total Recommendations * 100%
     * Same as Precision but labeled differently
     */
    public function calculateCTR(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        return $this->calculatePrecision($method, $dateFrom, $dateTo);
    }

    /**
     * Conversion Rate = Purchases / Total Recommendations * 100%
     */
    public function calculateConversionRate(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        $builder = $this->db->table('recommendation_logs');

        if ($method && $method !== 'Overall') {
            $builder->where('method', $method);
        }

        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        $totalRecs = $builder->countAllResults();

        if ($totalRecs == 0) {
            return 0.0;
        }

        $purchasesBuilder = $this->db->table('recommendation_logs')
            ->join('recommendation_interactions ri', 'recommendation_logs.id = ri.recommendation_id', 'left')
            ->where('ri.action', 'purchase');

        if ($method && $method !== 'Overall') {
            $purchasesBuilder->where('recommendation_logs.method', $method);
        }

        if ($dateFrom) {
            $purchasesBuilder->where('DATE(recommendation_logs.created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $purchasesBuilder->where('DATE(recommendation_logs.created_at) <=', $dateTo);
        }

        $purchases = $purchasesBuilder->countAllResults();

        return round(($purchases / $totalRecs) * 100, 2);
    }

    /**
     * Calculate all metrics for a given date range and method
     * Returns array dengan semua metrics
     */
    public function calculateAllMetrics(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        return [
            'precision'     => $this->calculatePrecision($method, $dateFrom, $dateTo),
            'recall'        => $this->calculateRecall($method, $dateFrom, $dateTo),
            'rmse'          => $this->calculateRMSE($method, $dateFrom, $dateTo),
            'mae'           => $this->calculateMAE($method, $dateFrom, $dateTo),
            'coverage'      => $this->calculateCoverage($method, $dateFrom, $dateTo),
            'ctr'           => $this->calculateCTR($method, $dateFrom, $dateTo),
            'conversion_rate' => $this->calculateConversionRate($method, $dateFrom, $dateTo),
        ];
    }

    /**
     * Count recommendations and interactions untuk snapshot
     */
    public function getCountMetrics(?string $method = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $logsBuilder = $this->db->table('recommendation_logs');

        if ($method && $method !== 'Overall') {
            $logsBuilder->where('method', $method);
        }

        if ($dateFrom) {
            $logsBuilder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $logsBuilder->where('DATE(created_at) <=', $dateTo);
        }

        $totalRecs = $logsBuilder->countAllResults();

        $clicksBuilder = $this->db->table('recommendation_logs')
            ->where('is_clicked', true);

        if ($method && $method !== 'Overall') {
            $clicksBuilder->where('method', $method);
        }

        if ($dateFrom) {
            $clicksBuilder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $clicksBuilder->where('DATE(created_at) <=', $dateTo);
        }

        $totalClicks = $clicksBuilder->countAllResults();

        $purchasesBuilder = $this->db->table('recommendation_interactions')
            ->where('action', 'purchase');

        if ($dateFrom) {
            $purchasesBuilder->where('DATE(action_timestamp) >=', $dateFrom);
        }

        if ($dateTo) {
            $purchasesBuilder->where('DATE(action_timestamp) <=', $dateTo);
        }

        $totalPurchases = $purchasesBuilder->countAllResults();

        $interactionsBuilder = $this->db->table('recommendation_interactions');

        if ($dateFrom) {
            $interactionsBuilder->where('DATE(action_timestamp) >=', $dateFrom);
        }

        if ($dateTo) {
            $interactionsBuilder->where('DATE(action_timestamp) <=', $dateTo);
        }

        $totalInteractions = $interactionsBuilder->countAllResults();

        return [
            'total_recommendations' => $totalRecs,
            'total_clicks'          => $totalClicks,
            'total_purchases'       => $totalPurchases,
            'total_interactions'    => $totalInteractions,
        ];
    }
}
