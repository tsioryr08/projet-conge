<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartementModel;

class DepartementController extends BaseController
{
    private DepartementModel $departementModel;

    public function __construct()
    {
        $this->departementModel = new DepartementModel();
    }

    public function index()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $departements = $this->departementModel->findAll();

        return view('admin/departements/index', [
            'title' => 'Gestion des départements',
            'user' => $user,
            'departements' => $departements,
        ]);
    }

    public function create()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        return view('admin/departements/create', [
            'title' => 'Créer un département',
            'user' => $user,
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
            'nom'         => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|min_length[5]|max_length[500]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom'         => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description') ?? null,
        ];

        if (!$this->departementModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création.');
        }

        return redirect()->to(base_url('admin/departements'))->with('success', 'Département créé avec succès.');
    }

    public function edit(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $departement = $this->departementModel->find($id);

        if (!$departement) {
            return redirect()->to(base_url('admin/departements'))->with('error', 'Département introuvable.');
        }

        return view('admin/departements/edit', [
            'title' => 'Éditer un département',
            'user' => $user,
            'departement' => $departement,
        ]);
    }

    public function update(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $departement = $this->departementModel->find($id);

        if (!$departement) {
            return redirect()->to(base_url('admin/departements'))->with('error', 'Département introuvable.');
        }

        $validation = service('validation');
        $validation->setRules([
            'nom'         => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|min_length[5]|max_length[500]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom'         => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description') ?? null,
        ];

        if (!$this->departementModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
        }

        return redirect()->to(base_url('admin/departements'))->with('success', 'Département mis à jour avec succès.');
    }

    public function delete(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $departement = $this->departementModel->find($id);

        if (!$departement) {
            return redirect()->to(base_url('admin/departements'))->with('error', 'Département introuvable.');
        }

        if (!$this->departementModel->delete($id)) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }

        return redirect()->to(base_url('admin/departements'))->with('success', 'Département supprimé avec succès.');
    }
}
