<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleDetailModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\ProductStockModel;
use App\Models\AccountModel;
use App\Models\TransactionModel;

class Sales extends BaseController
{
    protected $saleModel;
    protected $saleDetailModel;
    protected $customerModel;
    protected $productModel;
    protected $warehouseModel;
    protected $productStockModel;
    protected $accountModel;
    protected $transactionModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleDetailModel = new SaleDetailModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->warehouseModel = new WarehouseModel();
        $this->productStockModel = new ProductStockModel();
        $this->accountModel = new AccountModel();
        $this->transactionModel = new TransactionModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check view permission
        require_permission('sales', 'view');

        $data = [
            'title' => 'Ventas',
            'sales' => $this->saleModel->getSalesWithDetails()
        ];

        return view('sales/index', $data);
    }

    public function create()
    {
        // Check insert permission
        require_permission('sales', 'insert');

        $cashSessionModel = new \App\Models\CashSessionModel();
        $userId = $this->session->get('id') ?? $this->session->get('user_id');
        $activeSession = $cashSessionModel->getActiveSessionForUser($userId);

        $cashLimitAlert = false;
        $currentCashBalance = 0;
        if ($activeSession) {
            $summary = $cashSessionModel->getSessionSummary($activeSession['id']);
            $currentCashBalance = (float)$activeSession['opening_amount'] + $summary['net_movement'];
            $cashLimitThreshold = (float)model('SettingsModel')->getValue('cash_limit_threshold', 5000000);
            if ($currentCashBalance >= $cashLimitThreshold) {
                $cashLimitAlert = true;
            }
        }

        $data = [
            'title' => 'Nueva Venta',
            'customers' => $this->customerModel->findAll(),
            'products' => $this->productModel->getProductsWithCategory(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(), // Add warehouses
            'accounts' => $this->accountModel->where('status', 1)->findAll(),
            'sale_number' => $this->saleModel->generateSaleNumber(),
            'activeSession' => $activeSession,
            'currentCashBalance' => $currentCashBalance,
            'cashLimitAlert' => $cashLimitAlert
        ];

        return view('sales/create', $data);
    }

    public function store()
    {
        // Check insert permission
        require_permission('sales', 'insert');

        $this->db->transStart();

        try {
            // Validar datos básicos
            $customerId = $this->request->getPost('customer_id');
            $warehouseId = $this->request->getPost('warehouse_id'); // Get warehouse
            $paymentType = $this->request->getPost('payment_type');
            $accountId = $this->request->getPost('account_id');
            $products = $this->request->getPost('products');
            $authPassword = $this->request->getPost('auth_password');

            if (empty($products)) {
                return redirect()->back()->with('error', 'Debe agregar al menos un producto');
            }

            if (empty($warehouseId)) {
                return redirect()->back()->with('error', 'Debe seleccionar un depósito');
            }

            // Validar precios mínimos y STOCK en el depósito seleccionado
            $settingsModel = new \App\Models\SettingsModel();
            $minPricePassword = $settingsModel->getValue('min_price_password', '0000');
            $authRequired = false;

            foreach ($products as $item) {
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    // Validar Stock en Depósito
                    $currentStock = $this->productStockModel->getStock($item['product_id'], $warehouseId);
                    if ($currentStock < $item['quantity']) {
                        return redirect()->back()->with('error', "Stock insuficiente para {$product['name']} en el depósito seleccionado. Stock actual: $currentStock");
                    }

                    // Si el precio de venta es menor al mínimo
                    if ($item['price'] < $product['min_sale_price']) {
                        $authRequired = true;
                    }
                }
            }

            if ($authRequired) {
                if (empty($authPassword)) {
                    return redirect()->back()->withInput()->with('error', 'Se requiere autorización para vender por debajo del precio mínimo');
                }

                if ($authPassword !== $minPricePassword) {
                    return redirect()->back()->withInput()->with('error', 'Contraseña de autorización incorrecta');
                }
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($products as $product) {
                $subtotal += $product['quantity'] * $product['price'];
            }

            $tax = $subtotal * 0; // Sin impuestos por ahora
            $total = $subtotal + $tax;

            $userId = $this->session->get('id') ?? $this->session->get('user_id');

            // Crear venta
            $saleData = [
                'customer_id' => $customerId,
                'user_id' => $userId,
                'warehouse_id' => $warehouseId, // Record warehouse
                'sale_number' => $this->saleModel->generateSaleNumber(),
                'date' => date('Y-m-d'),
                'payment_type' => $paymentType,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $paymentType === 'cash' ? 'paid' : 'pending'
            ];

            $saleId = $this->saleModel->insert($saleData);

            // Registrar Medios de Pago Mixtos
            $payments = $this->request->getPost('payments');
            $salePaymentModel = new \App\Models\SalePaymentModel();
            $cashSessionModel = new \App\Models\CashSessionModel();
            $activeSession = $cashSessionModel->getActiveSessionForUser($userId);

            if (!empty($payments) && is_array($payments) && $paymentType === 'cash') {
                foreach ($payments as $pay) {
                    $payAccountId = $pay['account_id'] ?? null;
                    $payAmount    = (float)($pay['amount'] ?? 0);

                    if (!empty($payAccountId) && $payAmount > 0) {
                        $accInfo = $this->accountModel->find($payAccountId);
                        $accName = $accInfo ? $accInfo['name'] : 'Caja';
                        $accType = $accInfo ? $accInfo['type'] : 'cash';

                        // Insertar desglose de pago
                        $salePaymentModel->insert([
                            'sale_id'    => $saleId,
                            'account_id' => $payAccountId,
                            'amount'     => $payAmount
                        ]);

                        // Registrar transacción financiera asociada
                        $this->transactionModel->insert([
                            'account_id'     => $payAccountId,
                            'type'           => 'income',
                            'amount'         => $payAmount,
                            'reference_type' => 'sale',
                            'reference_id'   => $saleId,
                            'description'    => 'Cobro de venta #' . $saleData['sale_number'] . ' (' . $accName . ')',
                            'session_id'     => ($accType === 'cash' && $activeSession) ? $activeSession['id'] : null,
                            'user_id'        => $userId,
                            'date'           => date('Y-m-d H:i:s')
                        ]);

                        $this->accountModel->increaseBalance($payAccountId, $payAmount);
                    }
                }
            } elseif (!empty($accountId) && $paymentType === 'cash') {
                // Modo compatibilidad legacy (pago único)
                $accInfo = $this->accountModel->find($accountId);
                $accType = $accInfo ? $accInfo['type'] : 'cash';

                $salePaymentModel->insert([
                    'sale_id'    => $saleId,
                    'account_id' => $accountId,
                    'amount'     => $total
                ]);

                $this->transactionModel->insert([
                    'account_id'     => $accountId,
                    'type'           => 'income',
                    'amount'         => $total,
                    'reference_type' => 'sale',
                    'reference_id'   => $saleId,
                    'description'    => 'Cobro de venta #' . $saleData['sale_number'],
                    'session_id'     => ($accType === 'cash' && $activeSession) ? $activeSession['id'] : null,
                    'user_id'        => $userId,
                    'date'           => date('Y-m-d H:i:s')
                ]);
                $this->accountModel->increaseBalance($accountId, $total);
            }

            // Crear detalles y actualizar stock
            foreach ($products as $product) {
                $detailData = [
                    'sale_id' => $saleId,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                    'description' => $product['description'] ?? null
                ];

                $this->saleDetailModel->insert($detailData);

                // Reducir stock del depósito
                $this->productModel->updateStock($product['product_id'], $product['quantity'], 'subtract', $warehouseId);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Error al crear la venta');
            }

            log_activity('Ventas', 'Crear Venta', "Nueva venta creada #{$saleData['sale_number']} por $" . number_format($total, 2));

            // Redirect to view instead of index
            return redirect()->to('/sales/view/' . $saleId)->with('success', 'Venta creada correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        // Check view permission
        require_permission('sales', 'view');

        $sale = $this->saleModel->getSaleWithDetails($id);

        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Venta no encontrada');
        }

        $salePaymentModel = new \App\Models\SalePaymentModel();
        $salePayments = $salePaymentModel->getPaymentsBySale($id);

        $data = [
            'title' => 'Detalle de Venta #' . $sale['sale_number'],
            'sale' => $sale,
            'sale_payments' => $salePayments,
            'pending_balance' => $this->saleModel->getPendingBalance($id)
        ];

        return view('sales/view', $data);
    }

    public function ticket($id)
    {
        // Check view permission
        require_permission('sales', 'view');

        $sale = $this->saleModel->getSaleWithDetails($id);

        if (!$sale) {
            return "Venta no encontrada";
        }

        $salePaymentModel = new \App\Models\SalePaymentModel();
        $salePayments = $salePaymentModel->getPaymentsBySale($id);

        $data = [
            'sale' => $sale,
            'sale_payments' => $salePayments,
            'settings' => model('SettingsModel')->getAllSettings() // Corrected method name
        ];

        return view('sales/ticket', $data);
    }

    public function annul($id)
    {
        // Check delete permission (we use delete permission for annulment)
        require_permission('sales', 'delete');

        $this->db->transStart();

        try {
            $sale = $this->saleModel->getSaleWithDetails($id);

            if (!$sale) {
                return redirect()->to('/sales')->with('error', 'Venta no encontrada');
            }

            if ($sale['status'] === 'cancelled') {
                return redirect()->to('/sales')->with('error', 'La venta ya está anulada');
            }

            $warehouseId = $sale['warehouse_id'];

            // Restaurar stock
            foreach ($sale['details'] as $detail) {
                $this->productModel->updateStock($detail['product_id'], $detail['quantity'], 'add', $warehouseId);
            }

            // Anular venta (Cambiar estado a cancelled)
            $this->saleModel->update($id, ['status' => 'cancelled']);

            $this->db->transComplete();

            log_activity('Ventas', 'Anular Venta', "Venta #{$sale['sale_number']} (ID: $id) por monto de $" . number_format($sale['total'], 2) . " anulada correctamente. Stock restaurado.");

            return redirect()->to('/sales')->with('success', 'Venta anulada correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/sales')->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }

    public function validateAuth()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $password = $this->request->getPost('password');
        $settingsModel = new \App\Models\SettingsModel();
        $minPricePassword = $settingsModel->getValue('min_price_password', '0000');

        if ($password === $minPricePassword) {
            return $this->response->setJSON(['valid' => true]);
        } else {
            return $this->response->setJSON(['valid' => false]);
        }
    }
    public function searchProducts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $term = $this->request->getGet('term');

        $productModel = new \App\Models\ProductModel();

        // This is generic search, stock check happens at selection or validation
        // Could be enhanced to show stock per warehouse if warehouse_id is passed
        $warehouseId = $this->request->getGet('warehouse_id');

        $query = $productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id');

        if (!empty($term)) {
            $query->groupStart()
                ->like('products.name', $term)
                ->orLike('products.code', $term)
                ->orLike('products.imei1', $term)
                ->orLike('products.imei2', $term)
                ->groupEnd();
        }

        // Limit to 50 results for performance
        $products = $query->limit(50)->find();

        // If warehouse is selected, attach specific stock info
        if ($warehouseId) {
            $productStockModel = new ProductStockModel();
            foreach ($products as &$product) {
                $product['warehouse_stock'] = $productStockModel->getStock($product['id'], $warehouseId);
                $product['stock'] = $product['warehouse_stock']; // Override for UI
            }
        }

        return $this->response->setJSON($products);
    }

    public function updateObservations($id)
    {
        // Check view permission as they are viewing the sale to edit it. 
        // We also check the password for actual authorization.
        require_permission('sales', 'view');

        $password = $this->request->getPost('auth_password');
        $settingsModel = new \App\Models\SettingsModel();
        $minPricePassword = $settingsModel->getValue('min_price_password', '0000');

        if ($password !== $minPricePassword) {
            return redirect()->back()->with('error', 'Contraseña incorrecta');
        }

        $observations = $this->request->getPost('observations');

        if (!empty($observations) && is_array($observations)) {
            $this->db->transStart();
            try {
                foreach ($observations as $detailId => $description) {
                    $this->saleDetailModel->update($detailId, ['description' => $description]);
                }
                $this->db->transComplete();
                if ($this->db->transStatus() === false) {
                    return redirect()->back()->with('error', 'Error al actualizar las observaciones');
                }
                return redirect()->back()->with('success', 'Observaciones actualizadas correctamente');
            } catch (\Exception $e) {
                $this->db->transRollback();
                return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'No se recibieron observaciones para actualizar');
    }
}
