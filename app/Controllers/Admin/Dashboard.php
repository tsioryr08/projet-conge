<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        return view('admin/dashboard', [
            'title' => 'Tableau de bord Admin',
            'nom'    => session()->get('nom'),
            'prenom' => session()->get('prenom'),
        ]);
    }
}