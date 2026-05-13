<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table         = 'conges';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'employe_id', 'type_conge_id', 'date_debut', 'date_fin',
        'nb_jours', 'motif', 'statut', 'commentaire_rh',
        'created_at', 'traite_par',
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

    public function pending(): array
    {
        return $this->select('conges.*, types_conge.libelle as type_libelle, employes.nom, employes.prenom, employes.departement_id, departements.nom as dept_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->join('employes', 'employes.id = conges.employe_id', 'left')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('conges.statut', 'en_attente')
            ->orderBy('conges.created_at', 'ASC')
            ->findAll();
    }

    public function allWithDetails(): array
    {
        return $this->select('conges.*, types_conge.libelle as type_libelle, employes.nom, employes.prenom, employes.departement_id, departements.nom as dept_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->join('employes', 'employes.id = conges.employe_id', 'left')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->orderBy('conges.created_at', 'DESC')
            ->findAll();
    }

    public function findWithDetails(int $id): ?array
    {
        return $this->select('conges.*, types_conge.libelle as type_libelle, types_conge.deductible, employes.nom, employes.prenom, employes.departement_id, departements.nom as dept_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->join('employes', 'employes.id = conges.employe_id', 'left')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('conges.id', $id)
            ->first();
    }

    public function hasOverlap(int $employeId, string $dateDebut, string $dateFin): bool
    {
        return $this->where('employe_id', $employeId)
            ->whereIn('statut', ['en_attente', 'approuvee'])
            ->groupStart()
                ->where('date_debut <=', $dateFin)
                ->where('date_fin >=', $dateDebut)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    public function byStatus(string $status, ?int $departementId = null): array
    {
        $query = $this->select('conges.*, types_conge.libelle as type_libelle, employes.nom, employes.prenom, employes.departement_id, departements.nom as dept_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->join('employes', 'employes.id = conges.employe_id', 'left')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->where('conges.statut', $status);

        if ($departementId !== null) {
            $query->where('employes.departement_id', $departementId);
        }

        return $query->orderBy('conges.created_at', 'DESC')->findAll();
    }
}