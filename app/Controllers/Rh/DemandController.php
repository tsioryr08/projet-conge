<?php

namespace App\Controllers\Rh;

use App\Controllers\BaseController;
use App\Libraries\LeaveWorkflowService;
use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployeModel;

class DemandController extends BaseController
{
    private LeaveWorkflowService $workflowService;
    private CongeModel $congeModel;
    private DepartementModel $departementModel;
    private EmployeModel $employeModel;

    public function __construct()
    {
        $this->workflowService = new LeaveWorkflowService();
        $this->congeModel = new CongeModel();
        $this->departementModel = new DepartementModel();
        $this->employeModel = new EmployeModel();
    }

    public function index()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'rh') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $statut = $this->request->getGet('statut') ?? 'en_attente';
        $departementId = $this->request->getGet('departement_id');

        $departementId = $departementId !== '' && $departementId !== null ? (int) $departementId : null;

        $demandes = $departementId !== null
            ? $this->congeModel->byStatus($statut, $departementId)
            : $this->congeModel->byStatus($statut);

        $departements = $this->departementModel->findAll();

        $stats = [
            'en_attente' => $this->congeModel->where('statut', 'en_attente')->countAllResults(),
            'approuvee' => $this->congeModel->where('statut', 'approuvee')->countAllResults(),
            'refusee' => $this->congeModel->where('statut', 'refusee')->countAllResults(),
            'annulee' => $this->congeModel->where('statut', 'annulee')->countAllResults(),
        ];

        return view('rh/demandes/index', [
            'title' => 'Gestion des demandes de congés',
            'user' => $user,
            'demandes' => $demandes,
            'departements' => $departements,
            'statut' => $statut,
            'departement_id' => $departementId,
            'stats' => $stats,
        ]);
    }

    public function detail(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'rh') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $demande = $this->congeModel->findWithDetails($id);

        if (!$demande) {
            return redirect()->to(base_url('rh/demandes'))->with('error', 'Demande introuvable.');
        }

        return view('rh/demandes/detail', [
            'title' => 'Détail de la demande',
            'user' => $user,
            'demande' => $demande,
        ]);
    }

    public function approve(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'rh') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $commentaire = trim((string) $this->request->getPost('commentaire_rh'));

        $result = $this->workflowService->approve($id, (int) $user['id'], $commentaire);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(base_url('rh/demandes'))->with('success', $result['message']);
    }

    public function refuse(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'rh') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $commentaire = trim((string) $this->request->getPost('commentaire_rh'));

        $result = $this->workflowService->refuse($id, (int) $user['id'], $commentaire);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(base_url('rh/demandes'))->with('success', $result['message']);
    }
}
