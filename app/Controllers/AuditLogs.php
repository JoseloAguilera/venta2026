<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\UserModel;

class AuditLogs extends BaseController
{
    protected $auditLogModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
        $this->userModel = new UserModel();
        $this->session = session();
        helper(['form', 'url', 'permission', 'audit']);
    }

    public function index()
    {
        require_permission('settings', 'view');

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $module = $this->request->getGet('module');
        $userId = $this->request->getGet('user_id');

        $logs = $this->auditLogModel->getLogsWithFilters($startDate, $endDate, $module, $userId);
        $users = $this->userModel->findAll();

        $data = [
            'title' => 'Bitácora y Registro de Auditoría',
            'logs' => $logs,
            'users' => $users,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'selected_module' => $module,
            'selected_user' => $userId
        ];

        return view('audit_logs/index', $data);
    }
}
