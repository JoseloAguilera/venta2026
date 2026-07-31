<?php

namespace App\Models;

use CodeIgniter\Model;

class CashSessionModel extends Model
{
    protected $table            = 'cash_register_sessions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'account_id', 'user_id', 'opening_time', 'closing_time',
        'opening_amount', 'closing_amount', 'discrepancy', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveSessionForUser($userId)
    {
        return $this->select('cash_register_sessions.*, accounts.name as account_name, accounts.type as account_type')
                    ->join('accounts', 'accounts.id = cash_register_sessions.account_id', 'left')
                    ->where('cash_register_sessions.user_id', $userId)
                    ->where('cash_register_sessions.status', 'open')
                    ->first();
    }

    public function getActiveSessionByAccount($accountId)
    {
        return $this->where('account_id', $accountId)
                    ->where('status', 'open')
                    ->first();
    }

    public function getSessionWithDetails($id)
    {
        return $this->select('cash_register_sessions.*, accounts.name as account_name, accounts.type as account_type, users.username as user_name')
                    ->join('accounts', 'accounts.id = cash_register_sessions.account_id', 'left')
                    ->join('users', 'users.id = cash_register_sessions.user_id', 'left')
                    ->where('cash_register_sessions.id', $id)
                    ->first();
    }

    public function getSessionsList($startDate = null, $endDate = null, $userId = null)
    {
        $builder = $this->select('cash_register_sessions.*, accounts.name as account_name, users.username as user_name')
                        ->join('accounts', 'accounts.id = cash_register_sessions.account_id', 'left')
                        ->join('users', 'users.id = cash_register_sessions.user_id', 'left');

        if ($startDate) {
            $builder->where('DATE(cash_register_sessions.opening_time) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(cash_register_sessions.opening_time) <=', $endDate);
        }
        if ($userId) {
            $builder->where('cash_register_sessions.user_id', $userId);
        }

        return $builder->orderBy('cash_register_sessions.id', 'DESC')->findAll();
    }

    public function getSessionSummary($sessionId)
    {
        $db = \Config\Database::connect();
        
        // Obtenemos los movimientos asociados a esta sesión en la tabla transactions
        $builder = $db->table('transactions');
        $builder->select('type, reference_type, SUM(amount) as total');
        $builder->where('session_id', $sessionId);
        $builder->groupBy(['type', 'reference_type']);
        $movements = $builder->get()->getResultArray();

        $summary = [
            'sales_income'    => 0,
            'collection_income' => 0,
            'manual_income'   => 0,
            'total_income'    => 0,
            'expense_out'     => 0,
            'payment_out'     => 0,
            'manual_expense'  => 0,
            'total_expense'   => 0,
            'net_movement'    => 0
        ];

        foreach ($movements as $mov) {
            $amount = (float)$mov['total'];
            if ($mov['type'] === 'income') {
                $summary['total_income'] += $amount;
                if ($mov['reference_type'] === 'sale') {
                    $summary['sales_income'] += $amount;
                } elseif ($mov['reference_type'] === 'collection') {
                    $summary['collection_income'] += $amount;
                } else {
                    $summary['manual_income'] += $amount;
                }
            } elseif ($mov['type'] === 'expense') {
                $summary['total_expense'] += $amount;
                if ($mov['reference_type'] === 'expense') {
                    $summary['expense_out'] += $amount;
                } elseif ($mov['reference_type'] === 'payment') {
                    $summary['payment_out'] += $amount;
                } else {
                    $summary['manual_expense'] += $amount;
                }
            }
        }

        $summary['net_movement'] = $summary['total_income'] - $summary['total_expense'];
        return $summary;
    }
}
