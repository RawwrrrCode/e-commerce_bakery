<?php

namespace App\Models;

use CodeIgniter\Model;

class MetricsSnapshotsModel extends Model
{
    protected $table = 'metrics_snapshots';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'snapshot_date',
        'method',
        'precision',
        'recall',
        'rmse',
        'mae',
        'coverage',
        'ctr',
        'conversion_rate',
        'total_recommendations',
        'total_interactions',
        'total_clicks',
        'total_purchases',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    public function getLatestSnapshot(string $method = 'Overall'): ?array
    {
        return $this->where('method', $method)
            ->orderBy('snapshot_date', 'DESC')
            ->first();
    }

    public function getSnapshotsByDateRange(string $dateFrom, string $dateTo, ?string $method = null): array
    {
        $builder = $this->where('DATE(snapshot_date) >=', $dateFrom)
            ->where('DATE(snapshot_date) <=', $dateTo);

        if ($method) {
            $builder->where('method', $method);
        }

        return $builder->orderBy('snapshot_date', 'ASC')->findAll();
    }

    public function getComparisonData(string $dateFrom, string $dateTo): array
    {
        $methods = ['CF', 'CBF', 'Hybrid'];
        $result = [];

        foreach ($methods as $method) {
            $snapshots = $this->getSnapshotsByDateRange($dateFrom, $dateTo, $method);
            $result[$method] = $snapshots;
        }

        return $result;
    }
}
