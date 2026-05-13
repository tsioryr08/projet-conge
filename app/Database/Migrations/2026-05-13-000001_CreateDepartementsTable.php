<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepartementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INTEGER', 'auto_increment' => true],
            'nom'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('departements');
    }

    public function down()
    {
        $this->forge->dropTable('departements');
    }
}