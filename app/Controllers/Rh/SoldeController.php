<?php

namespace App\Controllers\Rh;

use App\Controllers\BaseController;
use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;

class SoldeController extends BaseController
{
    private SoldeModel $soldeModel;
    private EmployeModel $employeModel;
    private DepartementModel $departementModel;

    public function __construct()
    {
        $this->soldeModel       = new SoldeModel();
        $this->employeModel     = new EmployeModel();
        $this->departementModel = new DepartementModel();
    }

    public function index()
    {
        $user = $this->currentUser();
        if ($user === null || ($user['role'] ?? '') !== 'rh') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $departementId = $this->request->getGet('departement_id');
        $year          = (int) ($this->request->getGet('year') ?? date('Y'));
        $departementId = $departementId !== '' && $departementId !== null ? (int) $departementId : null;

        $query = $this->soldeModel
            ->select('soldes.*, employes.nom, employes.prenom, employes.departement_id, departements.nom as dept_libelle, types_conge.libelle as type_libelle')
            ->join('employes',     'employes.id = soldes.employe_id',              'left')
            ->join('departements', 'departements.id = employes.departement_id',    'left')
            ->join('types_conge',  'types_conge.id = soldes.type_conge_id',        'left')
            ->where('soldes.annee', $year);

        if ($departementId !== null) {
            $query->where('employes.departement_id', $departementId);
        }

        $soldes       = $query->orderBy('employes.nom', 'ASC')->orderBy('types_conge.libelle', 'ASC')->findAll();
        $departements = $this->departementModel->findAll();

        return view('rh/soldes/index', [
            'title'          => 'Gestion des soldes de congés',
            'user'           => $user,
            'soldes'         => $soldes,
            'departements'   => $departements,
            'year'           => $year,
            'departement_id' => $departementId,
        ]);
    }
}