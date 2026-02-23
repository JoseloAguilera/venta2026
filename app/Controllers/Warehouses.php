<?php

namespace App\Controllers;

use App\Models\WarehouseModel;

class Warehouses extends BaseController
{
    protected $warehouseModel;
    protected $session;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check view permission
        require_permission('warehouses', 'view');

        $data = [
            'title' => 'Depósitos',
            'warehouses' => $this->warehouseModel->findAll()
        ];

        return view('warehouses/index', $data);
    }

    public function create()
    {
        // Check insert permission
        require_permission('warehouses', 'insert');

        $data = [
            'title' => 'Nuevo Depósito'
        ];

        return view('warehouses/create', $data);
    }

    public function store()
    {
        // Check insert permission
        require_permission('warehouses', 'insert');

        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'address' => 'permit_empty|max_length[255]',
            'description' => 'permit_empty|max_length[500]'
        ], [
            'name' => [
                'required' => 'El nombre es requerido',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder los 100 caracteres'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->warehouseModel->insert($data)) {
            return redirect()->to('/warehouses')->with('success', 'Depósito creado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->warehouseModel->errors());
        }
    }

    public function edit($id)
    {
        // Check update permission
        require_permission('warehouses', 'update');

        $warehouse = $this->warehouseModel->find($id);

        if (!$warehouse) {
            return redirect()->to('/warehouses')->with('error', 'Depósito no encontrado');
        }

        $data = [
            'title' => 'Editar Depósito',
            'warehouse' => $warehouse
        ];

        return view('warehouses/edit', $data);
    }

    public function update($id)
    {
        // Check update permission
        require_permission('warehouses', 'update');

        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'address' => 'permit_empty|max_length[255]',
            'description' => 'permit_empty|max_length[500]'
        ], [
            'name' => [
                'required' => 'El nombre es requerido',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder los 100 caracteres'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->warehouseModel->update($id, $data)) {
            return redirect()->to('/warehouses')->with('success', 'Depósito actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->warehouseModel->errors());
        }
    }

    public function delete($id)
    {
        // Check delete permission
        require_permission('warehouses', 'delete');

        // Check availability (check if it has stock, etc. - for now simple delete)
        // Ideally we should check if there are stocks in this warehouse before deleting.
        // But since we are using Database constraints, it might fail if referenced in product_stock.
        // Let's wrap in try catch or check first.

        // For now, simple active/inactive toggle is better for warehouses to maintain history, 
        // but if user explicitly wants delete:

        $productStockModel = new \App\Models\ProductStockModel();
        $hasStock = $productStockModel->where('warehouse_id', $id)->where('quantity >', 0)->countAllResults();

        if ($hasStock > 0) {
            return redirect()->to('/warehouses')->with('error', 'No se puede eliminar el depósito porque tiene productos con stock.');
        }

        if ($this->warehouseModel->delete($id)) {
            return redirect()->to('/warehouses')->with('success', 'Depósito eliminado correctamente');
        } else {
            return redirect()->to('/warehouses')->with('error', 'No se pudo eliminar el depósito');
        }
    }
}
