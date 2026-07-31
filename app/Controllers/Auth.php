<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LoginAttemptModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;
    protected $loginAttemptModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loginAttemptModel = new LoginAttemptModel();
        $this->session = session();
        helper(['form', 'url', 'audit']);
    }

    /**
     * Show login form
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Process login with Brute Force protection & Audit Logging
     */
    public function attemptLogin()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $ipAddress = $this->request->getIPAddress();

        // 1. Verificación contra Ataques de Fuerza Bruta
        if ($this->loginAttemptModel->isLockedOut($ipAddress, $username, 5, 15)) {
            log_activity('Seguridad', 'Bloqueo Fuerza Bruta', "IP $ipAddress bloqueada temporalmente por excesivos intentos fallidos para el usuario '$username'");
            return redirect()->back()->withInput()->with('error', '⚠️ Acceso bloqueado por seguridad tras 5 intentos fallidos. Intente nuevamente en 15 minutos.');
        }

        $user = $this->userModel->verifyCredentials($username, $password);

        if ($user) {
            // Resetear contador de intentos fallidos
            $this->loginAttemptModel->resetAttempts($ipAddress, $username);

            // Get role information
            $roleId = $user['role_id'] ?? null;
            $roleName = null;
            $permissions = [];

            if ($roleId) {
                $roleModel = new \App\Models\RoleModel();
                $role = $roleModel->find($roleId);
                $roleName = $role['name'] ?? null;
                $permissions = $this->userModel->getRolePermissions($user['id']);
            }

            // Set session data
            $sessionData = [
                'id'          => $user['id'],
                'username'    => $user['username'],
                'email'       => $user['email'],
                'role_id'     => $roleId,
                'role_name'   => $roleName,
                'permissions' => $permissions,
                'isLoggedIn'  => true
            ];
            
            $this->session->set($sessionData);
            
            // Log de auditoría de inicio de sesión exitoso
            log_activity('Seguridad', 'Login Exitoso', "El usuario '{$user['username']}' inició sesión correctamente.");

            return redirect()->to('/dashboard')->with('success', 'Bienvenido, ' . $user['username']);
        } else {
            // Registrar intento fallido
            $attempts = $this->loginAttemptModel->recordFailedAttempt($ipAddress, $username);
            log_activity('Seguridad', 'Login Fallido', "Intento fallido #$attempts de inicio de sesión para el usuario '$username'");

            $remaining = 5 - $attempts;
            $errorMsg = ($remaining > 0)
                ? "Credenciales incorrectas. Te quedan $remaining intentos antes del bloqueo."
                : "Credenciales incorrectas. Se ha bloqueado el acceso por 15 minutos.";

            return redirect()->back()->withInput()->with('error', $errorMsg);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        if ($this->session->get('isLoggedIn')) {
            log_activity('Seguridad', 'Cierre de Sesión', "El usuario '{$this->session->get('username')}' cerró sesión.");
        }
        $this->session->destroy();
        return redirect()->to('/auth/login')->with('success', 'Sesión cerrada correctamente');
    }

    /**
     * Endpoint AJAX para verificar el PIN de Supervisor
     */
    public function verifySupervisorPin()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Petición inválida']);
        }

        $pin = $this->request->getPost('pin');
        $action = $this->request->getPost('action'); // e.g. 'sale_annul', 'min_price', 'collection_annul'

        if (empty($pin)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Por favor ingrese el PIN.']);
        }

        $settingsModel = new \App\Models\SettingsModel();
        $systemPin = $settingsModel->getValue('supervisor_pin', '1234');

        $actionPinKey = null;
        if ($action === 'sale_annul') {
            $actionPinKey = 'pin_sale_annul';
        } elseif ($action === 'min_price') {
            $actionPinKey = 'pin_min_price';
        } elseif ($action === 'collection_annul') {
            $actionPinKey = 'pin_collection_annul';
        }

        $specificPin = $actionPinKey ? $settingsModel->getValue($actionPinKey, null) : null;

        $pinValid = false;
        $supervisorName = '';

        // 1. Validar contra el PIN exclusivo de la acción (si está configurado)
        if (!empty($specificPin) && $pin === $specificPin) {
            $pinValid = true;
            $supervisorName = 'PIN Exclusivo de Acción (' . $action . ')';
        }

        // 2. Validar contra el PIN General configurado en Configuración del Sistema
        if (!$pinValid && $pin === $systemPin) {
            $pinValid = true;
            $supervisorName = 'Sistema / Configuración General';
        }

        // 3. Validar contra los PINs de usuarios supervisores
        if (!$pinValid) {
            $supervisors = $this->userModel->where('active', 1)->findAll();
            foreach ($supervisors as $sup) {
                $supPin = $sup['supervisor_pin'] ?? null;
                if (!empty($supPin) && ($pin === $supPin || password_verify($pin, $supPin))) {
                    $pinValid = true;
                    $supervisorName = $sup['username'];
                    break;
                }
            }
        }

        if ($pinValid) {
            log_activity('Seguridad', 'PIN Supervisor Validador', "Autorización por PIN aprobada ($supervisorName)");
            return $this->response->setJSON(['success' => true, 'message' => 'PIN de supervisor válido.']);
        } else {
            log_activity('Seguridad', 'PIN Supervisor Rechazado', "Intento de PIN de supervisor rechazado");
            return $this->response->setJSON(['success' => false, 'message' => 'PIN de supervisor incorrecto.']);
        }
    }

    /**
     * Endpoint AJAX para desbloquear pantalla por inactividad
     */
    public function unlockScreen()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Petición inválida']);
        }

        $password = $this->request->getPost('password');
        $username = $this->session->get('username');

        if (empty($username) || empty($password)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debe ingresar la contraseña']);
        }

        $user = $this->userModel->verifyCredentials($username, $password);

        if ($user) {
            log_activity('Seguridad', 'Desbloqueo de Pantalla', "Pantalla desbloqueada correctamente por '{$user['username']}'");
            return $this->response->setJSON(['success' => true, 'message' => 'Pantalla desbloqueada']);
        } else {
            log_activity('Seguridad', 'Desbloqueo Fallido', "Intento fallido de desbloqueo de pantalla para '$username'");
            return $this->response->setJSON(['success' => false, 'message' => 'Contraseña incorrecta']);
        }
    }

    /**
     * Show register form (only for admin)
     */
    public function register()
    {
        helper('permission');
        if (!$this->session->get('isLoggedIn') || !is_admin()) {
            return redirect()->to('/dashboard')->with('error', 'No tiene permisos para registrar usuarios');
        }

        return view('auth/register');
    }

    /**
     * Process registration
     */
    public function attemptRegister()
    {
        helper('permission');
        if (!$this->session->get('isLoggedIn') || !is_admin()) {
            return redirect()->to('/dashboard')->with('error', 'No tiene permisos para registrar usuarios');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role_id'  => 'required|is_natural_no_zero'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username'       => $this->request->getPost('username'),
            'email'          => $this->request->getPost('email'),
            'password'       => $this->request->getPost('password'),
            'supervisor_pin' => $this->request->getPost('supervisor_pin') ?? '1234',
            'role_id'        => $this->request->getPost('role_id')
        ];

        if ($this->userModel->insert($data)) {
            log_activity('Usuarios', 'Crear Usuario', "Nuevo usuario creado: '{$data['username']}'");
            return redirect()->to('/dashboard')->with('success', 'Usuario registrado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }
}
