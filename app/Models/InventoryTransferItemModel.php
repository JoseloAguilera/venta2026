<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryTransferItemModel extends Model
{
    protected $table = 'inventory_transfer_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'transfer_id',
        'product_id',
        'quantity'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get items for a transfer
     */
    public function getItems($transferId)
    {
        return $this->select('inventory_transfer_items.*, products.code, products.name')
            ->join('products', 'products.id = inventory_transfer_items.product_id')
            ->where('transfer_id', $transferId)
            ->findAll();
    }
}
