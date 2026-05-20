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

        // Absences du mois en cours
        $currentMonth = date('Y-m');
        $monthStart = $currentMonth . '-01';
        $monthEnd = date('Y-m-t'); // Dernier jour du mois

        $absencesThisMonth = $congeModel
            ->whereIn('statut', ['approuvee'])
            ->where('date_debut <=', $monthEnd)
            ->where('date_fin >=', $monthStart)
            ->select('conges.*, employes.nom, employes.prenom, types_conge.libelle')
            ->join('employes', 'employes.id = conges.employe_id')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->findAll();

        // === Graphique 1: Congés par mois ===
        $year = date('Y');
        $congesByMonth = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        
        $allConges = $congeModel
            ->whereIn('statut', ['approuvee'])
            ->select('conges.*, DATE(date_debut) as start_date')
            ->findAll();
            
        foreach ($allConges as $conge) {
            $congeMonth = (int)date('m', strtotime($conge['date_debut']));
            if ($congeMonth >= 1 && $congeMonth <= 12) {
                $congesByMonth[$congeMonth - 1]++;
            }
        }

        // === Graphique 2: Congés par jour de la semaine ===
        $daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $congesByDay = [0, 0, 0, 0, 0, 0, 0];
        
        foreach ($allConges as $conge) {
            $startDate = new \DateTime($conge['date_debut']);
            $endDate = new \DateTime($conge['date_fin']);
            
            $current = clone $startDate;
            while ($current <= $endDate) {
                $dayOfWeek = (int)$current->format('N'); // 1=Lundi, 7=Dimanche
                if ($dayOfWeek >= 1 && $dayOfWeek <= 7) {
                    $congesByDay[$dayOfWeek - 1]++;
                }
                $current->modify('+1 day');
            }
        }

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
            'absencesThisMonth' => $absencesThisMonth,
            'currentMonth' => $currentMonth,
            'congesByMonth' => $congesByMonth,
            'congesByDay' => $congesByDay,
            'daysOfWeek' => $daysOfWeek,
        ]);
    }
}