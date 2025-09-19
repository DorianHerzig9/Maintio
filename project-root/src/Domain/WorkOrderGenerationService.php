<?php

namespace Src\Domain;

class WorkOrderGenerationService
{
    private ScheduleCalculationService $scheduleCalculationService;

    public function __construct(ScheduleCalculationService $scheduleCalculationService)
    {
        $this->scheduleCalculationService = $scheduleCalculationService;
    }

    /**
     * Generate work order data from maintenance schedule
     */
    public function generateWorkOrderFromSchedule(array $schedule, int $currentWorkOrderCount): array
    {
        $workOrderNumber = $this->generateWorkOrderNumber($currentWorkOrderCount);

        return [
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
    }

    /**
     * Generate work order number
     */
    public function generateWorkOrderNumber(int $currentCount): string
    {
        return 'PM-' . date('Y') . '-' . str_pad($currentCount + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate comprehensive notes for work order
     */
    public function generateWorkOrderNotes(array $schedule): string
    {
        $notes = "Automatisch generiert für Instandhaltungsplan: {$schedule['schedule_name']}\n\n";

        if (!empty($schedule['task_details'])) {
            $notes .= "Aufgabendetails:\n{$schedule['task_details']}\n\n";
        }

        if (!empty($schedule['required_tools'])) {
            $notes .= "Benötigte Werkzeuge:\n{$schedule['required_tools']}\n\n";
        }

        if (!empty($schedule['required_parts'])) {
            $notes .= "Benötigte Ersatzteile:\n{$schedule['required_parts']}\n\n";
        }

        if (!empty($schedule['safety_notes'])) {
            $notes .= "Sicherheitshinweise:\n{$schedule['safety_notes']}";
        }

        return trim($notes);
    }

    /**
     * Check if work order should be generated for schedule
     */
    public function shouldGenerateWorkOrder(array $schedule): bool
    {
        return $schedule['auto_generate_work_orders']
               && $schedule['is_active']
               && $this->scheduleCalculationService->isOverdue(new \DateTime($schedule['next_due']));
    }

    /**
     * Create schedule data from work order
     */
    public function createScheduleFromWorkOrder(array $workOrder, string $intervalType, int $intervalValue): array
    {
        $nextDue = $this->scheduleCalculationService->calculateNextDueDate($intervalType, $intervalValue);

        return [
            'asset_id' => $workOrder['asset_id'],
            'schedule_name' => str_replace('PM: ', '', $workOrder['title']),
            'description' => $workOrder['description'],
            'task_details' => $workOrder['notes'] ?? '',
            'interval_type' => $intervalType,
            'interval_value' => $intervalValue,
            'priority' => $workOrder['priority'],
            'estimated_duration' => $workOrder['estimated_duration'],
            'assigned_user_id' => $workOrder['assigned_user_id'],
            'auto_generate_work_orders' => 1,
            'lead_time_days' => 7,
            'next_due' => $nextDue->format('Y-m-d H:i:s'),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Validate work order generation data
     */
    public function validateWorkOrderGeneration(array $selectedOrders): array
    {
        $errors = [];

        foreach ($selectedOrders as $index => $orderData) {
            if (empty($orderData['work_order_id'])) {
                $errors[] = "Arbeitsauftrag ID fehlt bei Eintrag " . ($index + 1);
            }

            if (empty($orderData['interval_type']) || !$this->scheduleCalculationService->isValidIntervalType($orderData['interval_type'])) {
                $errors[] = "Ungültiger Intervalltyp bei Eintrag " . ($index + 1);
            }

            if (empty($orderData['interval_value']) || $orderData['interval_value'] <= 0) {
                $errors[] = "Ungültiger Intervallwert bei Eintrag " . ($index + 1);
            }
        }

        return $errors;
    }
}