<?php

namespace App\Models;

use CodeIgniter\Model;

class RecommendationLogsModel extends Model
{
    protected $table = 'recommendation_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'product_id',
        'method',
        'cf_score',
        'cbf_score',
        'hybrid_score',
        'recommendation_rank',
        'position_in_list',
        'is_clicked',
    ];
    protected $useTimestamps = false;

    public function logRecommendation(int $userId, int $productId, string $method, float $cfScore, float $cbfScore, float $hybridScore, int $rank, int $position): bool
    {
        return (bool) $this->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'method' => $method,
            'cf_score' => $cfScore,
            'cbf_score' => $cbfScore,
            'hybrid_score' => $hybridScore,
            'recommendation_rank' => $rank,
            'position_in_list' => $position,
            'is_clicked' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function markAsClicked(int $logId): bool
    {
        return (bool) $this->update($logId, ['is_clicked' => true]);
    }

    public function markProductAsClicked(int $userId, int $productId): bool
    {
        return (bool) $this->where('user_id', $userId)
            ->where('product_id', $productId)
            ->set('is_clicked', true)
            ->update();
    }

    public function getRecommendationsByDate(string $dateFrom, string $dateTo): array
    {
        return $this->where('DATE(created_at) >=', $dateFrom)
            ->where('DATE(created_at) <=', $dateTo)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getRecommendationsByMethod(string $method, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $builder = $this->where('method', $method);

        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
}
