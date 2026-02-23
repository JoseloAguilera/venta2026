<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }

    /**
     * Dashboard home
     */
    public function index()
    {
        $saleModel = new \App\Models\SaleModel();
        $productModel = new \App\Models\ProductModel();

        // --- Sales Stats ---
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Sales Today
        $salesToday = $saleModel->where('DATE(date)', $today)
            ->where('status !=', 'cancelled')
            ->selectSum('total')
            ->first()['total'] ?? 0;

        // Sales Yesterday
        $salesYesterday = $saleModel->where('DATE(date)', $yesterday)
            ->where('status !=', 'cancelled')
            ->selectSum('total')
            ->first()['total'] ?? 0;

        // Change Percentage
        $salesChange = 0;
        if ($salesYesterday > 0) {
            $salesChange = (($salesToday - $salesYesterday) / $salesYesterday) * 100;
        } elseif ($salesToday > 0) {
            $salesChange = 100; // Increase from 0
        }

        // --- Inventory Stats ---
        $productsCount = $productModel->countAllResults(); // Active products (soft deletes handled)

        // Low Stock
        $lowStockCount = $productModel->where('stock <=', 10)->countAllResults();

        // --- Financial Stats ---
        $receivables = $saleModel->getTotalReceivables();

        // Get user data
        $data = [
            'title' => 'Dashboard',
            'user' => [
                'username' => $this->session->get('username'),
                'role' => $this->session->get('role')
            ],
            'stats' => [
                'sales_today' => $salesToday,
                'sales_change' => $salesChange,
                'products_count' => $productsCount,
                'receivables' => $receivables,
                'low_stock_count' => $lowStockCount
            ]
        ];

        return view('dashboard/index', $data);
    }
}
