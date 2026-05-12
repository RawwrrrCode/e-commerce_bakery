<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class DataExporter
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Export recommendation logs as CSV
     */
    public function exportRecommendationLogs(?string $dateFrom = null, ?string $dateTo = null, ?string $method = null, string $format = 'CSV'): array
    {
        $builder = $this->db->table('recommendation_logs');

        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }
        if ($method) {
            $builder->where('method', $method);
        }

        $logs = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        if ($format === 'JSON') {
            return $this->exportAsJSON($logs, 'recommendation_logs');
        } else {
            return $this->exportAsCSV($logs, 'recommendation_logs');
        }
    }

    /**
     * Export recommendation interactions
     */
    public function exportInteractions(?string $dateFrom = null, ?string $dateTo = null, string $format = 'CSV'): array
    {
        $builder = $this->db->table('recommendation_interactions');

        if ($dateFrom) {
            $builder->where('DATE(action_timestamp) >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('DATE(action_timestamp) <=', $dateTo);
        }

        $interactions = $builder->orderBy('action_timestamp', 'DESC')->get()->getResultArray();

        if ($format === 'JSON') {
            return $this->exportAsJSON($interactions, 'recommendation_interactions');
        } else {
            return $this->exportAsCSV($interactions, 'recommendation_interactions');
        }
    }

    /**
     * Export metrics snapshots
     */
    public function exportMetrics(?string $dateFrom = null, ?string $dateTo = null, string $format = 'CSV'): array
    {
        $builder = $this->db->table('metrics_snapshots');

        if ($dateFrom) {
            $builder->where('DATE(snapshot_date) >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('DATE(snapshot_date) <=', $dateTo);
        }

        $metrics = $builder->orderBy('snapshot_date', 'DESC')->get()->getResultArray();

        if ($format === 'JSON') {
            return $this->exportAsJSON($metrics, 'metrics_snapshots');
        } else {
            return $this->exportAsCSV($metrics, 'metrics_snapshots');
        }
    }

    /**
     * Export all data (comprehensive export)
     */
    public function exportAll(?string $dateFrom = null, ?string $dateTo = null, string $format = 'CSV'): array
    {
        $logs = $this->exportRecommendationLogs($dateFrom, $dateTo, null, 'array');
        $interactions = $this->exportInteractions($dateFrom, $dateTo, 'array');
        $metrics = $this->exportMetrics($dateFrom, $dateTo, 'array');

        if ($format === 'JSON') {
            return $this->exportAsJSON([
                'recommendation_logs' => $logs,
                'interactions' => $interactions,
                'metrics' => $metrics,
            ], 'full_export');
        } else {
            // For CSV, we'll export each table separately
            return $logs; // Return main logs, others can be accessed via separate exports
        }
    }

    /**
     * Convert array to CSV and save
     */
    protected function exportAsCSV(array $data, string $tableName): array
    {
        if (empty($data)) {
            return ['success' => false, 'message' => 'No data to export'];
        }

        $fileName = $tableName . '_' . date('YmdHis') . '.csv';
        $filePath = FCPATH . 'uploads/exports/' . $fileName;

        // Create directory if not exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $fp = fopen($filePath, 'w');

        // BOM UTF-8 supaya Excel baca encoding dengan benar
        fwrite($fp, "\xEF\xBB\xBF");

        // Header (pakai semicolon — standar Excel Indonesia)
        $headers = array_keys($data[0]);
        fputcsv($fp, $headers, ';');

        // Data
        foreach ($data as $row) {
            fputcsv($fp, array_values($row), ';');
        }

        fclose($fp);

        $fileSize = filesize($filePath);

        return [
            'success' => true,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'row_count' => count($data),
            'format' => 'CSV',
        ];
    }

    /**
     * Convert array to JSON and save
     */
    protected function exportAsJSON(array $data, string $tableName): array
    {
        $fileName = $tableName . '_' . date('YmdHis') . '.json';
        $filePath = FCPATH . 'uploads/exports/' . $fileName;

        // Create directory if not exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $fileSize = filesize($filePath);
        $rowCount = is_array($data) ? count($data) : 1;

        return [
            'success' => true,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'row_count' => $rowCount,
            'format' => 'JSON',
        ];
    }

    /**
     * Get file download link
     */
    public function getDownloadLink(string $fileName): string
    {
        return base_url('uploads/exports/' . $fileName);
    }
}
