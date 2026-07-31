<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'username', 'module', 'action', 'description',
        'ip_address', 'old_values', 'new_values'
    ];

    protected $useTimestamps = false;

    public function getLogsWithFilters($startDate = null, $endDate = null, $module = null, $userId = null)
    {
        $builder = $this->orderBy('id', 'DESC');

        if ($startDate) {
            $builder->where('DATE(created_at) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(created_at) <=', $endDate);
        }
        if ($module) {
            $builder->where('module', $module);
        }
        if ($userId) {
            $builder->where('user_id', $userId);
        }

        return $builder->findAll();
    }
}
