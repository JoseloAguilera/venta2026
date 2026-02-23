<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $session;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check view permission
        require_permission('products', 'view');

        $data = [
            'title' => 'Productos',
            'products' => $this->productModel->getProductsWithCategory()
        ];

        return view('products/index', $data);
    }

    public function create()
    {
        // Check insert permission
        require_permission('products', 'insert');

        $data = [
            'title' => 'Nuevo Producto',
            'categories' => $this->categoryModel->findAll()
        ];

        return view('products/create', $data);
    }

    public function view($id)
    {
        // Check view permission
        require_permission('products', 'view');

        $product = $this->productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->find($id);

        if (!$product) {
            return redirect()->to('/products')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title' => 'Detalles del Producto: ' . $product['name'],
            'product' => $product
        ];

        return view('products/view', $data);
    }

    public function store()
    {
        // Check insert permission
        require_permission('products', 'insert');

        $validation = \Config\Services::validation();

        $validation->setRules([
            'category_id' => 'required|is_natural_no_zero',
            'code' => 'required|max_length[50]|is_unique[products.code]',
            'name' => 'required|min_length[3]|max_length[200]',
            'cost_price' => 'required|decimal',
            'price' => 'required|decimal',
            'min_sale_price' => 'required|decimal',
            'stock' => 'required|integer',
            'description' => 'permit_empty|max_length[500]',
            'imei1' => 'permit_empty|max_length[50]|is_unique[products.imei1]',
            'imei2' => 'permit_empty|max_length[50]|is_unique[products.imei2]'
        ], [
            'category_id' => [
                'required' => 'La categoría es requerida',
                'is_natural_no_zero' => 'Seleccione una categoría válida'
            ],
            'code' => [
                'required' => 'El código es requerido',
                'is_unique' => 'Este código ya está en uso',
                'max_length' => 'El código no puede exceder los 50 caracteres'
            ],
            'name' => [
                'required' => 'El nombre del producto es requerido',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder los 200 caracteres'
            ],
            'cost_price' => [
                'required' => 'El precio de costo es requerido',
                'decimal' => 'El precio de costo debe ser un valor numérico decimal'
            ],
            'price' => [
                'required' => 'El precio de venta es requerido',
                'decimal' => 'El precio de venta debe ser un valor numérico decimal'
            ],
            'min_sale_price' => [
                'required' => 'El precio mínimo de venta es requerido',
                'decimal' => 'El precio mínimo debe ser un valor numérico decimal'
            ],
            'stock' => [
                'required' => 'El stock inicial es requerido',
                'integer' => 'El stock debe ser un número entero'
            ],
            'imei1' => [
                'is_unique' => 'Este IMEI 1 ya está registrado',
                'max_length' => 'El IMEI 1 no puede exceder los 50 caracteres'
            ],
            'imei2' => [
                'is_unique' => 'Este IMEI 2 ya está registrado',
                'max_length' => 'El IMEI 2 no puede exceder los 50 caracteres'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'cost_price' => $this->request->getPost('cost_price'),
            'price' => $this->request->getPost('price'),
            'min_sale_price' => $this->request->getPost('min_sale_price'),
            'stock' => $this->request->getPost('stock'),
            'imei1' => $this->request->getPost('imei1'),
            'imei2' => $this->request->getPost('imei2')
        ];

        // Initialize stock as 0 for the product record itself, we'll adds it via updateStock to ensure consistency
        $initialStock = $this->request->getPost('stock');
        $data['stock'] = 0;

        if ($productId = $this->productModel->insert($data)) {
            // Assign initial stock to Default Warehouse (ID 1)
            // Ideally we should adhere to a default warehouse setting or let user choose, 
            // but for now ID 1 (Depósito Central) is the safe default.
            if ($initialStock > 0) {
                // Determine ID using model or hardcoded for now since SQL script created ID 1
                $warehouseModel = new \App\Models\WarehouseModel();
                $defaultWarehouse = $warehouseModel->where('name', 'Depósito Central')->first();
                $warehouseId = $defaultWarehouse['id'] ?? 1;

                $this->productModel->updateStock($productId, $initialStock, 'add', $warehouseId);
            }

            return redirect()->to('/products')->with('success', 'Producto creado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        }
    }

    public function edit($id)
    {
        // Check update permission
        require_permission('products', 'update');

        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/products')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title' => 'Editar Producto',
            'product' => $product,
            'categories' => $this->categoryModel->findAll()
        ];

        return view('products/edit', $data);
    }

    public function update($id)
    {
        // Check update permission
        require_permission('products', 'update');

        $validation = \Config\Services::validation();

        $validation->setRules([
            'category_id' => 'required|is_natural_no_zero',
            'code' => "required|max_length[50]|is_unique[products.code,id,{$id}]",
            'name' => 'required|min_length[3]|max_length[200]',
            'cost_price' => 'required|decimal',
            'price' => 'required|decimal',
            'min_sale_price' => 'required|decimal',
            'stock' => 'required|integer',
            'description' => 'permit_empty|max_length[500]',
            'imei1' => "permit_empty|max_length[50]|is_unique[products.imei1,id,{$id}]",
            'imei2' => "permit_empty|max_length[50]|is_unique[products.imei2,id,{$id}]"
        ], [
            'category_id' => [
                'required' => 'La categoría es requerida',
                'is_natural_no_zero' => 'Seleccione una categoría válida'
            ],
            'code' => [
                'required' => 'El código es requerido',
                'is_unique' => 'Este código ya está en uso',
                'max_length' => 'El código no puede exceder los 50 caracteres'
            ],
            'name' => [
                'required' => 'El nombre del producto es requerido',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder los 200 caracteres'
            ],
            'cost_price' => [
                'required' => 'El precio de costo es requerido',
                'decimal' => 'El precio de costo debe ser un valor numérico decimal'
            ],
            'price' => [
                'required' => 'El precio de venta es requerido',
                'decimal' => 'El precio de venta debe ser un valor numérico decimal'
            ],
            'min_sale_price' => [
                'required' => 'El precio mínimo de venta es requerido',
                'decimal' => 'El precio mínimo debe ser un valor numérico decimal'
            ],
            'stock' => [
                'required' => 'El stock es requerido',
                'integer' => 'El stock debe ser un número entero'
            ],
            'imei1' => [
                'is_unique' => 'Este IMEI 1 ya está registrado',
                'max_length' => 'El IMEI 1 no puede exceder los 50 caracteres'
            ],
            'imei2' => [
                'is_unique' => 'Este IMEI 2 ya está registrado',
                'max_length' => 'El IMEI 2 no puede exceder los 50 caracteres'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'cost_price' => $this->request->getPost('cost_price'),
            'price' => $this->request->getPost('price'),
            'min_sale_price' => $this->request->getPost('min_sale_price'),
            'stock' => $this->request->getPost('stock'),
            'imei1' => $this->request->getPost('imei1'),
            'imei2' => $this->request->getPost('imei2')
        ];

        if ($this->productModel->skipValidation(true)->update($id, $data)) {
            return redirect()->to('/products')->with('success', 'Producto actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        }
    }

    public function delete($id)
    {
        // Check delete permission
        require_permission('products', 'delete');

        $product = $this->productModel->find($id);

        if ($product) {
            // Rename code to allow reuse
            $newCode = $product['code'] . '_del_' . time();
            $this->productModel->skipValidation(true)->update($id, ['code' => $newCode]);

            if ($this->productModel->delete($id)) {
                return redirect()->to('/products')->with('success', 'Producto eliminado correctamente');
            }
        }

        return redirect()->to('/products')->with('error', 'No se pudo eliminar el producto');
    }
}
