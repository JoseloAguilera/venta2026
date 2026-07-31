<?php

namespace App\Controllers;

use App\Models\CashSessionModel;
use App\Models\AccountModel;
use App\Models\TransactionModel;

class CashSessions extends BaseController
{
    protected $cashSessionModel;
    protected $accountModel;
    protected $transactionModel;
    protected $session;

    public function __construct()
    {
        $this->cashSessionModel = new CashSessionModel();
        $this->accountModel = new AccountModel();
        $this->transactionModel = new TransactionModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        $userId = $this->session->get('id') ?? $this->session->get('user_id');
        $activeSession = $this->cashSessionModel->getActiveSessionForUser($userId);

        $data = [
            'title' => 'Control de Sesiones de Caja',
            'sessions' => $this->cashSessionModel->getSessionsList(),
            'activeSession' => $activeSession
        ];

        return view('cash_sessions/index', $data);
    }

    public function open()
    {
        $userId = $this->session->get('id') ?? $this->session->get('user_id');
        $activeSession = $this->cashSessionModel->getActiveSessionForUser($userId);

        if ($activeSession) {
            return redirect()->to('/cash-sessions')->with('error', 'Ya tienes una sesión de caja abierta.');
        }

        // Solo permitir cuentas de tipo efectivo para sesiones de caja física
        $cashAccounts = $this->accountModel->where('status', 1)->where('type', 'cash')->findAll();
        if (empty($cashAccounts)) {
            // Si no hay cuentas clasificadas como 'cash', traer todas las activas
            $cashAccounts = $this->accountModel->where('status', 1)->findAll();
        }

        $data = [
            'title' => 'Apertura de Caja',
            'accounts' => $cashAccounts
        ];

        return view('cash_sessions/open', $data);
    }

    public function storeOpen()
    {
        $userId = $this->session->get('id') ?? $this->session->get('user_id');
        $activeSession = $this->cashSessionModel->getActiveSessionForUser($userId);

        if ($activeSession) {
            return redirect()->to('/cash-sessions')->with('error', 'Ya tienes una sesión de caja abierta.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'account_id' => 'required|numeric',
            'opening_amount' => 'required|numeric|greater_than_equal_to[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $accountId = $this->request->getPost('account_id');
        $openingAmount = (float)$this->request->getPost('opening_amount');

        // Verificar si la caja ya está abierta por otro usuario
        $existingAccountSession = $this->cashSessionModel->getActiveSessionByAccount($accountId);
        if ($existingAccountSession) {
            return redirect()->back()->withInput()->with('error', 'La cuenta seleccionada ya tiene una caja abierta en este momento.');
        }

        $sessionData = [
            'account_id' => $accountId,
            'user_id' => $userId,
            'opening_time' => date('Y-m-d H:i:s'),
            'opening_amount' => $openingAmount,
            'status' => 'open'
        ];

        if ($this->cashSessionModel->insert($sessionData)) {
            $sessionId = $this->cashSessionModel->getInsertID();
            log_activity('Caja', 'Apertura de Caja', "Caja abierta (Turno #$sessionId) con monto inicial de $" . number_format($openingAmount, 2));
            return redirect()->to('/cash-sessions')->with('success', 'Caja abierta correctamente con un monto inicial de $' . number_format($openingAmount, 2));
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al abrir la sesión de caja.');
        }
    }

    public function close($id)
    {
        $cashSession = $this->cashSessionModel->getSessionWithDetails($id);

        if (!$cashSession) {
            return redirect()->to('/cash-sessions')->with('error', 'Sesión de caja no encontrada.');
        }

        if ($cashSession['status'] === 'closed') {
            return redirect()->to('/cash-sessions/view/' . $id)->with('error', 'Esta sesión de caja ya se encuentra cerrada.');
        }

        $summary = $this->cashSessionModel->getSessionSummary($id);
        $expectedAmount = (float)$cashSession['opening_amount'] + $summary['net_movement'];

        $data = [
            'title' => 'Cierre y Arqueo de Caja',
            'session' => $cashSession,
            'summary' => $summary,
            'expected_amount' => $expectedAmount
        ];

        return view('cash_sessions/close', $data);
    }

    public function storeClose($id)
    {
        $cashSession = $this->cashSessionModel->find($id);

        if (!$cashSession || $cashSession['status'] === 'closed') {
            return redirect()->to('/cash-sessions')->with('error', 'Sesión de caja no válida o ya cerrada.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'closing_amount' => 'required|numeric|greater_than_equal_to[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $summary = $this->cashSessionModel->getSessionSummary($id);
        $expectedAmount = (float)$cashSession['opening_amount'] + $summary['net_movement'];
        $closingAmount = (float)$this->request->getPost('closing_amount');
        $discrepancy = $closingAmount - $expectedAmount;

        $updateData = [
            'closing_time' => date('Y-m-d H:i:s'),
            'closing_amount' => $closingAmount,
            'discrepancy' => $discrepancy,
            'status' => 'closed'
        ];

        if ($this->cashSessionModel->update($id, $updateData)) {
            $discLabel = ($discrepancy == 0) ? 'Exacto' : (($discrepancy > 0) ? "Sobrante +$" . number_format($discrepancy, 2) : "Faltante -$" . number_format(abs($discrepancy), 2));
            log_activity('Caja', 'Cierre de Caja', "Caja cerrada (Turno #$id). Esperado: $" . number_format($expectedAmount, 2) . " | Real: $" . number_format($closingAmount, 2) . " | Diferencia: $discLabel");
            return redirect()->to('/cash-sessions/view/' . $id)->with('success', 'Sesión de caja cerrada correctamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al cerrar la sesión de caja.');
        }
    }

    public function view($id)
    {
        $cashSession = $this->cashSessionModel->getSessionWithDetails($id);

        if (!$cashSession) {
            return redirect()->to('/cash-sessions')->with('error', 'Sesión de caja no encontrada.');
        }

        $summary = $this->cashSessionModel->getSessionSummary($id);
        $expectedAmount = (float)$cashSession['opening_amount'] + $summary['net_movement'];

        // Obtener todas las transacciones asociadas a la sesión
        $transactions = $this->transactionModel->where('session_id', $id)
                            ->orderBy('id', 'ASC')
                            ->findAll();

        $data = [
            'title' => 'Detalle de Sesión de Caja #' . $id,
            'session' => $cashSession,
            'summary' => $summary,
            'expected_amount' => $expectedAmount,
            'transactions' => $transactions
        ];

        return view('cash_sessions/view', $data);
    }

    public function ticket($id)
    {
        $cashSession = $this->cashSessionModel->getSessionWithDetails($id);

        if (!$cashSession) {
            return "Sesión de caja no encontrada";
        }

        $summary = $this->cashSessionModel->getSessionSummary($id);
        $expectedAmount = (float)$cashSession['opening_amount'] + $summary['net_movement'];
        $settings = model('SettingsModel')->getAllSettings();

        $data = [
            'session' => $cashSession,
            'summary' => $summary,
            'expected_amount' => $expectedAmount,
            'settings' => $settings
        ];

        return view('cash_sessions/ticket', $data);
    }

    public function audit()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $userId = $this->request->getGet('user_id');

        $sessions = $this->cashSessionModel->getSessionsList($startDate, $endDate, $userId);

        $closedSessions = array_filter($sessions, function($s) {
            return $s['status'] === 'closed';
        });

        $totalSessions = count($closedSessions);
        $totalDiscrepancy = 0;
        $totalOverages = 0;
        $totalShortages = 0;

        foreach ($closedSessions as $s) {
            $disc = (float)($s['discrepancy'] ?? 0);
            $totalDiscrepancy += $disc;
            if ($disc > 0) {
                $totalOverages += $disc;
            } elseif ($disc < 0) {
                $totalShortages += abs($disc);
            }
        }

        $userModel = new \App\Models\UserModel();
        $users = $userModel->findAll();

        $data = [
            'title' => 'Auditoría de Diferencias en Arqueos de Caja',
            'sessions' => $closedSessions,
            'users' => $users,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'selected_user' => $userId,
            'metrics' => [
                'total_sessions' => $totalSessions,
                'total_overages' => $totalOverages,
                'total_shortages' => $totalShortages,
                'net_discrepancy' => $totalDiscrepancy
            ]
        ];

        return view('cash_sessions/audit', $data);
    }
}
