<?php

namespace App\Libraries;

use App\Models\CongeModel;
use App\Models\EmployeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use InvalidArgumentException;

class LeaveWorkflowService
{
    private CongeModel $congeModel;
    private SoldeModel $soldeModel;
    private TypeCongeModel $typeCongeModel;
    private EmployeModel $employeModel;

    public function __construct()
    {
        $this->congeModel = new CongeModel();
        $this->soldeModel = new SoldeModel();
        $this->typeCongeModel = new TypeCongeModel();
        $this->employeModel = new EmployeModel();
    }

    public function countDays(string $dateDebut, string $dateFin): int
    {
        $start = new DateTimeImmutable($dateDebut);
        $end = new DateTimeImmutable($dateFin);

        if ($start > $end) {
            throw new InvalidArgumentException('La date de debut doit etre anterieure ou egale a la date de fin.');
        }

        $period = new DatePeriod($start, new DateInterval('P1D'), $end->modify('+1 day'));
        $days = 0;

        foreach ($period as $day) {
            if ((int) $day->format('N') <= 5) {
                $days++;
            }
        }

        return max(1, $days);
    }

    public function submitRequest(array $payload, int $employeId): array
    {
        $type = $this->typeCongeModel->find((int) $payload['type_conge_id']);

        if ($type === null) {
            return ['success' => false, 'message' => 'Type de conge introuvable.'];
        }

        $nbJours = $this->countDays((string) $payload['date_debut'], (string) $payload['date_fin']);

        if ($this->congeModel->hasOverlap($employeId, (string) $payload['date_debut'], (string) $payload['date_fin'])) {
            return ['success' => false, 'message' => 'Une demande se chevauche deja sur cette periode.'];
        }

        $data = [
            'employe_id' => $employeId,
            'type_conge_id' => (int) $payload['type_conge_id'],
            'date_debut' => $payload['date_debut'],
            'date_fin' => $payload['date_fin'],
            'nb_jours' => $nbJours,
            'motif' => trim((string) ($payload['motif'] ?? '')),
            'statut' => 'en_attente',
            'commentaire_rh' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'traite_par' => null,
        ];

        if (! $this->congeModel->insert($data)) {
            return ['success' => false, 'message' => implode(' ', $this->congeModel->errors())];
        }

        return ['success' => true, 'message' => 'Demande de conge soumise avec succes.'];
    }

    public function approve(int $congeId, int $rhId, string $commentaire = ''): array
    {
        $conge = $this->congeModel->findWithDetails($congeId);

        if ($conge === null) {
            return ['success' => false, 'message' => 'Demande introuvable.'];
        }

        if (($conge['statut'] ?? '') !== 'en_attente') {
            return ['success' => false, 'message' => 'Seules les demandes en attente peuvent etre approuvees.'];
        }

        $type = $this->typeCongeModel->find((int) $conge['type_conge_id']);
        $deductible = (int) ($type['deductible'] ?? 1) === 1;
        $annee = (int) substr((string) $conge['date_debut'], 0, 4);

        $db = db_connect();
        $db->transStart();

        if ($deductible) {
            $solde = $this->soldeModel->findForEmployeeTypeYear((int) $conge['employe_id'], (int) $conge['type_conge_id'], $annee);

            if ($solde === null) {
                $db->transRollback();

                return ['success' => false, 'message' => 'Aucun solde disponible pour cet employe.'];
            }

            $joursPriseApresApprobation = (int) $solde['jours_pris'] + (int) $conge['nb_jours'];

            if ($joursPriseApresApprobation > (int) $solde['jours_attribues']) {
                $db->transRollback();

                return ['success' => false, 'message' => 'Solde insuffisant pour approuver cette demande.'];
            }

            $this->soldeModel->update((int) $solde['id'], [
                'jours_pris' => $joursPriseApresApprobation,
            ]);
        }

        $this->congeModel->update($congeId, [
            'statut' => 'approuvee',
            'commentaire_rh' => trim($commentaire) !== '' ? trim($commentaire) : $conge['commentaire_rh'],
            'traite_par' => $rhId,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return ['success' => false, 'message' => 'La transaction a echoue.'];
        }

        return ['success' => true, 'message' => 'Demande approuvee et solde mis a jour.'];
    }

    public function refuse(int $congeId, int $rhId, string $commentaire = ''): array
    {
        $conge = $this->congeModel->findWithDetails($congeId);

        if ($conge === null) {
            return ['success' => false, 'message' => 'Demande introuvable.'];
        }

        $statut = (string) ($conge['statut'] ?? '');

        if (! in_array($statut, ['en_attente', 'approuvee'], true)) {
            return ['success' => false, 'message' => 'Cette demande ne peut plus etre modifiee.'];
        }

        $type = $this->typeCongeModel->find((int) $conge['type_conge_id']);
        $deductible = (int) ($type['deductible'] ?? 1) === 1;
        $annee = (int) substr((string) $conge['date_debut'], 0, 4);

        $db = db_connect();
        $db->transStart();

        if ($statut === 'approuvee' && $deductible) {
            $solde = $this->soldeModel->findForEmployeeTypeYear((int) $conge['employe_id'], (int) $conge['type_conge_id'], $annee);

            if ($solde !== null) {
                $joursPriseApresRecredit = max(0, (int) $solde['jours_pris'] - (int) $conge['nb_jours']);

                $this->soldeModel->update((int) $solde['id'], [
                    'jours_pris' => $joursPriseApresRecredit,
                ]);
            }

            $nouveauStatut = 'annulee';
            $message = 'Demande annulee et solde recrédite.';
        } else {
            $nouveauStatut = 'refusee';
            $message = 'Demande refusee.';
        }

        $this->congeModel->update($congeId, [
            'statut' => $nouveauStatut,
            'commentaire_rh' => trim($commentaire) !== '' ? trim($commentaire) : $conge['commentaire_rh'],
            'traite_par' => $rhId,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return ['success' => false, 'message' => 'La transaction a echoue.'];
        }

        return ['success' => true, 'message' => $message];
    }
}