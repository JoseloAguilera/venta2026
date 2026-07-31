<?php

namespace App\Models;

use CodeIgniter\Model;

class SalePaymentModel extends Model
{
    protected $table            = 'sale_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['sale_id', 'account_id', 'amount'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPaymentsBySale($saleId)
    {
        return $this->select('sale_payments.*, accounts.name as account_name, accounts.type as account_type')
                    ->join('accounts', 'accounts.id = sale_payments.account_id', 'left')
                    ->where('sale_payments.sale_id', $saleId)
                    ->findAll();
    }
}
