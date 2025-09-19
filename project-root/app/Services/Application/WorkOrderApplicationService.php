<?php

namespace App\Services\Application;

use App\Services\Business\WorkOrderBusinessService;

/**
 * WorkOrder Application Service
 *
 * Orchestriert Workflows und koordiniert Business Services.
 * Enthält keine Geschäftslogik, sondern definiert Abläufe.
 */
class WorkOrderApplicationService
{
    protected $workOrderBusinessService;

    public function __construct()
    {
        $this->workOrderBusinessService = new WorkOrderBusinessService();
    }

    /**
     * Workflow: Liste aller Arbeitsaufträge laden
     */
    public function getWorkOrdersList(array $filters = []): array
    {
        // Orchestrierung: Business Service aufrufen
        $workOrders = $this->workOrderBusinessService->getAllWorkOrdersWithDetails($filters);

        // Workflow: Additional processing could be added here
        // z.B. Caching, Logging, etc.

        return [
            'work_orders' => $workOrders,
            'status_filter' => $filters['status'] ?? null,
            'priority_filter' => $filters['priority'] ?? null
        ];
    }

    /**
     * Workflow: Arbeitsauftrag-Details laden
     */
    public function getWorkOrderDetails(int $id): array
    {
        // Orchestrierung: Business Service aufrufen
        return $this->workOrderBusinessService->getWorkOrderWithDetails($id);
    }

    /**
     * Workflow: Daten für Create-Form bereitstellen
     */
    public function getCreateFormData(): array
    {
        // Orchestrierung: Business Service aufrufen
        return $this->workOrderBusinessService->getFormData();
    }

    /**
     * Workflow: Neuen Arbeitsauftrag erstellen
     */
    public function createWorkOrder(array $requestData, ?int $currentUserId = null): array
    {
        try {
            // Orchestrierung: Business Service für Erstellung aufrufen
            $workOrderId = $this->workOrderBusinessService->createWorkOrder($requestData, $currentUserId);

            // Workflow: Zusätzliche Aktionen könnten hier orchestriert werden
            // z.B. Notifications, Logging, Events, etc.

            return [
                'success' => true,
                'work_order_id' => $workOrderId,
                'message' => 'Arbeitsauftrag erfolgreich erstellt'
            ];

        } catch (\Exception $e) {
            // Workflow: Fehlerbehandlung
            log_message('error', 'Error in createWorkOrder workflow: ' . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Workflow: Daten für Edit-Form bereitstellen
     */
    public function getEditFormData(int $id): array
    {
        // Orchestrierung: Details und Form-Daten laden
        $workOrder = $this->workOrderBusinessService->getWorkOrderWithDetails($id);
        $formData = $this->workOrderBusinessService->getFormData();

        return array_merge($workOrder, $formData);
    }

    /**
     * Workflow: Arbeitsauftrag aktualisieren
     */
    public function updateWorkOrder(int $id, array $requestData): array
    {
        try {
            // Orchestrierung: Business Service für Update aufrufen
            $success = $this->workOrderBusinessService->updateWorkOrder($id, $requestData);

            if ($success) {
                // Workflow: Zusätzliche Aktionen könnten hier orchestriert werden
                // z.B. Notifications, Audit Log, etc.

                return [
                    'success' => true,
                    'message' => 'Arbeitsauftrag erfolgreich aktualisiert'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Fehler beim Aktualisieren des Arbeitsauftrags'
                ];
            }

        } catch (\Exception $e) {
            // Workflow: Fehlerbehandlung
            log_message('error', 'Error in updateWorkOrder workflow: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Workflow: Arbeitsauftrag löschen
     */
    public function deleteWorkOrder(int $id): array
    {
        try {
            // Orchestrierung: Business Service für Löschung aufrufen
            $success = $this->workOrderBusinessService->deleteWorkOrder($id);

            if ($success) {
                // Workflow: Zusätzliche Aktionen könnten hier orchestriert werden
                // z.B. Cleanup, Notifications, etc.

                return [
                    'success' => true,
                    'message' => 'Arbeitsauftrag erfolgreich gelöscht'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Fehler beim Löschen des Arbeitsauftrags'
                ];
            }

        } catch (\Exception $e) {
            // Workflow: Fehlerbehandlung
            log_message('error', 'Error in deleteWorkOrder workflow: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Workflow: Arbeitsauftrag-Status aktualisieren
     */
    public function updateWorkOrderStatus(int $id, string $status): array
    {
        try {
            // Orchestrierung: Business Service für Status-Update aufrufen
            $success = $this->workOrderBusinessService->updateWorkOrderStatus($id, $status);

            if ($success) {
                // Workflow: Zusätzliche Aktionen könnten hier orchestriert werden
                // z.B. Status-Change Notifications, etc.

                return [
                    'success' => true,
                    'message' => 'Status erfolgreich aktualisiert'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Fehler beim Aktualisieren des Status'
                ];
            }

        } catch (\Exception $e) {
            // Workflow: Fehlerbehandlung
            log_message('error', 'Error in updateWorkOrderStatus workflow: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Workflow: Komponentenstatus aktualisieren
     */
    public function updateComponentStatus(int $workOrderId, int $componentId, string $status): array
    {
        try {
            // Orchestrierung: Business Service für Komponenten-Status-Update aufrufen
            $success = $this->workOrderBusinessService->updateComponentStatus($workOrderId, $componentId, $status);

            if ($success) {
                // Workflow: Zusätzliche Aktionen könnten hier orchestriert werden

                return [
                    'success' => true,
                    'message' => 'Komponentenstatus erfolgreich aktualisiert'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Fehler beim Aktualisieren des Komponentenstatus'
                ];
            }

        } catch (\Exception $e) {
            // Workflow: Fehlerbehandlung
            log_message('error', 'Error in updateComponentStatus workflow: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Workflow: Arbeitsaufträge suchen
     */
    public function searchWorkOrders(string $query): array
    {
        // Orchestrierung: Business Service für Suche aufrufen
        $results = $this->workOrderBusinessService->searchWorkOrders($query);

        // Workflow: Zusätzliche Verarbeitung könnte hier stattfinden
        // z.B. Search Analytics, Caching, etc.

        return $results;
    }
}