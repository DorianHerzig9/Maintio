<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Work Orders Performance Indexes
        $this->forge->addKey(['status', 'priority'], false, false, 'idx_work_orders_status_priority');
        $this->forge->processIndexes('work_orders');

        $this->forge->addKey(['scheduled_date', 'status'], false, false, 'idx_work_orders_scheduled_status');
        $this->forge->processIndexes('work_orders');

        $this->forge->addKey('created_at', false, false, 'idx_work_orders_created_at');
        $this->forge->processIndexes('work_orders');

        $this->forge->addKey('assigned_user_id', false, false, 'idx_work_orders_assigned_user');
        $this->forge->processIndexes('work_orders');

        // Assets Performance Indexes
        $this->forge->addKey(['status', 'type'], false, false, 'idx_assets_status_type');
        $this->forge->processIndexes('assets');

        $this->forge->addKey('location', false, false, 'idx_assets_location');
        $this->forge->processIndexes('assets');

        $this->forge->addKey('priority', false, false, 'idx_assets_priority');
        $this->forge->processIndexes('assets');

        // Preventive Maintenance Performance Indexes
        $this->forge->addKey(['next_due', 'is_active'], false, false, 'idx_pm_next_due_active');
        $this->forge->processIndexes('preventive_maintenance');

        $this->forge->addKey('last_completed', false, false, 'idx_pm_last_completed');
        $this->forge->processIndexes('preventive_maintenance');

        $this->forge->addKey(['asset_id', 'is_active'], false, false, 'idx_pm_asset_active');
        $this->forge->processIndexes('preventive_maintenance');

        // Users Performance Indexes
        $this->forge->addKey(['role', 'is_active'], false, false, 'idx_users_role_active');
        $this->forge->processIndexes('users');

        $this->forge->addKey('email', false, false, 'idx_users_email');
        $this->forge->processIndexes('users');
    }

    public function down()
    {
        // Work Orders Indexes
        $this->forge->dropKey('work_orders', 'idx_work_orders_status_priority');
        $this->forge->dropKey('work_orders', 'idx_work_orders_scheduled_status');
        $this->forge->dropKey('work_orders', 'idx_work_orders_created_at');
        $this->forge->dropKey('work_orders', 'idx_work_orders_assigned_user');

        // Assets Indexes
        $this->forge->dropKey('assets', 'idx_assets_status_type');
        $this->forge->dropKey('assets', 'idx_assets_location');
        $this->forge->dropKey('assets', 'idx_assets_priority');

        // Preventive Maintenance Indexes
        $this->forge->dropKey('preventive_maintenance', 'idx_pm_next_due_active');
        $this->forge->dropKey('preventive_maintenance', 'idx_pm_last_completed');
        $this->forge->dropKey('preventive_maintenance', 'idx_pm_asset_active');

        // Users Indexes
        $this->forge->dropKey('users', 'idx_users_role_active');
        $this->forge->dropKey('users', 'idx_users_email');
    }
}