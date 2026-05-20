<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = $this->currentUser();
        if ($user === null || ($user['role'] ?? '') !== 'employe') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $year           = (int) date('Y');
        $employeId      = (int) $user['id'];
        $soldeModel     = new SoldeModel();
        $congeModel     = new CongeModel();

        return view('employe/dashboard', [
            'title'          => 'Tableau de bord',
            'user'           => $user,
            'nom'            => $user['nom']    ?? '',
            'prenom'         => $user['prenom'] ?? '',
            'year'           => $year,
            'soldes'         => $soldeModel->forEmployeYear($employeId, $year),   // ← nom corrigé
            'latestDemandes' => $congeModel->latestForEmploye($employeId, 3),     // ← nom corrigé
            'pendingCount'   => $congeModel->countByStatus($employeId, 'en_attente'),
            'stats'          => [
                'en_attente' => $congeModel->countByStatus($employeId, 'en_attente'),
                'approuvee'  => $congeModel->countByStatus($employeId, 'approuvee'),
                'refusee'    => $congeModel->countByStatus($employeId, 'refusee'),
            ],
        ]);
    }
}