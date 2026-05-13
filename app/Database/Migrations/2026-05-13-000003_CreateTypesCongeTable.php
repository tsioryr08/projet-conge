<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTypesCongeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INTEGER', 'auto_increment' => true],
            'libelle'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'jours_annuels'  => ['type' => 'INTEGER', 'default' => 0],
            'deductible'     => ['type' => 'INTEGER', 'default' => 1],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('types_conge');
    }

    public function down()
    {
        $this->forge->dropTable('types_conge');
    }
}