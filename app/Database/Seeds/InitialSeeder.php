<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // evite de re-inserer les données à chaque exécution du seeder
        $this->db->table('conges')->truncate();
        $this->db->table('soldes')->truncate();
        $this->db->table('employes')->truncate();
        $this->db->table('types_conge')->truncate();
        $this->db->table('departements')->truncate();

        $annee = date('Y');
        $now = date('Y-m-d H:i:s');

        // ── 1. Départements ──────────────────────────────────────
        $this->db->table('departements')->insertBatch([
            ['nom' => 'Informatique', 'description' => 'Département IT'],
            ['nom' => 'Ressources Humaines', 'description' => 'Département RH'],
            ['nom' => 'Finance', 'description' => 'Comptabilité et Finance'],
        ]);

        // ── 2. Types de congé ─────────────────────────────────────
        $this->db->table('types_conge')->insertBatch([
            ['libelle' => 'Congé annuel',    'jours_annuels' => 30, 'deductible' => 1],
            ['libelle' => 'Congé maladie',   'jours_annuels' => 15, 'deductible' => 1],
            ['libelle' => 'Congé sans solde','jours_annuels' => 0,  'deductible' => 0],
        ]);

        // ── 3. Employés ───────────────────────────────────────────
        $this->db->table('employes')->insertBatch([
            [
                'nom'            => 'Admin',
                'prenom'         => 'TechMada',
                'email'          => 'admin@techmada.mg',
                'password'       => password_hash('admin123', PASSWORD_DEFAULT),
                'role'           => 'admin',
                'departement_id' => 1,
                'date_embauche'  => '2023-01-01',
                'actif'          => 1,
            ],
            [
                'nom'            => 'Rakoto',
                'prenom'         => 'RH',
                'email'          => 'rh@techmada.mg',
                'password'       => password_hash('rh123', PASSWORD_DEFAULT),
                'role'           => 'rh',
                'departement_id' => 2,
                'date_embauche'  => '2023-03-01',
                'actif'          => 1,
            ],
            [
                'nom'            => 'Rasoa',
                'prenom'         => 'Marie',
                'email'          => 'marie@techmada.mg',
                'password'       => password_hash('employe123', PASSWORD_DEFAULT),
                'role'           => 'employe',
                'departement_id' => 1,
                'date_embauche'  => '2024-01-15',
                'actif'          => 1,
            ],
            [
                'nom'            => 'Randria',
                'prenom'         => 'Jean',
                'email'          => 'jean@techmada.mg',
                'password'       => password_hash('employe123', PASSWORD_DEFAULT),
                'role'           => 'employe',
                'departement_id' => 3,
                'date_embauche'  => '2024-06-01',
                'actif'          => 1,
            ],
        ]);

        $marieId = (int) $this->db->table('employes')->select('id')->where('email', 'marie@techmada.mg')->get()->getRowArray()['id'];
        $jeanId = (int) $this->db->table('employes')->select('id')->where('email', 'jean@techmada.mg')->get()->getRowArray()['id'];

        // ── 4. Soldes initiaux ────────────────────────────────────
        $this->db->table('soldes')->insertBatch([
            ['employe_id' => $marieId, 'type_conge_id' => 1, 'annee' => $annee, 'jours_attribues' => 30, 'jours_pris' => 0],
            ['employe_id' => $marieId, 'type_conge_id' => 2, 'annee' => $annee, 'jours_attribues' => 15, 'jours_pris' => 0],
            ['employe_id' => $jeanId, 'type_conge_id' => 1, 'annee' => $annee, 'jours_attribues' => 30, 'jours_pris' => 0],
            ['employe_id' => $jeanId, 'type_conge_id' => 2, 'annee' => $annee, 'jours_attribues' => 15, 'jours_pris' => 0],
        ]);

        // ── 5. Demandes de congé de démonstration ────────────────
        $this->db->table('conges')->insertBatch([
            [
                'employe_id'     => $marieId,
                'type_conge_id'  => 1,
                'date_debut'     => '2026-05-20',
                'date_fin'       => '2026-05-22',
                'nb_jours'       => 3,
                'motif'          => 'Congé familial',
                'statut'         => 'en_attente',
                'commentaire_rh' => null,
                'created_at'     => $now,
                'traite_par'     => null,
            ],
            [
                'employe_id'     => $marieId,
                'type_conge_id'  => 2,
                'date_debut'     => '2026-04-12',
                'date_fin'       => '2026-04-13',
                'nb_jours'       => 2,
                'motif'          => 'Rendez-vous médical',
                'statut'         => 'approuvee',
                'commentaire_rh' => 'Approuvé par RH',
                'created_at'     => $now,
                'traite_par'     => 2,
            ],
            [
                'employe_id'     => $marieId,
                'type_conge_id'  => 1,
                'date_debut'     => '2026-03-03',
                'date_fin'       => '2026-03-05',
                'nb_jours'       => 3,
                'motif'          => 'Voyage',
                'statut'         => 'refusee',
                'commentaire_rh' => 'Chevauchement de planning',
                'created_at'     => $now,
                'traite_par'     => 2,
            ],
        ]);
    }
}
