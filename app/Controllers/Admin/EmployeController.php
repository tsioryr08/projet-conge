<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmployeModel;
use App\Models\DepartementModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class EmployeController extends BaseController
{
    private EmployeModel $employeModel;
    private DepartementModel $departementModel;
    private SoldeModel $soldeModel;
    private TypeCongeModel $typeCongeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
        $this->departementModel = new DepartementModel();
        $this->soldeModel = new SoldeModel();
        $this->typeCongeModel = new TypeCongeModel();
    }

    public function index()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $employes = $this->employeModel
            ->select('employes.*, departements.nom as departement_nom')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->findAll();

        $departements = $this->departementModel->findAll();

        return view('admin/employes/index', [
            'title' => 'Gestion des employés',
            'user' => $user,
            'employes' => $employes,
            'departements' => $departements,
        ]);
    }

    public function create()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $departements = $this->departementModel->findAll();

        return view('admin/employes/create', [
            'title' => 'Créer un employé',
            'user' => $user,
            'departements' => $departements,
        ]);
    }

    public function store()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $validation = service('validation');
        $validation->setRules([
            'nom'            => 'required|min_length[2]|max_length[100]',
            'prenom'         => 'required|min_length[2]|max_length[100]',
            'email'          => 'required|valid_email|is_unique[employes.email]',
            'password'       => 'required|min_length[6]|max_length[255]',
            'role'           => 'required|in_list[employe,rh,admin]',
            'departement_id' => 'required|is_not_empty',
            'date_embauche'  => 'required|valid_date[Y-m-d]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
            'actif'          => 1,
        ];

        if (!$this->employeModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création.');
        }

        $employe_id = $this->employeModel->getInsertID();

        // Initialiser les soldes pour les types de congé
        $typesConge = $this->typeCongeModel->findAll();
        $year = date('Y');

        foreach ($typesConge as $type) {
            $this->soldeModel->insert([
                'employe_id'     => $employe_id,
                'type_conge_id'  => $type['id'],
                'annee'          => $year,
                'jours_attribues' => $type['jours_annuels'] ?? 0,
                'jours_pris'     => 0,
            ]);
        }

        return redirect()->to(base_url('admin/employes'))->with('success', 'Employé créé avec succès.');
    }

    public function edit(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $employe = $this->employeModel->find($id);

        if (!$employe) {
            return redirect()->to(base_url('admin/employes'))->with('error', 'Employé introuvable.');
        }

        $departements = $this->departementModel->findAll();

        return view('admin/employes/edit', [
            'title' => 'Éditer un employé',
            'user' => $user,
            'employe' => $employe,
            'departements' => $departements,
        ]);
    }

    public function update(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $employe = $this->employeModel->find($id);

        if (!$employe) {
            return redirect()->to(base_url('admin/employes'))->with('error', 'Employé introuvable.');
        }

        $validation = service('validation');
        $validation->setRules([
            'nom'            => 'required|min_length[2]|max_length[100]',
            'prenom'         => 'required|min_length[2]|max_length[100]',
            'email'          => 'required|valid_email|is_unique[employes.email,id,' . $id . ']',
            'role'           => 'required|in_list[employe,rh,admin]',
            'departement_id' => 'required|is_not_empty',
            'date_embauche'  => 'required|valid_date[Y-m-d]',
            'actif'          => 'required|in_list[0,1]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
            'actif'          => $this->request->getPost('actif'),
        ];

        if (!$this->employeModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
        }

        return redirect()->to(base_url('admin/employes'))->with('success', 'Employé mis à jour avec succès.');
    }

    public function deactivate(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $employe = $this->employeModel->find($id);

        if (!$employe) {
            return redirect()->to(base_url('admin/employes'))->with('error', 'Employé introuvable.');
        }

        if (!$this->employeModel->update($id, ['actif' => 0])) {
            return redirect()->back()->with('error', 'Erreur lors de la désactivation.');
        }

        return redirect()->to(base_url('admin/employes'))->with('success', 'Employé désactivé.');
    }
}
