<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table      = 'employes';
    protected $primaryKey = 'id';

    protected $useTimestamps = false;

    protected $allowedFields = [
        'nom', 'prenom', 'email', 'password', 'role', 'departement_id', 'date_embauche', 'actif'
    ];

    protected $validationRules = [
        'email' => 'required|valid_email',
        'password' => 'required',
    ];
}
