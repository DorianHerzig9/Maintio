<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkOrderComponentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'work_order_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kks_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'KKS Kraftwerk-Kennzeichnungs-System Nummer',
            ],
            'component_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'comment'    => 'Name/Bezeichnung der Komponente',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Beschreibung der Komponente oder Arbeiten',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pending',
                'comment'    => 'pending, in_progress, completed, skipped',
            ],
            'order_position' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Reihenfolge der Komponenten',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('work_order_id');
        $this->forge->addKey('kks_number');
        $this->forge->addKey('status');
        
        // Foreign key constraint
        $this->forge->addForeignKey('work_order_id', 'work_orders', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('work_order_components');
    }

    public function down()
    {
        $this->forge->dropTable('work_order_components');
    }
}
