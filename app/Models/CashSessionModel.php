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

    public function getActiveSession($accountId, $userId)
    {
        return $this->where('account_id', $accountId)
                    ->where('user_id', $userId)
                    ->where('status', 'open')
                    ->first();
    }
}
