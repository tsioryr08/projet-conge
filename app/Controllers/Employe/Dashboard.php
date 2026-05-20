<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $user      = $this->currentUser();           // ← lecture correcte
        $employeId = (int) ($user['id'] ?? 0);       // ← session['user']['id']
        $year      = (int) date('Y');

        $congeModel = new CongeModel();
        $soldeModel = new SoldeModel();

        $data = [
            'title'          => 'Tableau de bord',
            'user'           => $user,
            'nom'            => $user['nom']    ?? '',
            'prenom'         => $user['prenom'] ?? '',
            'email'          => $user['email']  ?? '',
            'year'           => $year,
            'soldes'         => $soldeModel->forEmployeYear($employeId, $year),
            'latestDemandes' => $congeModel->latestForEmploye($employeId, 3),
            'pendingCount'   => $congeModel->countByStatus($employeId, 'en_attente'),
            'stats'          => [
                'en_attente' => $congeModel->countByStatus($employeId, 'en_attente'),
                'approuvee'  => $congeModel->countByStatus($employeId, 'approuvee'),
                'refusee'    => $congeModel->countByStatus($employeId, 'refusee'),
            ],
        ];

        return view('employe/dashboard', $data);
    }
}