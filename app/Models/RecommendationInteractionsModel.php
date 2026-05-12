<?php

namespace App\Models;

use CodeIgniter\Model;

class RecommendationInteractionsModel extends Model
{
    protected $table = 'recommendation_interactions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'recommendation_id',
        'user_id',
        'product_id',
        'action',
        'method',
        'action_timestamp',
        'created_at',
    ];
    protected $useTimestamps = false;

    public function logInteraction(int $userId, int $productId, string $action, ?int $recommendationId = null, ?string $method = null): bool
    {
        return (bool) $this->insert([
            'recommendation_id' => $recommendationId,
            'user_id'           => $userId,
            'product_id'        => $productId,
            'action'            => $action,
            'method'            => $method ?? 'Hybrid',
            'action_timestamp'  => date('Y-m-d H:i:s'),
            'created_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    public function getInteractionsByUser(int $userId, ?string $action = null): array
    {
        $builder = $this->where('user_id', $userId);

        if ($action) {
            $builder->where('action', $action);
        }

        return $builder->orderBy('action_timestamp', 'DESC')->findAll();
    }

    public function getInteractionsByProduct(int $productId): array
    {
        return $this->where('product_id', $productId)
            ->orderBy('action_timestamp', 'DESC')
            ->findAll();
    }

    public function getInteractionsByDateAndAction(string $dateFrom, string $dateTo, string $action): array
    {
        return $this->where('action', $action)
            ->where('DATE(action_timestamp) >=', $dateFrom)
            ->where('DATE(action_timestamp) <=', $dateTo)
            ->orderBy('action_timestamp', 'DESC')
            ->findAll();
    }

    public function countActionsByDate(string $dateFrom, string $dateTo, string $action): int
    {
        return $this->where('action', $action)
            ->where('DATE(action_timestamp) >=', $dateFrom)
            ->where('DATE(action_timestamp) <=', $dateTo)
            ->countAllResults();
    }
}
