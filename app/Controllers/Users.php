<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check permission
        if (!can_view('users')) {
            return redirect()->to('/dashboard')->with('error', 'No tiene permisos para acceder a esta sección');
        }

        // Get users with role info
        $users = $this->userModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->findAll();

        $data = [
            'title' => 'Gestión de Usuarios',
            'users' => $users
        ];

        return view('users/index', $data);
    }

    public function create()
    {
        // Check permission
        if (!can_insert('users')) {
            return redirect()->to('/users')->with('error', 'No tiene permisos para crear usuarios');
        }

        $data = [
            'title' => 'Nuevo Usuario',
            'roles' => $this->roleModel->findAll()
        ];

        return view('users/create', $data);
    }

    public function store()
    {
        // Check permission
        if (!can_insert('users')) {
            return redirect()->to('/users')->with('error', 'No tiene permisos para crear usuarios');
        }

        $validation = \Config\Services::validation();

        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role_id' => 'required|is_natural_no_zero'
        ], [
            'username' => [
                'required' => 'El nombre de usuario es requerido',
                'min_length' => 'El nombre de usuario debe tener al menos 3 caracteres',
                'is_unique' => 'Este nombre de usuario ya está en uso'
            ],
            'email' => [
                'required' => 'El email es requerido',
                'valid_email' => 'Debe proporcionar un email válido',
                'is_unique' => 'Este email ya está registrado'
            ],
            'password' => [
                'required' => 'La contraseña es requerida',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres'
            ],
            'role_id' => [
                'required' => 'Debe seleccionar un rol'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Create user
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'), // UserModel hashes this automatically
            'role_id' => $this->request->getPost('role_id'),
            'active' => 1
        ];

        if ($this->userModel->insert($userData)) {
            return redirect()->to('/users')->with('success', 'Usuario creado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }

    public function edit($id)
    {
        // Check permission
        if (!can_update('users')) {
            return redirect()->to('/users')->with('error', 'No tiene permisos para editar usuarios');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'Usuario no encontrado');
        }

        $data = [
            'title' => 'Editar Usuario',
            'user' => $user,
            'roles' => $this->roleModel->findAll()
        ];

        return view('users/edit', $data);
    }

    public function update($id)
    {
        // Check permission
        if (!can_update('users')) {
            return redirect()->to('/users')->with('error', 'No tiene permisos para editar usuarios');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'Usuario no encontrado');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role_id' => 'required|is_natural_no_zero'
        ];

        // Password validation only if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        $validation->setRules($rules, [
            'username' => [
                'required' => 'El nombre de usuario es requerido',
                'min_length' => 'El nombre de usuario debe tener al menos 3 caracteres',
                'is_unique' => 'Este nombre de usuario ya está en uso'
            ],
            'email' => [
                'required' => 'El email es requerido',
                'valid_email' => 'Debe proporcionar un email válido',
                'is_unique' => 'Este email ya está registrado'
            ],
            'role_id' => [
                'required' => 'Debe seleccionar un rol'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Update user
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role_id' => $this->request->getPost('role_id'),
            'active' => $this->request->getPost('active') ? 1 : 0
        ];

        if (!empty($password)) {
            $userData['password'] = $password; // UserModel hashes this automatically
        }

        if ($this->userModel->update($id, $userData)) {
            return redirect()->to('/users')->with('success', 'Usuario actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }

    public function delete($id)
    {
        // Check permission
        if (!can_delete('users')) {
            return redirect()->to('/users')->with('error', 'No tiene permisos para eliminar usuarios');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'Usuario no encontrado');
        }

        // Prevent deleting own account
        if ($user['id'] == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'No puede eliminar su propia cuenta');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/users')->with('success', 'Usuario eliminado correctamente');
        } else {
            return redirect()->to('/users')->with('error', 'No se pudo eliminar el usuario');
        }
    }
}
