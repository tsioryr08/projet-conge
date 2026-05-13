<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $employeId = (int) session()->get('employe_id');
        $year = (int) date('Y');
        $congeModel = new CongeModel();
        $soldeModel = new SoldeModel();

        $data = [
            'title' => 'Tableau de bord',
            'nom' => session()->get('nom'),
            'prenom' => session()->get('prenom'),
            'email' => session()->get('email'),
            'year' => $year,
            'soldes' => $soldeModel->forEmployeYear($employeId, $year),
            'latestDemandes' => $congeModel->latestForEmploye($employeId, 3),
            'stats' => [
                'en_attente' => $congeModel->countByStatus($employeId, 'en_attente'),
                'approuvee' => $congeModel->countByStatus($employeId, 'approuvee'),
                'refusee' => $congeModel->countByStatus($employeId, 'refusee'),
            ],
        ];

        return view('employe/dashboard', $data);
    }
}
