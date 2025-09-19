<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PreventiveMaintenanceModel;
use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\WorkOrderModel;
use CodeIgniter\HTTP\ResponseInterface;

class PreventiveMaintenance extends BaseController
{
    protected $pmModel;
    protected $assetModel;
    protected $userModel;
    protected $workOrderModel;

    public function __construct()
    {
        $this->pmModel = new PreventiveMaintenanceModel();
        $this->assetModel = new AssetModel();
        $this->userModel = new UserModel();
        $this->workOrderModel = new WorkOrderModel();
    }

    /**
     * Display list of maintenance schedules
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        
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

        $data = [
            'page_title' => 'Instandhaltung',
            'schedules' => $schedules,
            'search' => $search,
            'status' => $status,
            'stats' => $this->pmModel->getMaintenanceStatistics()
        ];

        return view('preventive_maintenance/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = [
            'page_title' => 'Instandhaltungsplan erstellen',
            'assets' => $this->assetModel->findAll(),
            'users' => $this->userModel->findAll(),
            'schedule' => [],
            'validation' => null
        ];

        return view('preventive_maintenance/create', $data);
    }

    /**
     * Store new preventive maintenance schedule
     */
    public function store()
    {
        $rules = [
            'asset_id' => 'required|integer',
            'schedule_name' => 'required|max_length[200]',
            'interval_type' => 'required|in_list[daily,weekly,monthly,quarterly,annually,hours,cycles,kilometers]',
            'interval_value' => 'required|integer|greater_than[0]',
            'priority' => 'required|in_list[low,medium,high,critical]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator);
        }

        $data = [
            'asset_id' => $this->request->getPost('asset_id'),
            'schedule_name' => $this->request->getPost('schedule_name'),
            'description' => $this->request->getPost('description'),
            'task_details' => $this->request->getPost('task_details'),
            'interval_type' => $this->request->getPost('interval_type'),
            'interval_value' => $this->request->getPost('interval_value'),
            'priority' => $this->request->getPost('priority'),
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'auto_generate_work_orders' => $this->request->getPost('auto_generate_work_orders') ? 1 : 0,
            'lead_time_days' => $this->request->getPost('lead_time_days') ?: 7,
            'assigned_user_id' => $this->request->getPost('assigned_user_id') ?: null,
            'category' => $this->request->getPost('category'),
            'required_tools' => $this->request->getPost('required_tools'),
            'required_parts' => $this->request->getPost('required_parts'),
            'safety_notes' => $this->request->getPost('safety_notes'),
            'is_active' => 1
        ];

        if ($this->pmModel->save($data)) {
            return redirect()->to('/preventive-maintenance')
                           ->with('success', 'Instandhaltungsplan wurde erfolgreich erstellt.');
        }

        return redirect()->back()
                       ->withInput()
                       ->with('error', 'Fehler beim Erstellen des Instandhaltungsplans.');
    }

    /**
     * Show schedule details
     */
    public function show($id)
    {
        $schedule = $this->pmModel->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number, users.username as assigned_user')
                                 ->join('assets', 'assets.id = preventive_maintenance.asset_id')
                                 ->join('users', 'users.id = preventive_maintenance.assigned_user_id', 'left')
                                 ->find($id);

        if (!$schedule) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'page_title' => 'Instandhaltungsplan Details',
            'schedule' => $schedule
        ];

        return view('preventive_maintenance/show', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $schedule = $this->pmModel->find($id);

        if (!$schedule) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'page_title' => 'Instandhaltungsplan bearbeiten',
            'assets' => $this->assetModel->findAll(),
            'users' => $this->userModel->findAll(),
            'schedule' => $schedule,
            'validation' => null
        ];

        return view('preventive_maintenance/edit', $data);
    }

    /**
     * Update preventive maintenance schedule
     */
    public function update($id)
    {
        $schedule = $this->pmModel->find($id);

        if (!$schedule) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'asset_id' => 'required|integer',
            'schedule_name' => 'required|max_length[200]',
            'interval_type' => 'required|in_list[daily,weekly,monthly,quarterly,annually,hours,cycles,kilometers]',
            'interval_value' => 'required|integer|greater_than[0]',
            'priority' => 'required|in_list[low,medium,high,critical]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator);
        }

        $data = [
            'asset_id' => $this->request->getPost('asset_id'),
            'schedule_name' => $this->request->getPost('schedule_name'),
            'description' => $this->request->getPost('description'),
            'task_details' => $this->request->getPost('task_details'),
            'interval_type' => $this->request->getPost('interval_type'),
            'interval_value' => $this->request->getPost('interval_value'),
            'priority' => $this->request->getPost('priority'),
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'auto_generate_work_orders' => $this->request->getPost('auto_generate_work_orders') ? 1 : 0,
            'lead_time_days' => $this->request->getPost('lead_time_days') ?: 7,
            'assigned_user_id' => $this->request->getPost('assigned_user_id') ?: null,
            'category' => $this->request->getPost('category'),
            'required_tools' => $this->request->getPost('required_tools'),
            'required_parts' => $this->request->getPost('required_parts'),
            'safety_notes' => $this->request->getPost('safety_notes'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->pmModel->update($id, $data)) {
            return redirect()->to('/preventive-maintenance')
                           ->with('success', 'Instandhaltungsplan wurde erfolgreich aktualisiert.');
        }

        return redirect()->back()
                       ->withInput()
                       ->with('error', 'Fehler beim Aktualisieren des Instandhaltungsplans.');
    }

    /**
     * Delete preventive maintenance schedule
     */
    public function delete($id)
    {
        $schedule = $this->pmModel->find($id);

        if (!$schedule) {
            if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                return $this->response->setStatusCode(404)
                                     ->setJSON(['success' => false, 'message' => 'Instandhaltungsplan nicht gefunden']);
            }
            return redirect()->to('/preventive-maintenance')
                           ->with('error', 'Instandhaltungsplan nicht gefunden.');
        }

        try {
            if ($this->pmModel->delete($id)) {
                if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                    return $this->response->setStatusCode(200)
                                         ->setJSON(['success' => true, 'message' => 'Instandhaltungsplan wurde erfolgreich gelöscht']);
                }
                return redirect()->to('/preventive-maintenance')
                               ->with('success', 'Instandhaltungsplan wurde erfolgreich gelöscht.');
            } else {
                if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                    return $this->response->setStatusCode(500)
                                         ->setJSON(['success' => false, 'message' => 'Fehler beim Löschen des Instandhaltungsplans']);
                }
                return redirect()->to('/preventive-maintenance')
                               ->with('error', 'Fehler beim Löschen des Instandhaltungsplans.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting preventive maintenance ' . $id . ': ' . $e->getMessage());
            if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                return $this->response->setStatusCode(500)
                                     ->setJSON(['success' => false, 'message' => 'Fehler beim Löschen des Instandhaltungsplans: ' . $e->getMessage()]);
            }
            return redirect()->to('/preventive-maintenance')
                           ->with('error', 'Fehler beim Löschen des Instandhaltungsplans.');
        }
    }

    /**
     * Mark maintenance as completed
     */
    public function markCompleted($id)
    {
        $schedule = $this->pmModel->find($id);

        if (!$schedule) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Instandhaltungsplan nicht gefunden.'
            ]);
        }

        $completedDate = new \DateTime($this->request->getPost('completed_date') ?: 'now');

        if ($this->pmModel->markAsCompleted($id, $completedDate)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Instandhaltung als abgeschlossen markiert.',
                'next_due' => $this->pmModel->find($id)['next_due']
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Fehler beim Markieren als abgeschlossen.'
        ]);
    }

    /**
     * Show modal with maintenance and inspection work orders for selection
     */
    public function getCompletedWorkOrders()
    {
        $maintenanceOrders = $this->workOrderModel
            ->select('work_orders.*, assets.name as asset_name, assets.asset_number')
            ->join('assets', 'assets.id = work_orders.asset_id', 'left')
            ->whereIn('work_orders.type', ['instandhaltung', 'inspektion'])
            ->orderBy('work_orders.created_at', 'DESC')
            ->limit(100)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $maintenanceOrders
        ]);
    }

    /**
     * Create preventive maintenance schedules from selected work orders
     */
    public function createSchedulesFromWorkOrders()
    {
        $selectedOrders = $this->request->getJSON(true)['selected_orders'] ?? [];
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

            // Calculate next due date based on interval
            $nextDue = $this->calculateNextDueDate($intervalType, $intervalValue);

            $scheduleData = [
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
                'next_due' => $nextDue,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

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

        return $this->response->setJSON([
            'success' => $createdCount > 0,
            'message' => "{$createdCount} Instandhaltungspläne wurden erfolgreich erstellt.",
            'created_count' => $createdCount,
            'errors' => $errors
        ]);
    }

    /**
     * Calculate next due date based on interval
     */
    private function calculateNextDueDate($intervalType, $intervalValue)
    {
        $now = new \DateTime();

        switch ($intervalType) {
            case 'daily':
                $now->add(new \DateInterval("P{$intervalValue}D"));
                break;
            case 'weekly':
                $now->add(new \DateInterval("P" . ($intervalValue * 7) . "D"));
                break;
            case 'monthly':
                $now->add(new \DateInterval("P{$intervalValue}M"));
                break;
            case 'quarterly':
                $now->add(new \DateInterval("P" . ($intervalValue * 3) . "M"));
                break;
            case 'annually':
                $now->add(new \DateInterval("P{$intervalValue}Y"));
                break;
            default:
                $now->add(new \DateInterval("P30D")); // Default to 30 days
                break;
        }

        return $now->format('Y-m-d H:i:s');
    }

    /**
     * Generate work orders for due maintenance
     */
    public function generateWorkOrders()
    {
        $schedulesNeedingOrders = $this->pmModel->getSchedulesNeedingWorkOrders();
        $generatedCount = 0;

        foreach ($schedulesNeedingOrders as $schedule) {
            // Check if work order already exists for this schedule and due date
            $existingOrder = $this->workOrderModel
                ->where('title', 'PM: ' . $schedule['schedule_name'])
                ->where('asset_id', $schedule['asset_id'])
                ->where('type', 'preventive')
                ->where('status', 'open')
                ->first();

            if ($existingOrder) {
                continue; // Skip if already exists
            }

            // Generate work order number
            $workOrderNumber = 'PM-' . date('Y') . '-' . str_pad($this->workOrderModel->countAll() + 1, 4, '0', STR_PAD_LEFT);

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
                'notes' => "Automatisch generiert für Instandhaltungsplan: {$schedule['schedule_name']}\n\n" .
                          "Aufgabendetails:\n{$schedule['task_details']}\n\n" .
                          ($schedule['required_tools'] ? "Benötigte Werkzeuge:\n{$schedule['required_tools']}\n\n" : '') .
                          ($schedule['required_parts'] ? "Benötigte Ersatzteile:\n{$schedule['required_parts']}\n\n" : '') .
                          ($schedule['safety_notes'] ? "Sicherheitshinweise:\n{$schedule['safety_notes']}" : '')
            ];

            if ($this->workOrderModel->save($workOrderData)) {
                $generatedCount++;
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "{$generatedCount} Arbeitsaufträge wurden generiert.",
                'count' => $generatedCount
            ]);
        }

        return redirect()->to('/preventive-maintenance')
                       ->with('success', "{$generatedCount} Arbeitsaufträge wurden automatisch generiert.");
    }

    /**
     * Get overdue maintenance (API endpoint)
     */
    public function getOverdue()
    {
        $overdue = $this->pmModel->getOverdueSchedules();
        return $this->response->setJSON($overdue);
    }

    /**
     * Get upcoming maintenance (API endpoint)
     */
    public function getUpcoming($days = 30)
    {
        $upcoming = $this->pmModel->getUpcomingSchedules($days);
        return $this->response->setJSON($upcoming);
    }

    /**
     * Get maintenance statistics (API endpoint)
     */
    public function getStats()
    {
        $stats = $this->pmModel->getMaintenanceStatistics();
        return $this->response->setJSON($stats);
    }

    /**
     * Dashboard widget - upcoming preventive maintenance
     */
    public function dashboardWidget()
    {
        $upcomingMaintenance = $this->pmModel->getUpcomingSchedules(14); // Next 14 days
        $overdueMaintenance = $this->pmModel->getOverdueSchedules();

        $data = [
            'upcoming_maintenance' => $upcomingMaintenance,
            'overdue_maintenance' => $overdueMaintenance
        ];

        return view('preventive_maintenance/dashboard_widget', $data);
    }
}