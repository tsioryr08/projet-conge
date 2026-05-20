<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
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
        $user=$this->currentUser();
        $employeId = (int) ($user['id'] ?? 0);
        $employe = $this->employeModel->find($employeId);

        if (! $employe) {
            return redirect()->to('/login')->with('error', 'Votre compte est introuvable.');
        }

        return view('employe/profile', [
            'title' => 'Mon profil',
            'nom' => $employe['nom'] ?? '',
            'prenom' => $employe['prenom'] ?? '',
            'email' => $employe['email'] ?? '',
            'role' => $employe['role'] ?? 'employe',
            'pendingCount' => (int) (new \App\Models\CongeModel())->countByStatus($employeId, 'en_attente'),
        ]);
    }

    public function update()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/employe/profile');
        }

        $response = redirect()->to('/employe/profile');
        $employeId = (int) session()->get('employe_id');
        $employe = $this->employeModel->find($employeId);

        if (! $employe) {
            return redirect()->to('/login')->with('error', 'Votre compte est introuvable.');
        }

        $rules = [
            'nom' => 'required|min_length[2]|max_length[100]',
            'prenom' => 'required|min_length[2]|max_length[100]',
            'current_password' => 'required',
        ];

        $newPassword = (string) $this->request->getPost('new_password');
        if ($newPassword !== '') {
            $rules['new_password'] = 'min_length[6]|max_length[255]';
            $rules['confirm_password'] = 'required_with[new_password]|matches[new_password]';
        }

        if (! $this->validate($rules)) {
            $response = redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        } elseif (! password_verify((string) $this->request->getPost('current_password'), $employe['password'])) {
            $response = redirect()->back()->withInput()->with('errors', ['current_password' => 'Le mot de passe actuel est incorrect.']);
        } else {
            $data = [
                'nom' => trim((string) $this->request->getPost('nom')),
                'prenom' => trim((string) $this->request->getPost('prenom')),
            ];

            if ($newPassword !== '') {
                $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $this->employeModel->update($employeId, $data);

            session()->set([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
            ]);

            $response = redirect()->to('/employe/profile')->with('success', 'Votre profil a bien été mis à jour.');
        }

        return $response;
    }
}
