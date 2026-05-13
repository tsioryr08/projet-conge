<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'created_at',
        'traite_par',
    ];

    public function forEmploye(int $employeId)
    {
        return $this->select('conges.*, types_conge.libelle as type_libelle, types_conge.deductible')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->where('conges.employe_id', $employeId)
            ->orderBy('conges.created_at', 'DESC')
            ->orderBy('conges.id', 'DESC')
            ->findAll();
    }

    public function latestForEmploye(int $employeId, int $limit = 3)
    {
        return $this->select('conges.*, types_conge.libelle as type_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->where('conges.employe_id', $employeId)
            ->orderBy('conges.created_at', 'DESC')
            ->orderBy('conges.id', 'DESC')
            ->findAll($limit);
    }

    public function countByStatus(int $employeId, string $status): int
    {
        return (int) $this->where('employe_id', $employeId)
            ->where('statut', $status)
            ->countAllResults();
    }
}
