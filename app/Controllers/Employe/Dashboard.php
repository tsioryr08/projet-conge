<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $user      = $this->currentUser();
        $employeId = (int) ($user['id'] ?? 0);
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

    public function calendrier()
    {
        $user      = $this->currentUser();
        $employeId = (int) ($user['id'] ?? 0);

        $congeModel = new CongeModel();
        $demandes   = $congeModel->forEmploye($employeId);

        $events = [];
        foreach ($demandes as $d) {
            $color = match ($d['statut']) {
                'approuvee' => '#1e6b3f',
                'refusee'   => '#c0392b',
                'annulee'   => '#7a8f80',
                default     => '#b8750a',
            };
            $label = match ($d['statut']) {
                'approuvee' => 'Approuvée',
                'refusee'   => 'Refusée',
                'annulee'   => 'Annulée',
                default     => 'En attente',
            };
            $events[] = [
                'title' => ($d['type_libelle'] ?? 'Congé') . ' — ' . $label,
                'start' => $d['date_debut'],
                'end'   => date('Y-m-d', strtotime($d['date_fin'] . ' +1 day')),
                'color' => $color,
                'extendedProps' => [
                    'statut'   => $label,
                    'motif'    => $d['motif'] ?? '',
                    'nb_jours' => $d['nb_jours'] ?? 0,
                ],
            ];
        }

        return view('employe/calendrier', [
            'title'        => 'Vue calendrier',
            'user'         => $user,
            'nom'          => $user['nom'] ?? '',
            'prenom'       => $user['prenom'] ?? '',
            'events'       => json_encode($events),
            'pendingCount' => $congeModel->countByStatus($employeId, 'en_attente'),
        ]);
    }
}
