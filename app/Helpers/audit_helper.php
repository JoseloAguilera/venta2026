<?php

use App\Models\AuditLogModel;

if (!function_exists('log_activity')) {
    /**
     * Registra una acción del usuario o sistema en la bitácora audit_logs
     */
    function log_activity($module, $action, $description, $oldValues = null, $newValues = null)
    {
        try {
            $session = session();
            $userId = $session ? ($session->get('id') ?? $session->get('user_id')) : null;
            $username = $session ? $session->get('username') : 'System';
            $request = \Config\Services::request();
            $ipAddress = $request ? $request->getIPAddress() : '127.0.0.1';

            $auditModel = new AuditLogModel();
            $auditModel->insert([
                'user_id'     => $userId,
                'username'    => $username,
                'module'      => $module,
                'action'      => $action,
                'description' => $description,
                'ip_address'  => $ipAddress,
                'old_values'  => is_array($oldValues) ? json_encode($oldValues) : $oldValues,
                'new_values'  => is_array($newValues) ? json_encode($newValues) : $newValues,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'AuditLog Error: ' . $e->getMessage());
        }
    }
}
