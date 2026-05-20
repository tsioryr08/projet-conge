<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Libraries\LeaveWorkflowService;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class DashboardController extends BaseController
{
    private LeaveWorkflowService $workflowService;

    public function __construct()
    {
        $this->workflowService = new LeaveWorkflowService();
    }

    public function index()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'employe') {
            return redirect()->to(base_url('login'))->with('error', 'Acces refuse.');
        }

        $year = (int) date('Y');
        $soldeModel = new SoldeModel();
        $congeModel = new CongeModel();
        $typeCongeModel = new TypeCongeModel();

        return view('employe/dashboard', [
            'title' => 'Espace employe',
            'user' => $user,
            'types' => $typeCongeModel->allForSelection(),
            'soldes' => $soldeModel->forEmployeeYear((int) $user['id'], $year),
            'conges' => $congeModel->forEmployee((int) $user['id']),
            'year' => $year,
        ]);
    }

    public function store()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'employe') {
            return redirect()->to(base_url('login'))->with('error', 'Acces refuse.');
        }

        if (! $this->validate([
            'type_conge_id' => 'required|is_natural_no_zero',
            'date_debut' => 'required|valid_date[Y-m-d]',
            'date_fin' => 'required|valid_date[Y-m-d]',
            'motif' => 'permit_empty|max_length[1000]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Merci de remplir correctement le formulaire.');
        }

        $result = $this->workflowService->submitRequest($this->request->getPost(), (int) $user['id']);

        if (! $result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to(base_url('employe'))->with('success', $result['message']);
    }
}