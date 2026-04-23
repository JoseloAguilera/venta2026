<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'account_id', 'type', 'amount', 'reference_type', 
        'reference_id', 'description', 'session_id', 'user_id', 'date'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getMovementsByAccountAndDate($accountId, $startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('transactions.*, users.username as user_name');
        $builder->join('users', 'users.id = transactions.user_id', 'left');
        $builder->where('account_id', $accountId);
        
        if ($startDate) {
            $builder->where('date >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $builder->where('date <=', $endDate . ' 23:59:59');
        }

        $builder->orderBy('date', 'DESC');
        $builder->orderBy('id', 'DESC');
        return $builder->get()->getResultArray();
    }
}
