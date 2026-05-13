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

        // ── 4. Soldes initiaux ────────────────────────────────────
        // Pour Marie (id=3) et Jean (id=4)
        $this->db->table('soldes')->insertBatch([
            // Marie — congé annuel
            ['employe_id' => 3, 'type_conge_id' => 1, 'annee' => $annee, 'jours_attribues' => 30, 'jours_pris' => 0],
            // Marie — congé maladie
            ['employe_id' => 3, 'type_conge_id' => 2, 'annee' => $annee, 'jours_attribues' => 15, 'jours_pris' => 0],
            // Jean — congé annuel
            ['employe_id' => 4, 'type_conge_id' => 1, 'annee' => $annee, 'jours_attribues' => 30, 'jours_pris' => 0],
            // Jean — congé maladie
            ['employe_id' => 4, 'type_conge_id' => 2, 'annee' => $annee, 'jours_attribues' => 15, 'jours_pris' => 0],
        ]);
    }
}