<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\MetricsCalculator;
use App\Models\MetricsSnapshotsModel;

class Metrics extends BaseController
{
    protected MetricsCalculator $calculator;
    protected MetricsSnapshotsModel $snapshotsModel;

    public function __construct()
    {
        $this->calculator = new MetricsCalculator();
        $this->snapshotsModel = new MetricsSnapshotsModel();
    }

    /**
     * Main metrics dashboard
     */
    public function index()
    {
        $user = session()->get();
        if ($user['role'] !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');
        $method = $this->request->getGet('method') ?? 'Overall';

        // Get snapshots for the date range
        $snapshots = $this->snapshotsModel->getSnapshotsByDateRange($dateFrom, $dateTo, $method);

        // Get comparison data (CF vs CBF vs Hybrid)
        $comparisonData = $this->snapshotsModel->getComparisonData($dateFrom, $dateTo);

        // Get latest metrics
        $latestSnapshot = $this->snapshotsModel->getLatestSnapshot($method);

        return view('admin/metrics_simple', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'method' => $method,
            'snapshots' => $snapshots,
            'comparisonData' => $comparisonData,
            'latestSnapshot' => $latestSnapshot,
        ]);
    }

    /**
     * API endpoint for dashboard data
     */
    public function data()
    {
        $user = session()->get();
        if ($user['role'] !== 'admin' || !$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $methods = ['CF', 'CBF', 'Hybrid'];
        $data = [];

        foreach ($methods as $method) {
            $snapshots = $this->snapshotsModel->getSnapshotsByDateRange($dateFrom, $dateTo, $method);
            $data[$method] = [
                'snapshots' => $snapshots,
                'latest' => $this->snapshotsModel->getLatestSnapshot($method),
            ];
        }

        // Overall metrics
        $allSnapshots = $this->snapshotsModel->getSnapshotsByDateRange($dateFrom, $dateTo);
        $overallMetrics = [
            'total_recommendations' => array_sum(array_column($allSnapshots, 'total_recommendations')),
            'total_clicks' => array_sum(array_column($allSnapshots, 'total_clicks')),
            'total_purchases' => array_sum(array_column($allSnapshots, 'total_purchases')),
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'overall' => $overallMetrics,
        ]);
    }

    /**
     * Calculate metrics for a specific date
     */
    public function calculate()
    {
        $user = session()->get();
        if ($user['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $date = $this->request->getPost('date') ?? date('Y-m-d');
        $method = $this->request->getPost('method');

        try {
            new \DateTime($date);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid date']);
        }

        $methods = $method ? [$method] : ['CF', 'CBF', 'Hybrid', 'Overall'];

        foreach ($methods as $m) {
            $metrics = $this->calculator->calculateAllMetrics($m, $date, $date);
            $counts = $this->calculator->getCountMetrics($m, $date, $date);

            $snapshotData = [
                'snapshot_date'         => $date,
                'method'                => $m,
                'precision'             => $metrics['precision'],
                'recall'                => $metrics['recall'],
                'rmse'                  => $metrics['rmse'],
                'mae'                   => $metrics['mae'],
                'coverage'              => $metrics['coverage'],
                'ctr'                   => $metrics['ctr'],
                'conversion_rate'       => $metrics['conversion_rate'],
                'total_recommendations' => $counts['total_recommendations'],
                'total_interactions'    => $counts['total_interactions'],
                'total_clicks'          => $counts['total_clicks'],
                'total_purchases'       => $counts['total_purchases'],
            ];

            // Delete and insert
            $this->snapshotsModel->where('snapshot_date', $date)
                ->where('method', $m)
                ->delete();

            $this->snapshotsModel->insert($snapshotData);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Metrics calculated successfully',
        ]);
    }

    /**
     * Export data (recommendations, interactions, metrics)
     */
    public function export()
    {
        $user = session()->get();
        if ($user['role'] !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized');
        }

        $exportType = $this->request->getPost('export_type') ?? 'logs';
        $format = $this->request->getPost('format') ?? 'CSV';
        $dateFrom = $this->request->getPost('date_from');
        $dateTo = $this->request->getPost('date_to');
        $method = $this->request->getPost('method');

        $exporter = new \App\Libraries\DataExporter();
        $logsModel = new \App\Models\ExportLogsModel();

        try {
            $result = match ($exportType) {
                'logs' => $exporter->exportRecommendationLogs($dateFrom, $dateTo, $method, $format),
                'interactions' => $exporter->exportInteractions($dateFrom, $dateTo, $format),
                'metrics' => $exporter->exportMetrics($dateFrom, $dateTo, $format),
                'all' => $exporter->exportAll($dateFrom, $dateTo, $format),
                default => ['success' => false, 'message' => 'Invalid export type'],
            };

            if ($result['success']) {
                // Log the export
                $logsModel->logExport(
                    $exportType,
                    $format,
                    $result['file_name'],
                    $result['file_path'],
                    $result['file_size'],
                    $result['row_count'],
                    $user['id'],
                    $dateFrom,
                    $dateTo,
                    $method
                );

                return $this->response->setJSON([
                    'success' => true,
                    'file_name' => $result['file_name'],
                    'download_link' => $exporter->getDownloadLink($result['file_name']),
                    'file_size' => $this->formatBytes($result['file_size']),
                    'row_count' => $result['row_count'],
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => $result['message'] ?? 'Export failed']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Export error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Export error: ' . $e->getMessage()]);
        }
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
