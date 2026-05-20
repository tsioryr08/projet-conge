<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\EmployeModel;

class Profil extends BaseController
{
    private EmployeModel $employeModel;

    public function __construct()
    {
        helper('form');
        $this->employeModel = new EmployeModel();
    }

    public function edit()
    {
        $user      = $this->currentUser();
        $employeId = (int) ($user['id'] ?? 0);

        $employe = $this->employeModel->find($employeId);
        if (! $employe) {
            return redirect()->to('/login')->with('error', 'Votre compte est introuvable.');
        }

        return view('employe/profile', [
            'title'        => 'Mon profil',
            'user'         => $user,
            'nom'          => $employe['nom']    ?? '',
            'prenom'       => $employe['prenom'] ?? '',
            'email'        => $employe['email']  ?? '',
            'role'         => $employe['role']   ?? 'employe',
            'pendingCount' => (int) (new CongeModel())->countByStatus($employeId, 'en_attente'),
        ]);
    }

    public function update()
    {
        $user      = $this->currentUser();
        $employeId = (int) ($user['id'] ?? 0);
        $response  = redirect()->to('/employe/profile');

        if ($this->request->getMethod() !== 'post') {
            return $response;
        }

        $employe = $this->employeModel->find($employeId);
        if (! $employe) {
            return redirect()->to('/login')->with('error', 'Votre compte est introuvable.');
        }

        $rules = [
            'nom'              => 'required|min_length[2]|max_length[100]',
            'prenom'           => 'required|min_length[2]|max_length[100]',
            'current_password' => 'required',
        ];

        $newPassword = (string) $this->request->getPost('new_password');
        if ($newPassword !== '') {
            $rules['new_password']      = 'min_length[6]|max_length[255]';
            $rules['confirm_password']  = 'required_with[new_password]|matches[new_password]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! password_verify((string) $this->request->getPost('current_password'), $employe['password'])) {
            return redirect()->back()->withInput()->with('errors', [
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        $data = [
            'nom'    => trim((string) $this->request->getPost('nom')),
            'prenom' => trim((string) $this->request->getPost('prenom')),
        ];

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->employeModel->update($employeId, $data);

        // Mettre à jour la session correctement sous session['user']
        $updatedUser = array_merge($user, [
            'nom'    => $data['nom'],
            'prenom' => $data['prenom'],
        ]);
        session()->set('user', $updatedUser);

        return redirect()->to('/employe/profile')->with('success', 'Votre profil a bien été mis à jour.');
    }
}
