<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class Demandes extends BaseController
{
    private const PATH_INDEX  = '/employe/demandes';
    private const PATH_CREATE = '/employe/demandes/create';

    private CongeModel $congeModel;
    private SoldeModel $soldeModel;
    private TypeCongeModel $typeCongeModel;

    public function __construct()
    {
        helper('form');
        $this->congeModel     = new CongeModel();
        $this->soldeModel     = new SoldeModel();
        $this->typeCongeModel = new TypeCongeModel();
    }

    // Helper : récupère l'id de l'employé connecté depuis session['user']
    private function employeId(): int
    {
        $user = session()->get('user');
        return (int) ($user['id'] ?? 0);
    }

    public function index()
    {
        $user      = $this->currentUser();
        $employeId = $this->employeId();

        return view('employe/demandes/index', [
            'title'        => 'Mes demandes',
            'user'         => $user,
            'pendingCount' => $this->congeModel->countByStatus($employeId, 'en_attente'),
            'demandes'     => $this->congeModel->forEmploye($employeId),
        ]);
    }

    public function create()
    {
        $user      = $this->currentUser();
        $employeId = $this->employeId();
        $year      = (int) date('Y');

        return view('employe/demandes/create', [
            'title'        => 'Nouvelle demande',
            'user'         => $user,
            'pendingCount' => $this->congeModel->countByStatus($employeId, 'en_attente'),
            'typesConge'   => $this->typeCongeModel->findAll(),
            'soldes'       => $this->soldeModel->forEmployeYear($employeId, $year),
            'year'         => $year,
        ]);
    }

    public function store()
    {
        $response = redirect()->to(self::PATH_CREATE);

        if ($this->request->is('post')) {
            $rules = [
                'type_conge_id' => 'required|is_natural_no_zero',
                'date_debut'    => 'required|valid_date[Y-m-d]',
                'date_fin'      => 'required|valid_date[Y-m-d]',
                'motif'         => 'permit_empty|max_length[1000]',
            ];

            if (! $this->validate($rules)) {
                $response = redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            } else {
                $payload = $this->buildDemandPayload();

                if ($payload['error'] !== null) {
                    $response = redirect()->back()->withInput()->with('errors', $payload['error']);
                } else {
                    $insertedId = $this->congeModel->insert($payload['data']);

                    if ($insertedId === false) {
                        $dbError = service('db')->error();
                        log_message('error', 'Erreur insertion demande congé: ' . json_encode($dbError, JSON_UNESCAPED_UNICODE));
                        $response = redirect()->back()->withInput()->with('errors', ['database' => 'Impossible d\'enregistrer la demande.']);
                    } else {
                        $response = redirect()->to(self::PATH_INDEX)->with('success', 'Votre demande de congé a bien été soumise.');
                    }
                }
            }
        }

        return $response;
    }

    private function buildDemandPayload(): array
    {
        $input      = $this->getDemandInput();
        $validation = $this->validateDemandInput($input);
        $error      = $validation['error'];
        $data       = [];

        if ($error === null) {
            $normalizedInput = $validation['input'];
            $typeConge       = $this->typeCongeModel->find($normalizedInput['type_conge_id']);

            if (! $typeConge) {
                $error = ['type_conge_id' => 'Type de congé introuvable.'];
            } elseif ($this->hasDemandOverlap($normalizedInput['employe_id'], $normalizedInput['date_debut'], $normalizedInput['date_fin'])) {
                $error = ['date_debut' => 'Vous avez déjà une demande active sur cette période.'];
            } else {
                $soldeError = $this->validateSoldeAvailability(
                    $normalizedInput['employe_id'],
                    $normalizedInput['type_conge_id'],
                    $typeConge,
                    $normalizedInput['nb_jours']
                );

                if ($soldeError !== null) {
                    $error = $soldeError;
                } else {
                    $data = $this->buildDemandData($normalizedInput);
                }
            }
        }

        return ['error' => $error, 'data' => $data];
    }

    private function getDemandInput(): array
    {
        return [
            'employe_id'    => $this->employeId(),   // ← corrigé
            'type_conge_id' => (int) $this->request->getPost('type_conge_id'),
            'date_debut'    => (string) $this->request->getPost('date_debut'),
            'date_fin'      => (string) $this->request->getPost('date_fin'),
            'motif'         => trim((string) $this->request->getPost('motif')),
        ];
    }

    private function validateDemandInput(array $input): array
    {
        $error          = null;
        $normalizedInput = $input;
        $debut = \DateTimeImmutable::createFromFormat('Y-m-d', $input['date_debut']) ?: null;
        $fin   = \DateTimeImmutable::createFromFormat('Y-m-d', $input['date_fin'])   ?: null;

        if (! $debut || ! $fin) {
            $error = ['date_debut' => 'Dates invalides.'];
        } else {
            $today = new \DateTimeImmutable('today');

            if ($debut < $today) {
                $error = ['date_debut' => 'La date de début doit être aujourd\'hui ou dans le futur.'];
            } elseif ($fin < $debut) {
                $error = ['date_fin' => 'La date de fin doit être supérieure ou égale à la date de début.'];
            } else {
                $nbJours = $this->countBusinessDays($debut, $fin);

                if ($nbJours < 1) {
                    $error = ['date_fin' => 'La période sélectionnée ne contient aucun jour ouvrable.'];
                } else {
                    $normalizedInput['date_debut'] = $debut->format('Y-m-d');
                    $normalizedInput['date_fin']   = $fin->format('Y-m-d');
                    $normalizedInput['nb_jours']   = $nbJours;
                }
            }
        }

        return ['error' => $error, 'input' => $normalizedInput];
    }

    private function hasDemandOverlap(int $employeId, string $dateDebut, string $dateFin): bool
    {
        return $this->congeModel->where('employe_id', $employeId)
            ->whereIn('statut', ['en_attente', 'approuvee'])
            ->groupStart()
                ->where('date_debut <=', $dateFin)
                ->where('date_fin >=', $dateDebut)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    private function validateSoldeAvailability(int $employeId, int $typeCongeId, array $typeConge, int $nbJours): ?array
    {
        $deductible = (int) ($typeConge['deductible'] ?? 1);
        if ($deductible !== 1) {
            return null;
        }

        $solde         = $this->soldeModel->findForEmployeeTypeYear($employeId, $typeCongeId, (int) date('Y'));
        $joursAttribues = (int) ($solde['jours_attribues'] ?? 0);
        $joursPris      = (int) ($solde['jours_pris']      ?? 0);

        if (($joursPris + $nbJours) > $joursAttribues) {
            return ['type_conge_id' => 'Solde insuffisant pour cette demande.'];
        }

        return null;
    }

    private function buildDemandData(array $input): array
    {
        return [
            'employe_id'    => $input['employe_id'],
            'type_conge_id' => $input['type_conge_id'],
            'date_debut'    => $input['date_debut'],
            'date_fin'      => $input['date_fin'],
            'nb_jours'      => $input['nb_jours'],
            'motif'         => $input['motif'] !== '' ? $input['motif'] : null,
            'statut'        => 'en_attente',
            'created_at'    => date('Y-m-d H:i:s'),
        ];
    }

    public function cancel(int $id)
    {
        $employeId = $this->employeId();
        $demande   = $this->congeModel->where('id', $id)->where('employe_id', $employeId)->first();

        if (! $demande) {
            return redirect()->to(self::PATH_INDEX)->with('error', 'Demande introuvable.');
        }

        if ($demande['statut'] !== 'en_attente') {
            return redirect()->to(self::PATH_INDEX)->with('error', 'Seule une demande en attente peut être annulée.');
        }

        $this->congeModel->update($id, ['statut' => 'annulee']);

        return redirect()->to(self::PATH_INDEX)->with('success', 'La demande a été annulée.');
    }

    private function countBusinessDays(\DateTimeImmutable $debut, \DateTimeImmutable $fin): int
    {
        $count   = 0;
        $current = $debut;

        while ($current <= $fin) {
            $dayOfWeek = (int) $current->format('N');
            if ($dayOfWeek < 6) {
                $count++;
            }
            $current = $current->modify('+1 day');
        }

        return $count;
    }
}