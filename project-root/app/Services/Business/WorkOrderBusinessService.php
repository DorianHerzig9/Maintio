<?php

namespace App\Services\Business;

use App\Models\WorkOrderModel;
use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\WorkOrderComponentModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * WorkOrder Business Service
 *
 * Enthält die gesamte Geschäftslogik für Arbeitsaufträge:
 * - Validierungen
 * - Geschäftsregeln
 * - Berechnungen
 * - Status-Logik
 */
class WorkOrderBusinessService
{
    protected $workOrderModel;
    protected $assetModel;
    protected $userModel;
    protected $componentModel;

    public function __construct()
    {
        $this->workOrderModel = new WorkOrderModel();
        $this->assetModel = new AssetModel();
        $this->userModel = new UserModel();
        $this->componentModel = new WorkOrderComponentModel();
    }

    /**
     * Alle Arbeitsaufträge mit Details laden
     */
    public function getAllWorkOrdersWithDetails(array $filters = [])
    {
        return $this->workOrderModel->getWorkOrdersWithDetails();
    }

    /**
     * Arbeitsauftrag mit vollständigen Details laden
     */
    public function getWorkOrderWithDetails(int $id): array
    {
        $workOrder = $this->workOrderModel->find($id);

        if (!$workOrder) {
            throw new PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        // Business Logic: Verwandte Daten laden
        $components = $this->componentModel->getComponentsByWorkOrder($id);
        $componentStats = $this->componentModel->getComponentStats($id);
        $asset = $workOrder['asset_id'] ? $this->assetModel->find($workOrder['asset_id']) : null;
        $assignedUser = $workOrder['assigned_user_id'] ? $this->userModel->getUserSafe($workOrder['assigned_user_id']) : null;
        $createdBy = $this->userModel->getUserSafe($workOrder['created_by_user_id']);

        return [
            'work_order' => $workOrder,
            'components' => $components,
            'component_stats' => $componentStats,
            'asset' => $asset,
            'assigned_user' => $assignedUser,
            'created_by' => $createdBy
        ];
    }

    /**
     * Neuen Arbeitsauftrag erstellen mit Geschäftslogik
     */
    public function createWorkOrder(array $data, ?int $currentUserId = null): int
    {
        // Business Logic: Standardwerte setzen
        $workOrderData = [
            'title' => esc($data['title']),
            'description' => esc($data['description'] ?? ''),
            'type' => $data['type'],
            'status' => 'open', // Business Rule: Neue Aufträge sind immer "open"
            'priority' => $data['priority'],
            'asset_id' => $data['asset_id'] ?: null,
            'assigned_user_id' => $data['assigned_user_id'] ?: null,
            'created_by_user_id' => $currentUserId ?? 1, // Business Rule: Fallback User
            'scheduled_date' => $data['scheduled_date'] ?: null,
            'estimated_duration' => $data['estimated_duration'] ?: null
        ];

        // Business Logic: Validation der Geschäftsregeln
        $this->validateWorkOrderData($workOrderData);

        $workOrderId = $this->workOrderModel->insert($workOrderData);

        if (!$workOrderId) {
            throw new \RuntimeException('Fehler beim Erstellen des Arbeitsauftrags');
        }

        // Business Logic: Komponenten hinzufügen falls vorhanden
        if (!empty($data['components']) && is_array($data['components'])) {
            $this->addComponentsToWorkOrder($workOrderId, $data['components']);
        }

        return $workOrderId;
    }

    /**
     * Arbeitsauftrag aktualisieren mit Status-Logik
     */
    public function updateWorkOrder(int $id, array $data): bool
    {
        $workOrder = $this->workOrderModel->find($id);

        if (!$workOrder) {
            throw new PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        $updateData = [
            'title' => esc($data['title']),
            'description' => esc($data['description'] ?? ''),
            'type' => $data['type'],
            'status' => $data['status'],
            'priority' => $data['priority'],
            'asset_id' => $data['asset_id'] ?: null,
            'assigned_user_id' => $data['assigned_user_id'] ?: null,
            'scheduled_date' => $data['scheduled_date'] ?: null,
            'estimated_duration' => $data['estimated_duration'] ?: null,
            'actual_duration' => $data['actual_duration'] ?: null,
            'notes' => esc($data['notes'] ?? '')
        ];

        // Business Logic: Status-spezifische Regeln
        $updateData = $this->applyStatusLogic($updateData, $workOrder);

        return $this->workOrderModel->update($id, $updateData);
    }

    /**
     * Arbeitsauftrag löschen
     */
    public function deleteWorkOrder(int $id): bool
    {
        $workOrder = $this->workOrderModel->find($id);

        if (!$workOrder) {
            throw new PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        // Business Logic: Prüfen ob löschbar (z.B. nicht wenn in_progress)
        if ($workOrder['status'] === 'in_progress') {
            throw new \RuntimeException('Arbeitsauftrag in Bearbeitung kann nicht gelöscht werden');
        }

        return $this->workOrderModel->delete($id);
    }

    /**
     * Status eines Arbeitsauftrags aktualisieren
     */
    public function updateWorkOrderStatus(int $id, string $status): bool
    {
        $workOrder = $this->workOrderModel->find($id);

        if (!$workOrder) {
            throw new PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        // Business Logic: Valide Status-Übergänge prüfen
        $this->validateStatusTransition($workOrder['status'], $status);

        $data = ['status' => $status];

        // Business Logic: Status-spezifische Zeitstempel
        if ($status === 'in_progress' && !$workOrder['started_at']) {
            $data['started_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'completed' && !$workOrder['completed_at']) {
            $data['completed_at'] = date('Y-m-d H:i:s');

            // Business Logic: Automatische Berechnung der tatsächlichen Dauer
            if ($workOrder['started_at']) {
                $start = new \DateTime($workOrder['started_at']);
                $end = new \DateTime();
                $data['actual_duration'] = $end->getTimestamp() - $start->getTimestamp();
            }
        }

        return $this->workOrderModel->update($id, $data);
    }

    /**
     * Komponentenstatus aktualisieren
     */
    public function updateComponentStatus(int $workOrderId, int $componentId, string $status): bool
    {
        $component = $this->componentModel->find($componentId);

        if (!$component || $component['work_order_id'] != $workOrderId) {
            throw new PageNotFoundException('Komponente nicht gefunden');
        }

        return $this->componentModel->updateComponentStatus($componentId, $status);
    }

    /**
     * Arbeitsaufträge suchen
     */
    public function searchWorkOrders(string $query): array
    {
        // Business Logic: Mindestlänge für Suche
        if (strlen(trim($query)) < 2) {
            return [];
        }

        return $this->workOrderModel->searchWorkOrders($query);
    }

    /**
     * Daten für Create/Edit Forms bereitstellen
     */
    public function getFormData(): array
    {
        return [
            'assets' => $this->assetModel->findAll(),
            'users' => $this->userModel->findAll()
        ];
    }

    /**
     * PRIVATE BUSINESS LOGIC METHODS
     */

    /**
     * Geschäftsregeln für Arbeitsauftrag-Daten validieren
     */
    private function validateWorkOrderData(array $data): void
    {
        // Business Rule: Titel ist pflicht und nicht leer
        if (empty(trim($data['title']))) {
            throw new \InvalidArgumentException('Titel darf nicht leer sein');
        }

        // Business Rule: Bei kritischer Priorität muss ein User zugewiesen werden
        if ($data['priority'] === 'critical' && empty($data['assigned_user_id'])) {
            throw new \InvalidArgumentException('Kritische Arbeitsaufträge müssen einem Benutzer zugewiesen werden');
        }

        // Business Rule: Scheduled Date darf nicht in der Vergangenheit liegen
        if (!empty($data['scheduled_date'])) {
            $scheduledDate = new \DateTime($data['scheduled_date']);
            $today = new \DateTime('today');

            if ($scheduledDate < $today) {
                throw new \InvalidArgumentException('Geplantes Datum darf nicht in der Vergangenheit liegen');
            }
        }
    }

    /**
     * Status-spezifische Logik anwenden
     */
    private function applyStatusLogic(array $data, array $currentWorkOrder): array
    {
        // Business Logic: Status-spezifische Zeitstempel
        if ($data['status'] === 'in_progress' && !$currentWorkOrder['started_at']) {
            $data['started_at'] = date('Y-m-d H:i:s');
        } elseif ($data['status'] === 'completed' && !$currentWorkOrder['completed_at']) {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * Status-Übergänge validieren
     */
    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $validTransitions = [
            'open' => ['in_progress', 'cancelled', 'on_hold'],
            'in_progress' => ['completed', 'on_hold', 'cancelled'],
            'on_hold' => ['in_progress', 'cancelled'],
            'completed' => [], // Completed ist final
            'cancelled' => ['open'] // Cancelled kann wieder geöffnet werden
        ];

        if (!isset($validTransitions[$currentStatus]) ||
            !in_array($newStatus, $validTransitions[$currentStatus])) {
            throw new \InvalidArgumentException("Status-Übergang von '$currentStatus' zu '$newStatus' ist nicht erlaubt");
        }
    }

    /**
     * Komponenten zu Arbeitsauftrag hinzufügen
     */
    private function addComponentsToWorkOrder(int $workOrderId, array $components): void
    {
        $validComponents = [];

        foreach ($components as $component) {
            // Business Logic: Komponenten-Validierung
            if (!empty($component['kks_number']) && !empty($component['component_name'])) {
                $validComponents[] = [
                    'kks_number' => esc(trim($component['kks_number'])),
                    'component_name' => esc(trim($component['component_name'])),
                    'description' => !empty($component['description']) ? esc(trim($component['description'])) : null
                ];
            }
        }

        if (!empty($validComponents)) {
            $result = $this->componentModel->addComponentsToWorkOrder($workOrderId, $validComponents);
            if (!$result) {
                log_message('error', 'Failed to add components: ' . print_r($this->componentModel->errors(), true));
                throw new \RuntimeException('Fehler beim Hinzufügen der Komponenten');
            }
        }
    }
}