<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TypeCongeModel;

class TypeCongeController extends BaseController
{
    private TypeCongeModel $typeCongeModel;

    public function __construct()
    {
        $this->typeCongeModel = new TypeCongeModel();
    }

    public function index()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $typesConge = $this->typeCongeModel->findAll();

        return view('admin/types_conge/index', [
            'title' => 'Gestion des types de congé',
            'user' => $user,
            'typesConge' => $typesConge,
        ]);
    }

    public function create()
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        return view('admin/types_conge/create', [
            'title' => 'Créer un type de congé',
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
            'libelle'       => 'required|min_length[3]|max_length[100]',
            'jours_annuels' => 'required|integer|greater_than[0]',
            'deductible'    => 'required|in_list[0,1]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'libelle'       => $this->request->getPost('libelle'),
            'jours_annuels' => $this->request->getPost('jours_annuels'),
            'deductible'    => $this->request->getPost('deductible'),
        ];

        if (!$this->typeCongeModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création.');
        }

        return redirect()->to(base_url('admin/types_conge'))->with('success', 'Type de congé créé avec succès.');
    }

    public function edit(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $type = $this->typeCongeModel->find($id);

        if (!$type) {
            return redirect()->to(base_url('admin/types_conge'))->with('error', 'Type de congé introuvable.');
        }

        return view('admin/types_conge/edit', [
            'title' => 'Éditer un type de congé',
            'user' => $user,
            'type' => $type,
        ]);
    }

    public function update(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $type = $this->typeCongeModel->find($id);

        if (!$type) {
            return redirect()->to(base_url('admin/types_conge'))->with('error', 'Type de congé introuvable.');
        }

        $validation = service('validation');
        $validation->setRules([
            'libelle'       => 'required|min_length[3]|max_length[100]',
            'jours_annuels' => 'required|integer|greater_than[0]',
            'deductible'    => 'required|in_list[0,1]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'libelle'       => $this->request->getPost('libelle'),
            'jours_annuels' => $this->request->getPost('jours_annuels'),
            'deductible'    => $this->request->getPost('deductible'),
        ];

        if (!$this->typeCongeModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
        }

        return redirect()->to(base_url('admin/types_conge'))->with('success', 'Type de congé mis à jour avec succès.');
    }

    public function delete(int $id)
    {
        $user = $this->currentUser();

        if ($user === null || ($user['role'] ?? '') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Accès refusé.');
        }

        $type = $this->typeCongeModel->find($id);

        if (!$type) {
            return redirect()->to(base_url('admin/types_conge'))->with('error', 'Type de congé introuvable.');
        }

        if (!$this->typeCongeModel->delete($id)) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }

        return redirect()->to(base_url('admin/types_conge'))->with('success', 'Type de congé supprimé avec succès.');
    }
}
