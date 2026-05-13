<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCongesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INTEGER', 'auto_increment' => true],
            'employe_id'     => ['type' => 'INTEGER'],
            'type_conge_id'  => ['type' => 'INTEGER'],
            'date_debut'     => ['type' => 'DATE'],
            'date_fin'       => ['type' => 'DATE'],
            'nb_jours'       => ['type' => 'INTEGER'],
            'motif'          => ['type' => 'TEXT', 'null' => true],
            'statut'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'en_attente'],
            'commentaire_rh' => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'traite_par'     => ['type' => 'INTEGER', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('employe_id', 'employes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_conge_id', 'types_conge', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('traite_par', 'employes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('conges');
    }

    public function down()
    {
        $this->forge->dropTable('conges');
    }
}