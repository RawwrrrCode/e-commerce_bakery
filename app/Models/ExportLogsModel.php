<?php

namespace App\Models;

use CodeIgniter\Model;

class ExportLogsModel extends Model
{
    protected $table = 'export_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'export_type',
        'file_format',
        'file_name',
        'file_path',
        'file_size',
        'date_from',
        'date_to',
        'filter_method',
        'row_count',
        'created_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    public function logExport(string $type, string $format, string $fileName, string $filePath, int $fileSize, int $rowCount, int $userId, ?string $dateFrom = null, ?string $dateTo = null, ?string $method = null): bool
    {
        return (bool) $this->insert([
            'export_type'   => $type,
            'file_format'   => $format,
            'file_name'     => $fileName,
            'file_path'     => $filePath,
            'file_size'     => $fileSize,
            'date_from'     => $dateFrom,
            'date_to'       => $dateTo,
            'filter_method' => $method,
            'row_count'     => $rowCount,
            'created_by'    => $userId,
        ]);
    }

    public function getExportsByUser(int $userId): array
    {
        return $this->where('created_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getRecentExports(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
