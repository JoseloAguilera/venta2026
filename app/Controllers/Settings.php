<?php

namespace App\Controllers;

use App\Models\SettingsModel;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $session;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        helper('permission');
        // Solo admin puede acceder
        if (!is_admin()) {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $data = [
            'title' => 'Configuración del Sistema',
            'settings' => $this->settingsModel->getAllSettings()
        ];

        return view('settings/index', $data);
    }

    public function update()
    {
        helper('permission');
        // Solo admin puede actualizar
        if (!is_admin()) {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'company_name' => 'required|min_length[3]|max_length[100]',
            'company_ruc' => 'required|max_length[20]',
            'company_address' => 'required|max_length[200]',
            'company_email' => 'required|valid_email',
            'min_price_password' => 'required|min_length[4]',
            'currency' => 'required|in_list[USD,PYG]'
        ];

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Actualizar configuraciones
        $settings = [
            'company_name',
            'company_ruc',
            'company_address',
            'company_email',
            'company_phone',
            'min_price_password',
            'supervisor_pin',
            'pin_sale_annul',
            'pin_min_price',
            'pin_collection_annul',
            'currency',
            'autolock_enabled',
            'autolock_minutes'
        ];

        foreach ($settings as $key) {
            $value = $this->request->getPost($key);
            if ($value !== null) {
                if ($key === 'autolock_enabled' && $value === null) {
                    $value = '0';
                }
                $this->settingsModel->setValue($key, $value);
            }
        }

        return redirect()->to('/settings')->with('success', 'Configuración actualizada correctamente');
    }

    public function changeKey()
    {
        helper('permission');
        if (!is_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }

        $key = $this->request->getPost('key');
        $newPin = $this->request->getPost('new_pin');
        $confirmPin = $this->request->getPost('confirm_pin');

        $allowedKeys = ['supervisor_pin', 'min_price_password', 'pin_sale_annul', 'pin_min_price', 'pin_collection_annul'];

        if (!in_array($key, $allowedKeys)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Clave no válida']);
        }

        if (empty($newPin) || strlen($newPin) < 4) {
            return $this->response->setJSON(['success' => false, 'message' => 'La nueva clave debe tener al menos 4 caracteres.']);
        }

        if ($newPin !== $confirmPin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Las claves no coinciden. Por favor vuelva a intentar.']);
        }

        $this->settingsModel->setValue($key, $newPin);
        log_activity('Seguridad', 'Cambio de Clave', "Se actualizó la clave de seguridad para '$key'");

        return $this->response->setJSON(['success' => true, 'message' => 'Clave actualizada correctamente']);
    }
}
