<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PreventiveMaintenanceModel;
use App\Models\WorkOrderModel;

class GeneratePreventiveWorkOrders extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'maintenance:generate-work-orders';
    protected $description = 'Generate work orders for due preventive maintenance schedules';
    protected $usage       = 'maintenance:generate-work-orders [options]';
    protected $arguments   = [];
    protected $options     = [
        '--dry-run' => 'Show what would be generated without actually creating work orders',
        '--days'    => 'Number of days ahead to check for due maintenance (default: 30)'
    ];

    public function run(array $params)
    {
        $pmModel = new PreventiveMaintenanceModel();
        $workOrderModel = new WorkOrderModel();
        
        $dryRun = CLI::getOption('dry-run');
        $daysAhead = (int) (CLI::getOption('days') ?? 30);

        CLI::write('Checking for preventive maintenance schedules needing work orders...', 'yellow');
        CLI::newLine();

        // Get schedules that need work order generation
        $schedulesNeedingOrders = $pmModel->getSchedulesNeedingWorkOrders();
        
        if (empty($schedulesNeedingOrders)) {
            CLI::write('No preventive maintenance schedules require work order generation at this time.', 'green');
            return;
        }

        CLI::write('Found ' . count($schedulesNeedingOrders) . ' schedules needing work orders:', 'cyan');
        CLI::newLine();

        $generatedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($schedulesNeedingOrders as $schedule) {
            $scheduleName = $schedule['schedule_name'];
            $assetName = $schedule['asset_name'];
            $dueDate = date('Y-m-d H:i', strtotime($schedule['next_due']));
            
            CLI::write("Processing: {$scheduleName} for {$assetName} (Due: {$dueDate})");

            // Check if work order already exists for this schedule and due date
            $existingOrder = $workOrderModel
                ->where('title', 'PM: ' . $schedule['schedule_name'])
                ->where('asset_id', $schedule['asset_id'])
                ->where('type', 'preventive')
                ->where('status', 'open')
                ->first();

            if ($existingOrder) {
                CLI::write("  ↳ Skipped: Work order already exists (#{$existingOrder['work_order_number']})", 'yellow');
                $skippedCount++;
                continue;
            }

            if ($dryRun) {
                CLI::write("  ↳ Would generate work order: PM: {$scheduleName}", 'blue');
                $generatedCount++;
                continue;
            }

            try {
                // Generate work order number
                $workOrderNumber = 'PM-' . date('Y') . '-' . str_pad($workOrderModel->countAll() + 1, 4, '0', STR_PAD_LEFT);

                $workOrderData = [
                    'work_order_number' => $workOrderNumber,
                    'title' => 'PM: ' . $schedule['schedule_name'],
                    'description' => $schedule['description'],
                    'type' => 'preventive',
                    'status' => 'open',
                    'priority' => $schedule['priority'],
                    'asset_id' => $schedule['asset_id'],
                    'assigned_user_id' => $schedule['assigned_user_id'],
                    'created_by_user_id' => 1, // System generated
                    'estimated_duration' => $schedule['estimated_duration'],
                    'scheduled_date' => $schedule['next_due'],
                    'notes' => $this->generateWorkOrderNotes($schedule)
                ];

                if ($workOrderModel->save($workOrderData)) {
                    CLI::write("  ↳ Generated work order: {$workOrderNumber}", 'green');
                    $generatedCount++;
                } else {
                    $error = "Failed to save work order for: {$scheduleName}";
                    CLI::write("  ↳ Error: {$error}", 'red');
                    $errors[] = $error;
                }
            } catch (\Exception $e) {
                $error = "Exception generating work order for {$scheduleName}: " . $e->getMessage();
                CLI::write("  ↳ Error: {$error}", 'red');
                $errors[] = $error;
            }
        }

        CLI::newLine();
        CLI::write('=== Summary ===', 'cyan');
        
        if ($dryRun) {
            CLI::write("Dry run completed:", 'blue');
            CLI::write("  Would generate: {$generatedCount} work orders");
            CLI::write("  Would skip: {$skippedCount} (already exist)");
        } else {
            CLI::write("Generation completed:", 'green');
            CLI::write("  Generated: {$generatedCount} new work orders");
            CLI::write("  Skipped: {$skippedCount} (already exist)");
            CLI::write("  Errors: " . count($errors));
        }

        if (!empty($errors)) {
            CLI::newLine();
            CLI::write('Errors encountered:', 'red');
            foreach ($errors as $error) {
                CLI::write("  • {$error}", 'red');
            }
        }

        CLI::newLine();
        
        if (!$dryRun && $generatedCount > 0) {
            CLI::write("Run 'php spark maintenance:generate-work-orders --dry-run' to preview what would be generated.", 'yellow');
        }
    }

    private function generateWorkOrderNotes(array $schedule): string
    {
        $notes = "Automatisch generiert für Instandhaltungsplan: {$schedule['schedule_name']}\n";
        
        if (!empty($schedule['task_details'])) {
            $notes .= "\nAufgabendetails:\n{$schedule['task_details']}\n";
        }
        
        if (!empty($schedule['required_tools'])) {
            $notes .= "\nBenötigte Werkzeuge:\n{$schedule['required_tools']}\n";
        }
        
        if (!empty($schedule['required_parts'])) {
            $notes .= "\nBenötigte Ersatzteile:\n{$schedule['required_parts']}\n";
        }
        
        if (!empty($schedule['safety_notes'])) {
            $notes .= "\nSicherheitshinweise:\n{$schedule['safety_notes']}\n";
        }

        $notes .= "\nInstandhaltungsintervall: {$schedule['interval_value']} ";
        $notes .= App\Models\PreventiveMaintenanceModel::getIntervalTypeText($schedule['interval_type']);

        return $notes;
    }
}