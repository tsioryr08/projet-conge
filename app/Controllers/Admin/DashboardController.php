<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\EmployeModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Acces refuse.');
        }

        $congeModel = new CongeModel();
        $employeModel = new EmployeModel();

        return view('admin/dashboard', [
            'title' => 'Tableau de bord admin',
            'user' => $user,
            'stats' => [
                'employes' => $employeModel->countAllResults(),
                'pending' => $congeModel->where('statut', 'en_attente')->countAllResults(),
                'approved' => $congeModel->where('statut', 'approuvee')->countAllResults(),
                'refused' => $congeModel->whereIn('statut', ['refusee', 'annulee'])->countAllResults(),
            ],
            'conges' => $congeModel->allWithDetails(),
        ]);
    }
}