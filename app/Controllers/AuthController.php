<?php

namespace App\Controllers;

use App\Models\EmployeModel;

class AuthController extends BaseController
{
    public function login()
    {
        $user = $this->currentUser();
        if ($user !== null) {
            return redirect()->to(base_url($this->dashboardPathForRole($user['role'] ?? null)));
        }

        return view('auth/login', [
            'title' => 'Connexion',
        ]);
    }

    public function authenticate()
    {
        if (! $this->validate([
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Identifiants invalides.');
        }

        $email    = strtolower((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $employeModel = new EmployeModel();
        $user = $employeModel->findByEmail($email);

        if (
            $user === null ||
            (int) ($user['actif'] ?? 0) !== 1 ||
            ! password_verify($password, (string) $user['password'])
        ) {
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect.');
        }

        $session = service('session');
        $session->regenerate(true);
        $session->set('user', [
            'id'             => (int) $user['id'],
            'nom'            => $user['nom'],
            'prenom'         => $user['prenom'],
            'email'          => $user['email'],
            'role'           => $user['role'],
            'departement_id' => $user['departement_id'],
        ]);

        return redirect()->to(base_url($this->dashboardPathForRole($user['role'])));
    }

    public function logout()
    {
        service('session')->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Déconnexion réussie.');
    }
}