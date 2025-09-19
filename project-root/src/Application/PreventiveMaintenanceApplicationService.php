<?php

namespace Src\Application;

use Src\Domain\ScheduleCalculationService;
use Src\Domain\MaintenanceStatisticsService;
use Src\Domain\WorkOrderGenerationService;
use Src\Domain\ScheduleValidationService;

class PreventiveMaintenanceApplicationService
{
    private ScheduleCalculationService $scheduleCalculationService;
    private MaintenanceStatisticsService $statisticsService;
    private WorkOrderGenerationService $workOrderGenerationService;
    private ScheduleValidationService $validationService;

    // Data access dependencies (injected by Controller)
    private $pmModel;
    private $assetModel;
    private $userModel;
    private $workOrderModel;

    public function __construct(
        ScheduleCalculationService $scheduleCalculationService,
        MaintenanceStatisticsService $statisticsService,
        WorkOrderGenerationService $workOrderGenerationService,
        ScheduleValidationService $validationService
    ) {
        $this->scheduleCalculationService = $scheduleCalculationService;
        $this->statisticsService = $statisticsService;
        $this->workOrderGenerationService = $workOrderGenerationService;
        $this->validationService = $validationService;
    }

    /**
     * Set data access dependencies
     */
    public function setDataAccess($pmModel, $assetModel, $userModel, $workOrderModel): void
    {
        $this->pmModel = $pmModel;
        $this->assetModel = $assetModel;
        $this->userModel = $userModel;
        $this->workOrderModel = $workOrderModel;
    }

    /**
     * Get all schedules with optional filtering
     */
    public function getSchedules(?string $search = null, ?string $status = null): array
    {
        if ($search) {
            $schedules = $this->pmModel->searchSchedules($search);
        } else {
            $query = $this->pmModel->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number')
                                   ->join('assets', 'assets.id = preventive_maintenance.asset_id');

            if ($status === 'overdue') {
                $query->where('preventive_maintenance.next_due <', date('Y-m-d H:i:s'));
            } elseif ($status === 'upcoming') {
                $query->where('preventive_maintenance.next_due >=', date('Y-m-d H:i:s'))
                      ->where('preventive_maintenance.next_due <=', date('Y-m-d H:i:s', strtotime('+30 days')));
            }

            $schedules = $query->where('preventive_maintenance.is_active', 1)
                              ->orderBy('preventive_maintenance.next_due', 'ASC')
                              ->findAll();
        }

        return $schedules;
    }

    /**
     * Get maintenance statistics
     */
    public function getMaintenanceStatistics(): array
    {
        $schedules = $this->pmModel->findAll();
        $stats = $this->statisticsService->calculateStatistics($schedules);
        return $this->statisticsService->formatStatisticsForDisplay($stats);
    }

    /**
     * Create new preventive maintenance schedule
     */
    public function createSchedule(array $data): array
    {
        // Validate input data
        $sanitizedData = $this->validationService->sanitizeScheduleData($data);
        $validationErrors = $this->validationService->validateScheduleData($sanitizedData);

        if (!empty($validationErrors)) {
            return ['success' => false, 'errors' => $validationErrors];
        }

        // Calculate next due date
        $nextDue = $this->scheduleCalculationService->calculateNextDueDate(
            $sanitizedData['interval_type'],
            $sanitizedData['interval_value']
        );
        $sanitizedData['next_due'] = $nextDue->format('Y-m-d H:i:s');

        // Save to database
        if ($this->pmModel->save($sanitizedData)) {
            return ['success' => true, 'message' => 'Instandhaltungsplan wurde erfolgreich erstellt.'];
        }

        return ['success' => false, 'message' => 'Fehler beim Erstellen des Instandhaltungsplans.'];
    }

    /**
     * Update existing preventive maintenance schedule
     */
    public function updateSchedule(int $id, array $data): array
    {
        $schedule = $this->pmModel->find($id);
        if (!$schedule) {
            return ['success' => false, 'message' => 'Instandhaltungsplan nicht gefunden.'];
        }

        // Validate input data
        $sanitizedData = $this->validationService->sanitizeScheduleData($data);
        $validationErrors = $this->validationService->validateScheduleUpdate($schedule, $sanitizedData);

        if (!empty($validationErrors)) {
            return ['success' => false, 'errors' => $validationErrors];
        }

        // Save to database
        if ($this->pmModel->update($id, $sanitizedData)) {
            return ['success' => true, 'message' => 'Instandhaltungsplan wurde erfolgreich aktualisiert.'];
        }

        return ['success' => false, 'message' => 'Fehler beim Aktualisieren des Instandhaltungsplans.'];
    }

    /**
     * Get schedule details
     */
    public function getScheduleDetails(int $id): ?array
    {
        return $this->pmModel->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number, users.username as assigned_user')
                             ->join('assets', 'assets.id = preventive_maintenance.asset_id')
                             ->join('users', 'users.id = preventive_maintenance.assigned_user_id', 'left')
                             ->find($id);
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule(int $id): array
    {
        $schedule = $this->pmModel->find($id);
        if (!$schedule) {
            return ['success' => false, 'message' => 'Instandhaltungsplan nicht gefunden.'];
        }

        if ($this->pmModel->delete($id)) {
            return ['success' => true, 'message' => 'Instandhaltungsplan wurde erfolgreich gelöscht.'];
        }

        return ['success' => false, 'message' => 'Fehler beim Löschen des Instandhaltungsplans.'];
    }

    /**
     * Mark maintenance as completed
     */
    public function markAsCompleted(int $id, \DateTime $completedDate): array
    {
        $schedule = $this->pmModel->find($id);
        if (!$schedule) {
            return ['success' => false, 'message' => 'Instandhaltungsplan nicht gefunden.'];
        }

        if (!$this->validationService->canMarkAsCompleted($schedule)) {
            return ['success' => false, 'message' => 'Instandhaltung kann nicht als abgeschlossen markiert werden.'];
        }

        if ($this->pmModel->markAsCompleted($id, $completedDate)) {
            $updatedSchedule = $this->pmModel->find($id);
            return [
                'success' => true,
                'message' => 'Instandhaltung als abgeschlossen markiert.',
                'next_due' => $updatedSchedule['next_due']
            ];
        }

        return ['success' => false, 'message' => 'Fehler beim Markieren als abgeschlossen.'];
    }

    /**
     * Get completed work orders for schedule creation
     */
    public function getMaintenanceWorkOrders(): array
    {
        $maintenanceOrders = $this->workOrderModel
            ->select('work_orders.*, assets.name as asset_name, assets.asset_number')
            ->join('assets', 'assets.id = work_orders.asset_id', 'left')
            ->whereIn('work_orders.type', ['instandhaltung', 'inspektion'])
            ->orderBy('work_orders.created_at', 'DESC')
            ->limit(100)
            ->findAll();

        return ['success' => true, 'data' => $maintenanceOrders];
    }

    /**
     * Create schedules from selected work orders
     */
    public function createSchedulesFromWorkOrders(array $selectedOrders): array
    {
        // Validate input data
        $validationErrors = $this->workOrderGenerationService->validateWorkOrderGeneration($selectedOrders);
        if (!empty($validationErrors)) {
            return ['success' => false, 'errors' => $validationErrors];
        }

        $createdCount = 0;
        $errors = [];

        foreach ($selectedOrders as $orderData) {
            $workOrderId = $orderData['work_order_id'];
            $intervalType = $orderData['interval_type'];
            $intervalValue = $orderData['interval_value'];

            $workOrder = $this->workOrderModel->find($workOrderId);
            if (!$workOrder) {
                $errors[] = "Arbeitsauftrag mit ID {$workOrderId} nicht gefunden.";
                continue;
            }

            // Generate schedule data using business service
            $scheduleData = $this->workOrderGenerationService->createScheduleFromWorkOrder(
                $workOrder,
                $intervalType,
                $intervalValue
            );

            try {
                if ($this->pmModel->save($scheduleData)) {
                    $createdCount++;
                } else {
                    $errors[] = "Fehler beim Erstellen des Instandhaltungsplans für: {$workOrder['title']}";
                }
            } catch (\Exception $e) {
                $errors[] = "Fehler beim Erstellen des Instandhaltungsplans für {$workOrder['title']}: " . $e->getMessage();
            }
        }

        return [
            'success' => $createdCount > 0,
            'message' => "{$createdCount} Instandhaltungspläne wurden erfolgreich erstellt.",
            'created_count' => $createdCount,
            'errors' => $errors
        ];
    }

    /**
     * Generate work orders for due maintenance
     */
    public function generateWorkOrders(): array
    {
        $schedulesNeedingOrders = $this->pmModel->getSchedulesNeedingWorkOrders();
        $generatedCount = 0;

        foreach ($schedulesNeedingOrders as $schedule) {
            // Check if should generate using business logic
            if (!$this->workOrderGenerationService->shouldGenerateWorkOrder($schedule)) {
                continue;
            }

            // Check if work order already exists
            $existingOrder = $this->workOrderModel
                ->where('title', 'PM: ' . $schedule['schedule_name'])
                ->where('asset_id', $schedule['asset_id'])
                ->where('type', 'preventive')
                ->where('status', 'open')
                ->first();

            if ($existingOrder) {
                continue;
            }

            // Generate work order data using business service
            $workOrderData = $this->workOrderGenerationService->generateWorkOrderFromSchedule(
                $schedule,
                $this->workOrderModel->countAll()
            );

            if ($this->workOrderModel->save($workOrderData)) {
                $generatedCount++;
            }
        }

        return [
            'success' => true,
            'message' => "{$generatedCount} Arbeitsaufträge wurden generiert.",
            'count' => $generatedCount
        ];
    }

    /**
     * Get overdue schedules
     */
    public function getOverdueSchedules(): array
    {
        $schedules = $this->pmModel->findAll();
        return $this->statisticsService->getOverdueSchedules($schedules);
    }

    /**
     * Get upcoming schedules
     */
    public function getUpcomingSchedules(int $days = 30): array
    {
        $schedules = $this->pmModel->findAll();
        return $this->statisticsService->getUpcomingSchedules($schedules, $days);
    }

    /**
     * Get data for create/edit forms
     */
    public function getFormData(): array
    {
        return [
            'assets' => $this->assetModel->findAll(),
            'users' => $this->userModel->findAll()
        ];
    }

    /**
     * Get validation rules for frontend
     */
    public function getValidationRules(): array
    {
        return $this->validationService->getValidationRules();
    }
}