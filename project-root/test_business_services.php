<?php

// Simple test file to verify business services work correctly
require_once __DIR__ . '/src/Domain/ScheduleCalculationService.php';
require_once __DIR__ . '/src/Domain/MaintenanceStatisticsService.php';
require_once __DIR__ . '/src/Domain/WorkOrderGenerationService.php';
require_once __DIR__ . '/src/Domain/ScheduleValidationService.php';

use Src\Domain\ScheduleCalculationService;
use Src\Domain\MaintenanceStatisticsService;
use Src\Domain\WorkOrderGenerationService;
use Src\Domain\ScheduleValidationService;

echo "=== Testing Business Services ===\n\n";

// Test 1: ScheduleCalculationService
echo "1. Testing ScheduleCalculationService:\n";
$scheduleService = new ScheduleCalculationService();

$nextDue = $scheduleService->calculateNextDueDate('monthly', 3);
echo "   Next due date (3 months): " . $nextDue->format('Y-m-d H:i:s') . "\n";

$isOverdue = $scheduleService->isOverdue(new DateTime('2024-01-01'));
echo "   Is 2024-01-01 overdue: " . ($isOverdue ? 'Yes' : 'No') . "\n";

$daysUntil = $scheduleService->getDaysUntilDue($nextDue);
echo "   Days until next due: " . $daysUntil . "\n";

$intervalText = $scheduleService->getIntervalText('monthly');
echo "   Interval text for 'monthly': " . $intervalText . "\n\n";

// Test 2: MaintenanceStatisticsService
echo "2. Testing MaintenanceStatisticsService:\n";
$statisticsService = new MaintenanceStatisticsService($scheduleService);

// Sample schedule data
$sampleSchedules = [
    [
        'is_active' => 1,
        'priority' => 'high',
        'next_due' => (new DateTime('-5 days'))->format('Y-m-d H:i:s')
    ],
    [
        'is_active' => 1,
        'priority' => 'medium',
        'next_due' => (new DateTime('+10 days'))->format('Y-m-d H:i:s')
    ],
    [
        'is_active' => 1,
        'priority' => 'critical',
        'next_due' => (new DateTime('+2 days'))->format('Y-m-d H:i:s')
    ]
];

$stats = $statisticsService->calculateStatistics($sampleSchedules);
echo "   Total active: " . $stats['total_active'] . "\n";
echo "   Overdue: " . $stats['overdue'] . "\n";
echo "   Upcoming (7 days): " . $stats['upcoming_7_days'] . "\n";
echo "   Critical priority: " . $stats['by_priority']['critical'] . "\n\n";

// Test 3: WorkOrderGenerationService
echo "3. Testing WorkOrderGenerationService:\n";
$workOrderService = new WorkOrderGenerationService($scheduleService);

$sampleSchedule = [
    'schedule_name' => 'Test Wartung',
    'description' => 'Test Beschreibung',
    'priority' => 'high',
    'asset_id' => 1,
    'assigned_user_id' => 1,
    'estimated_duration' => 120,
    'next_due' => (new DateTime())->format('Y-m-d H:i:s'),
    'task_details' => 'Detaillierte Aufgaben',
    'required_tools' => 'Schraubenzieher, Hammer',
    'required_parts' => 'Filter, Dichtung',
    'safety_notes' => 'Schutzbrille tragen'
];

$workOrderData = $workOrderService->generateWorkOrderFromSchedule($sampleSchedule, 100);
echo "   Generated work order number: " . $workOrderData['work_order_number'] . "\n";
echo "   Work order title: " . $workOrderData['title'] . "\n";
echo "   Work order type: " . $workOrderData['type'] . "\n";

$shouldGenerate = $workOrderService->shouldGenerateWorkOrder([
    'auto_generate_work_orders' => true,
    'is_active' => true,
    'next_due' => (new DateTime('-1 day'))->format('Y-m-d H:i:s')
]);
echo "   Should generate work order: " . ($shouldGenerate ? 'Yes' : 'No') . "\n\n";

// Test 4: ScheduleValidationService
echo "4. Testing ScheduleValidationService:\n";
$validationService = new ScheduleValidationService($scheduleService);

$testData = [
    'asset_id' => 1,
    'schedule_name' => 'Test Schedule',
    'interval_type' => 'monthly',
    'interval_value' => 3,
    'priority' => 'high'
];

$errors = $validationService->validateScheduleData($testData);
echo "   Validation errors for valid data: " . (empty($errors) ? 'None' : count($errors)) . "\n";

$invalidData = [
    'asset_id' => '',
    'schedule_name' => '',
    'interval_type' => 'invalid',
    'interval_value' => 0,
    'priority' => 'invalid'
];

$errors = $validationService->validateScheduleData($invalidData);
echo "   Validation errors for invalid data: " . count($errors) . "\n";

$isValidPriority = $validationService->isValidPriority('high');
echo "   Is 'high' a valid priority: " . ($isValidPriority ? 'Yes' : 'No') . "\n";

$sanitized = $validationService->sanitizeScheduleData([
    'asset_id' => '1',
    'schedule_name' => '  Test Schedule  ',
    'interval_value' => '3',
    'auto_generate_work_orders' => '1'
]);
echo "   Sanitized asset_id type: " . gettype($sanitized['asset_id']) . "\n";
echo "   Sanitized schedule_name: '" . $sanitized['schedule_name'] . "'\n";
echo "   Sanitized auto_generate_work_orders: " . ($sanitized['auto_generate_work_orders'] ? 'true' : 'false') . "\n\n";

echo "=== All Business Services Tests Completed Successfully! ===\n";