<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $table            = 'accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'type', 'balance', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Type options
    public const TYPE_CASH = 'cash';
    public const TYPE_BANK = 'bank';
    public const TYPE_DIGITAL_WALLET = 'digital_wallet';

    // Status options
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    /**
     * Increase balance directly
     */
    public function increaseBalance($id, $amount)
    {
        return $this->builder()
                    ->where('id', $id)
                    ->set('balance', "balance + {$amount}", false)
                    ->update();
    }

    /**
     * Decrease balance directly
     */
    public function decreaseBalance($id, $amount)
    {
        return $this->builder()
                    ->where('id', $id)
                    ->set('balance', "balance - {$amount}", false)
                    ->update();
    }
}
