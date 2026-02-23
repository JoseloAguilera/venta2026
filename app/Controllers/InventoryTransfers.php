<?php

namespace App\Controllers;

use App\Models\InventoryTransferModel;
use App\Models\InventoryTransferItemModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\ProductStockModel;

class InventoryTransfers extends BaseController
{
    protected $transferModel;
    protected $transferItemModel;
    protected $productModel;
    protected $warehouseModel;
    protected $productStockModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->transferModel = new InventoryTransferModel();
        $this->transferItemModel = new InventoryTransferItemModel();
        $this->productModel = new ProductModel();
        $this->warehouseModel = new WarehouseModel();
        $this->productStockModel = new ProductStockModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Transferencias entre Depósitos',
            'transfers' => $this->transferModel->getTransfersWithDetails()
        ];

        return view('inventory_transfers/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nueva Transferencia',
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'products' => $this->productModel->getProductsWithCategory()
        ];

        return view('inventory_transfers/create', $data);
    }

    public function store()
    {
        $this->db->transStart();

        try {
            $sourceId = $this->request->getPost('source_warehouse_id');
            $destId = $this->request->getPost('destination_warehouse_id');
            $notes = $this->request->getPost('notes');
            $products = $this->request->getPost('products');
            $quantities = $this->request->getPost('quantities');

            if ($sourceId == $destId) {
                return redirect()->back()->with('error', 'El depósito de origen y destino no pueden ser el mismo.');
            }

            if (empty($products) || empty($quantities)) {
                return redirect()->back()->with('error', 'Debe seleccionar al menos un producto.');
            }

            // Generate transfer code
            $code = 'TR-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            // Create Transfer Header
            $transferId = $this->transferModel->insert([
                'transfer_code' => $code,
                'source_warehouse_id' => $sourceId,
                'destination_warehouse_id' => $destId,
                'user_id' => $this->session->get('id'),
                'status' => 'completed', // Immediate transfer for now
                'notes' => $notes
            ]);

            if (!$transferId) {
                throw new \Exception('Error al crear la cabecera de la transferencia.');
            }

            // Process Items
            foreach ($products as $index => $productId) {
                $quantity = (int) $quantities[$index];

                if ($quantity <= 0)
                    continue;

                // Check stock in source warehouse
                $currentStock = $this->productStockModel->getStock($productId, $sourceId);

                if ($currentStock < $quantity) {
                    $product = $this->productModel->find($productId);
                    throw new \Exception("Stock insuficiente para '{$product['name']}'. Stock actual: $currentStock, solicitado: $quantity.");
                }

                // Insert Item
                $this->transferItemModel->insert([
                    'transfer_id' => $transferId,
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);

                // Update Stocks
                $this->productModel->updateStock($productId, $quantity, 'subtract', $sourceId);
                $this->productModel->updateStock($productId, $quantity, 'add', $destId);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Error en la transacción de base de datos.');
            }

            return redirect()->to('/inventory-transfers')->with('success', 'Transferencia realizada con éxito.');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $transfer = $this->transferModel->select('inventory_transfers.*, 
                                                sw.name as source_warehouse, 
                                                dw.name as destination_warehouse, 
                                                users.username')
            ->join('warehouses as sw', 'sw.id = inventory_transfers.source_warehouse_id')
            ->join('warehouses as dw', 'dw.id = inventory_transfers.destination_warehouse_id')
            ->join('users', 'users.id = inventory_transfers.user_id')
            ->find($id);

        if (!$transfer) {
            return redirect()->to('/inventory-transfers')->with('error', 'Transferencia no encontrada.');
        }

        // Get items with product details
        $items = $this->transferItemModel->select('inventory_transfer_items.*, products.code, products.name')
            ->join('products', 'products.id = inventory_transfer_items.product_id')
            ->where('transfer_id', $id)
            ->findAll();

        $data = [
            'title' => 'Detalle de Transferencia ' . $transfer['transfer_code'],
            'transfer' => $transfer,
            'items' => $items
        ];

        return view('inventory_transfers/view', $data);
    }

    // Helper for AJAX to check stock
    public function getStock()
    {
        $productId = $this->request->getGet('product_id');
        $warehouseId = $this->request->getGet('warehouse_id');

        if (!$productId || !$warehouseId) {
            return $this->response->setJSON(['error' => 'Missing parameters']);
        }

        $stock = $this->productStockModel->getStock($productId, $warehouseId);
        return $this->response->setJSON(['stock' => (int) $stock]);
    }
}
