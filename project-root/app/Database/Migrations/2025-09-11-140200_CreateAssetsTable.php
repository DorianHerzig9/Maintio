<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'asset_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'operational',
            ],
            'priority' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'medium',
            ],
            'manufacturer' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'model' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'installation_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'purchase_price' => [
                'type'    => 'DECIMAL',
                'constraint' => '15,2',
                'null'    => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'svg_position_x' => [
                'type'    => 'DECIMAL',
                'constraint' => '10,2',
                'null'    => true,
            ],
            'svg_position_y' => [
                'type'    => 'DECIMAL',
                'constraint' => '10,2',
                'null'    => true,
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
        $this->forge->addKey('asset_number');
        $this->forge->addKey('status');
        $this->forge->createTable('assets');
    }

    public function down()
    {
        $this->forge->dropTable('assets');
    }
}
