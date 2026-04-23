<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\TransactionModel;

class Accounts extends BaseController
{
    protected $accountModel;
    protected $transactionModel;
    protected $session;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->transactionModel = new TransactionModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check view permission. If not implemented, just wrap or skip.
        // require_permission('accounts', 'view');

        $data = [
            'title' => 'Cuentas y Cajas',
            'accounts' => $this->accountModel->findAll()
        ];

        return view('accounts/index', $data);
    }

    public function create()
    {
        // require_permission('accounts', 'insert');

        $data = ['title' => 'Nueva Cuenta'];
        return view('accounts/create', $data);
    }

    public function store()
    {
        // require_permission('accounts', 'insert');

        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'type' => 'required|in_list[cash,bank,digital_wallet]',
            'initial_balance' => 'numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $initialBalance = $this->request->getPost('initial_balance') ?: 0;

        $data = [
            'name' => $this->request->getPost('name'),
            'type' => $this->request->getPost('type'),
            'balance' => $initialBalance,
            'status' => $this->request->getPost('status') ?? 1
        ];

        if ($this->accountModel->insert($data)) {
            $accountId = $this->accountModel->getInsertID();

            if ($initialBalance > 0) {
                // Register initial balance transaction
                $this->transactionModel->insert([
                    'account_id' => $accountId,
                    'type' => 'income',
                    'amount' => $initialBalance,
                    'reference_type' => 'initial_balance',
                    'description' => 'Saldo Inicial',
                    'user_id' => session()->get('user_id'),
                    'date' => date('Y-m-d H:i:s')
                ]);
            }

            return redirect()->to('/accounts')->with('success', 'Cuenta creada correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->accountModel->errors());
        }
    }

    public function edit($id)
    {
        // require_permission('accounts', 'update');

        $account = $this->accountModel->find($id);

        if (!$account) {
            return redirect()->to('/accounts')->with('error', 'Cuenta no encontrada');
        }

        $data = [
            'title' => 'Editar Cuenta',
            'account' => $account
        ];

        return view('accounts/edit', $data);
    }

    public function update($id)
    {
        // require_permission('accounts', 'update');

        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'type' => 'required|in_list[cash,bank,digital_wallet]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status') ?? 1
        ];

        if ($this->accountModel->update($id, $data)) {
            return redirect()->to('/accounts')->with('success', 'Cuenta actualizada correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->accountModel->errors());
        }
    }

    public function delete($id)
    {
        // require_permission('accounts', 'delete');

        if ($this->transactionModel->where('account_id', $id)->countAllResults() > 0) {
            return redirect()->to('/accounts')->with('error', 'No se puede eliminar la cuenta porque ya tiene movimientos. Desactívela en su lugar.');
        }

        if ($this->accountModel->delete($id)) {
            return redirect()->to('/accounts')->with('success', 'Cuenta eliminada correctamente');
        } else {
            return redirect()->to('/accounts')->with('error', 'Error al eliminar cuenta');
        }
    }

    // This is the Arqueo/Extracto view
    public function statement($id)
    {
        $account = $this->accountModel->find($id);
        if (!$account) {
            return redirect()->to('/accounts')->with('error', 'Cuenta no encontrada');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $transactions = $this->transactionModel->getMovementsByAccountAndDate($id, $startDate, $endDate);

        $filteredBalance = 0;
        foreach ($transactions as $tx) {
            if (in_array($tx['type'], ['income', 'transfer_in'])) {
                $filteredBalance += $tx['amount'];
            } else {
                $filteredBalance -= $tx['amount'];
            }
        }

        $data = [
            'title' => 'Extracto de Cuenta - ' . $account['name'],
            'account' => $account,
            'transactions' => $transactions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filtered_balance' => $filteredBalance
        ];

        return view('accounts/statement', $data);
    }

    public function storeTransaction($id)
    {
        // require_permission('accounts', 'insert');

        $account = $this->accountModel->find($id);
        if (!$account) {
            return redirect()->to('/accounts')->with('error', 'Cuenta no encontrada');
        }

        $validation = \Config\Services::validation();

        $validation->setRules([
            'type' => 'required|in_list[income,expense]',
            'amount' => 'required|numeric|greater_than[0]',
            'description' => 'required|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $type = $this->request->getPost('type');
        $amount = $this->request->getPost('amount');
        $description = $this->request->getPost('description');

        $this->transactionModel->insert([
            'account_id' => $id,
            'type' => $type,
            'amount' => $amount,
            'reference_type' => 'manual',
            'description' => $description,
            'user_id' => session()->get('user_id'),
            'date' => date('Y-m-d H:i:s')
        ]);

        if ($type === 'income') {
            $this->accountModel->increaseBalance($id, $amount);
        } else {
            $this->accountModel->decreaseBalance($id, $amount);
        }

        return redirect()->back()->with('success', 'Movimiento registrado correctamente');
    }
}
