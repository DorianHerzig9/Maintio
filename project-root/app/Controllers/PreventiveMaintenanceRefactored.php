<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PreventiveMaintenanceModel;
use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\WorkOrderModel;
use CodeIgniter\HTTP\ResponseInterface;

// Import Business Services
use Src\Domain\ScheduleCalculationService;
use Src\Domain\MaintenanceStatisticsService;
use Src\Domain\WorkOrderGenerationService;
use Src\Domain\ScheduleValidationService;
use Src\Application\PreventiveMaintenanceApplicationService;

class PreventiveMaintenanceRefactored extends BaseController
{
    protected PreventiveMaintenanceApplicationService $applicationService;
    protected $pmModel;
    protected $assetModel;
    protected $userModel;
    protected $workOrderModel;

    public function __construct()
    {
        // Initialize models
        $this->pmModel = new PreventiveMaintenanceModel();
        $this->assetModel = new AssetModel();
        $this->userModel = new UserModel();
        $this->workOrderModel = new WorkOrderModel();

        // Initialize business services
        $scheduleCalculationService = new ScheduleCalculationService();
        $statisticsService = new MaintenanceStatisticsService($scheduleCalculationService);
        $workOrderGenerationService = new WorkOrderGenerationService($scheduleCalculationService);
        $validationService = new ScheduleValidationService($scheduleCalculationService);

        // Initialize application service
        $this->applicationService = new PreventiveMaintenanceApplicationService(
            $scheduleCalculationService,
            $statisticsService,
            $workOrderGenerationService,
            $validationService
        );

        // Inject data access dependencies
        $this->applicationService->setDataAccess(
            $this->pmModel,
            $this->assetModel,
            $this->userModel,
            $this->workOrderModel
        );
    }

    /**
     * Display list of maintenance schedules
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        // Delegate to application service
        $schedules = $this->applicationService->getSchedules($search, $status);
        $stats = $this->applicationService->getMaintenanceStatistics();

        $data = [
            'page_title' => 'Instandhaltung',
            'schedules' => $schedules,
            'search' => $search,
            'status' => $status,
            'stats' => $stats
        ];

        return view('preventive_maintenance/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $formData = $this->applicationService->getFormData();

        $data = [
            'page_title' => 'Instandhaltungsplan erstellen',
            'assets' => $formData['assets'],
            'users' => $formData['users'],
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
        $rules = $this->applicationService->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator);
        }

        // Delegate to application service
        $result = $this->applicationService->createSchedule($this->request->getPost());

        if ($result['success']) {
            return redirect()->to('/preventive-maintenance')
                           ->with('success', $result['message']);
        }

        return redirect()->back()
                       ->withInput()
                       ->with('error', $result['message'] ?? 'Fehler beim Erstellen des Instandhaltungsplans.');
    }

    /**
     * Show schedule details
     */
    public function show($id)
    {
        $schedule = $this->applicationService->getScheduleDetails($id);

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

        $formData = $this->applicationService->getFormData();

        $data = [
            'page_title' => 'Instandhaltungsplan bearbeiten',
            'assets' => $formData['assets'],
            'users' => $formData['users'],
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
        $rules = $this->applicationService->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator);
        }

        // Delegate to application service
        $result = $this->applicationService->updateSchedule($id, $this->request->getPost());

        if ($result['success']) {
            return redirect()->to('/preventive-maintenance')
                           ->with('success', $result['message']);
        }

        return redirect()->back()
                       ->withInput()
                       ->with('error', $result['message'] ?? 'Fehler beim Aktualisieren des Instandhaltungsplans.');
    }

    /**
     * Delete preventive maintenance schedule
     */
    public function delete($id)
    {
        // Delegate to application service
        $result = $this->applicationService->deleteSchedule($id);

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setStatusCode($result['success'] ? 200 : 404)
                                 ->setJSON($result);
        }

        if ($result['success']) {
            return redirect()->to('/preventive-maintenance')
                           ->with('success', $result['message']);
        } else {
            return redirect()->to('/preventive-maintenance')
                           ->with('error', $result['message']);
        }
    }

    /**
     * Mark maintenance as completed
     */
    public function markCompleted($id)
    {
        $completedDate = new \DateTime($this->request->getPost('completed_date') ?: 'now');

        // Delegate to application service
        $result = $this->applicationService->markAsCompleted($id, $completedDate);

        return $this->response->setJSON($result);
    }

    /**
     * Get maintenance and inspection work orders for selection
     */
    public function getCompletedWorkOrders()
    {
        // Delegate to application service
        $result = $this->applicationService->getMaintenanceWorkOrders();

        return $this->response->setJSON($result);
    }

    /**
     * Create preventive maintenance schedules from selected work orders
     */
    public function createSchedulesFromWorkOrders()
    {
        $selectedOrders = $this->request->getJSON(true)['selected_orders'] ?? [];

        // Delegate to application service
        $result = $this->applicationService->createSchedulesFromWorkOrders($selectedOrders);

        return $this->response->setJSON($result);
    }

    /**
     * Generate work orders for due maintenance
     */
    public function generateWorkOrders()
    {
        // Delegate to application service
        $result = $this->applicationService->generateWorkOrders();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        return redirect()->to('/preventive-maintenance')
                       ->with('success', $result['message']);
    }

    /**
     * Get overdue maintenance (API endpoint)
     */
    public function getOverdue()
    {
        $overdue = $this->applicationService->getOverdueSchedules();
        return $this->response->setJSON($overdue);
    }

    /**
     * Get upcoming maintenance (API endpoint)
     */
    public function getUpcoming($days = 30)
    {
        $upcoming = $this->applicationService->getUpcomingSchedules($days);
        return $this->response->setJSON($upcoming);
    }

    /**
     * Get maintenance statistics (API endpoint)
     */
    public function getStats()
    {
        $stats = $this->applicationService->getMaintenanceStatistics();
        return $this->response->setJSON($stats);
    }

    /**
     * Dashboard widget - upcoming preventive maintenance
     */
    public function dashboardWidget()
    {
        $upcomingMaintenance = $this->applicationService->getUpcomingSchedules(14);
        $overdueMaintenance = $this->applicationService->getOverdueSchedules();

        $data = [
            'upcoming_maintenance' => $upcomingMaintenance,
            'overdue_maintenance' => $overdueMaintenance
        ];

        return view('preventive_maintenance/dashboard_widget', $data);
    }
}