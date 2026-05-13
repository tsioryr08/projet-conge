<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\EmployeModel;

class Auth extends BaseController
{
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

    // public function attempt()
    // {
    //     if ($this->request->getMethod() !== 'post') {
    //         return redirect()->to('/login');
    //     }

    //     $email = $this->request->getPost('email');
    //     $password = $this->request->getPost('password');
        
    // die("email reçu: " . $email . " | password reçu: " . $password);

    //     $user = $this->employeModel->where('email', $email)->first();

    //     if (!$user) {
    //         session()->setFlashdata('error', 'Identifiants invalides.');
    //         return redirect()->to('/login');
    //     }

    //     if (!password_verify($password, $user['password'])) {
    //         session()->setFlashdata('error', 'Identifiants invalides.');
    //         return redirect()->to('/login');
    //     }

    //     if ((int) $user['actif'] !== 1) {
    //         session()->setFlashdata('error', 'Compte désactivé.');
    //         return redirect()->to('/login');
    //     }

    //     // set session
    //     $data = [
    //         'employe_id' => $user['id'],
    //         'email' => $user['email'],
    //         'nom' => $user['nom'],
    //         'prenom' => $user['prenom'],
    //         'role' => $user['role'],
    //         'isLoggedIn' => true,
    //     ];

    //     session()->set($data);

    //     // redirect based on role; default to employe dashboard
    //     if ($user['role'] === 'admin') {
    //         return redirect()->to('/admin');
    //     }
    //     if ($user['role'] === 'rh') {
    //         return redirect()->to('/rh');
    //     }

    //     // return redirect()->to('/employe/');
    //     return redirect()->to(site_url('employe'));
    // }

    public function attempt()
{
    $email    = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    // TEST BRUTAL - on bypass tout
    session()->set([
        'employe_id' => 15,
        'email'      => 'marie@techmada.mg',
        'nom'        => 'Rasoa',
        'prenom'     => 'Marie',
        'role'       => 'employe',
        'isLoggedIn' => true,
    ]);

    return redirect()->to('/employe/');
}

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
