<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePreventiveMaintenanceTable extends Migration
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
            'asset_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'schedule_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'task_details' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Detailed task instructions for maintenance',
            ],
            'interval_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'daily, weekly, monthly, quarterly, annually, hours, cycles, kilometers',
            ],
            'interval_value' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Number of intervals (e.g., 30 for 30 days, 500 for 500 hours)',
            ],
            'priority' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'medium',
            ],
            'estimated_duration' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Estimated duration in minutes',
            ],
            'auto_generate_work_orders' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'lead_time_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 7,
                'comment'    => 'Generate work order X days before due date',
            ],
            'last_completed' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Last time this maintenance was completed',
            ],
            'next_due' => [
                'type' => 'DATETIME',
                'null' => false,
                'comment' => 'Next scheduled maintenance date',
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'assigned_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Default user to assign generated work orders to',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Maintenance category (electrical, mechanical, safety, etc.)',
            ],
            'required_tools' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'List of tools/equipment needed',
            ],
            'required_parts' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'List of spare parts typically needed',
            ],
            'safety_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Safety considerations and precautions',
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
        $this->forge->addKey('asset_id');
        $this->forge->addKey('next_due');
        $this->forge->addKey('is_active');
        $this->forge->addKey('interval_type');
        
        // Foreign key constraints
        $this->forge->addForeignKey('asset_id', 'assets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('preventive_maintenance');
    }

    public function down()
    {
        $this->forge->dropTable('preventive_maintenance');
    }
}