<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\EmployeModel;

class Auth extends BaseController
{
    private const PATH_LOGIN = '/login';
    private const PATH_EMPLOYE_DASHBOARD = '/employe/';

    protected $employeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
        helper('form');
    }

    public function login()
    {
        // show login form
        return view('employe/login');
    }

    public function attempt()
    {
        $response = redirect()->to(self::PATH_LOGIN);

        if ($this->request->is('post')) {
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');

            $user = $this->employeModel->where('email', $email)->first();

            if (! $user || ! password_verify($password, $user['password'])) {
                session()->setFlashdata('error', 'Identifiants invalides.');
            } elseif ((int) ($user['actif'] ?? 0) !== 1) {
                session()->setFlashdata('error', 'Compte désactivé.');
            } else {
                session()->set([
                    'employe_id' => (int) $user['id'],
                    'email' => $user['email'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'role' => $user['role'],
                    'isLoggedIn' => true,
                ]);

                if ($user['role'] === 'admin') {
                    $response = redirect()->to('/admin');
                } elseif ($user['role'] === 'rh') {
                    $response = redirect()->to('/rh');
                } else {
                    $response = redirect()->to(self::PATH_EMPLOYE_DASHBOARD);
                }
            }
        }

        return $response;
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(self::PATH_LOGIN);
    }
}
