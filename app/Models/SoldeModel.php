<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['employe_id', 'type_conge_id', 'annee', 'jours_attribues', 'jours_pris'];

    public function forEmployeYear(int $employeId, int $year)
    {
        return $this->select('soldes.*, types_conge.libelle, types_conge.deductible')
            ->join('types_conge', 'types_conge.id = soldes.type_conge_id', 'left')
            ->where('soldes.employe_id', $employeId)
            ->where('soldes.annee', $year)
            ->orderBy('types_conge.libelle', 'ASC')
            ->findAll();
    }

    public function findForEmployeeTypeYear(int $employeId, int $typeCongeId, int $year)
    {
        return $this->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $year)
            ->first();
    }
}
