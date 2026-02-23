<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryTransferModel extends Model
{
    protected $table = 'inventory_transfers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'transfer_code',
        'source_warehouse_id',
        'destination_warehouse_id',
        'user_id',
        'status',
        'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'source_warehouse_id' => 'required|integer',
        'destination_warehouse_id' => 'required|integer|differs[source_warehouse_id]',
        'user_id' => 'required|integer',
        'status' => 'required|in_list[completed,pending,cancelled]',
    ];
    protected $validationMessages = [
        'destination_warehouse_id' => [
            'differs' => 'El depósito de destino debe ser diferente al de origen.',
        ],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get transfers with details
     */
    public function getTransfersWithDetails()
    {
        return $this->select('inventory_transfers.*, 
                              sw.name as source_warehouse, 
                              dw.name as destination_warehouse, 
                              users.username')
            ->join('warehouses as sw', 'sw.id = inventory_transfers.source_warehouse_id')
            ->join('warehouses as dw', 'dw.id = inventory_transfers.destination_warehouse_id')
            ->join('users', 'users.id = inventory_transfers.user_id')
            ->orderBy('inventory_transfers.created_at', 'DESC')
            ->findAll();
    }
}
