<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleDetailModel;
use App\Models\UserModel;
use App\Models\ProductModel;

class Reports extends BaseController
{
    protected $saleModel;
    protected $saleDetailModel;
    protected $userModel;
    protected $productModel;
    protected $db;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleDetailModel = new SaleDetailModel();
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'permission']);
    }

    public function sales()
    {
        require_permission('sales', 'view');

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');
        $sellerId = $this->request->getGet('seller_id');

        $query = $this->saleModel->select('sales.*, customers.name as customer_name, users.username as seller_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->join('users', 'users.id = sales.user_id')
            ->where('sales.date >=', $dateFrom)
            ->where('sales.date <=', $dateTo)
            ->where('sales.status !=', 'cancelled');

        if (!empty($sellerId)) {
            $query->where('sales.user_id', $sellerId);
        }

        $sales = $query->orderBy('sales.date', 'DESC')->findAll();

        $totalSales = array_sum(array_column($sales, 'total'));

        $data = [
            'title' => 'Reporte de Ventas',
            'sales' => $sales,
            'totalSales' => $totalSales,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sellerId' => $sellerId,
            'sellers' => $this->userModel->findAll()
        ];

        return view('reports/sales', $data);
    }

    public function profitBySale()
    {
        require_permission('sales', 'view');

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $sql = "
            SELECT 
                s.id, 
                s.sale_number, 
                s.date, 
                c.name as customer_name, 
                s.total,
                (SELECT SUM(sd.quantity * p.cost_price) 
                 FROM sale_details sd 
                 JOIN products p ON p.id = sd.product_id 
                 WHERE sd.sale_id = s.id) as total_cost
            FROM sales s
            JOIN customers c ON c.id = s.customer_id
            WHERE s.date >= ? AND s.date <= ? AND s.status != 'cancelled'
            ORDER BY s.date DESC
        ";

        $sales = $this->db->query($sql, [$dateFrom, $dateTo])->getResultArray();

        $totalProfit = 0;
        foreach ($sales as $s) {
            $totalProfit += ($s['total'] - $s['total_cost']);
        }

        $data = [
            'title' => 'Ganancia por Venta',
            'sales' => $sales,
            'totalProfit' => $totalProfit,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ];

        return view('reports/profit_by_sale', $data);
    }

    public function profitByItem()
    {
        require_permission('sales', 'view');

        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $sql = "
            SELECT 
                p.name as product_name,
                p.code as product_code,
                SUM(sd.quantity) as total_qty,
                SUM(sd.subtotal) as total_amount,
                SUM(sd.quantity * p.cost_price) as total_cost
            FROM sale_details sd
            JOIN sales s ON s.id = sd.sale_id
            JOIN products p ON p.id = sd.product_id
            WHERE s.date >= ? AND s.date <= ? AND s.status != 'cancelled'
            GROUP BY p.id
            ORDER BY total_amount DESC
        ";

        $items = $this->db->query($sql, [$dateFrom, $dateTo])->getResultArray();

        $totalProfit = 0;
        foreach ($items as $item) {
            $totalProfit += ($item['total_amount'] - $item['total_cost']);
        }

        $data = [
            'title' => 'Ganancia por Artículo',
            'items' => $items,
            'totalProfit' => $totalProfit,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ];

        return view('reports/profit_by_item', $data);
    }
}
