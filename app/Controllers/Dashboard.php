<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AssetModel;
use App\Models\WorkOrderModel;
use App\Models\PreventiveMaintenanceModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $assetModel;
    protected $workOrderModel;
    protected $pmModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->assetModel = new AssetModel();
        $this->workOrderModel = new WorkOrderModel();
        $this->pmModel = new PreventiveMaintenanceModel();
    }

    public function index()
    {
        // Dashboard Statistiken sammeln - optimiert für weniger DB-Queries
        $data = [
            'page_title' => 'Maintio Dashboard',
            'stats' => $this->getDashboardStats(),
            'recent_work_orders' => $this->getRecentWorkOrders(),
            'critical_assets' => $this->getCriticalAssets(),
            'upcoming_tasks' => $this->getUpcomingTasks()
        ];

        // Batch-Abfrage für alle Work Order Daten
        $workOrderData = $this->getWorkOrderDashboardData();
        $data = array_merge($data, $workOrderData);

        // Batch-Abfrage für alle Maintenance Daten
        $maintenanceData = $this->getMaintenanceDashboardData();
        $data = array_merge($data, $maintenanceData);

        return view('dashboard/index', $data);
    }

    private function getDashboardStats()
    {
        $workOrderStats = $this->workOrderModel->getWorkOrderStatistics();
        $assetStats = $this->assetModel->getAssetStatistics();
        
        // Berechne KPIs
        $totalWorkOrders = $workOrderStats['total'];
        $completedOrders = 0;
        $openOrders = 0;
        $criticalOrders = 0;

        foreach ($workOrderStats['by_status'] as $status) {
            if ($status['status'] === 'completed') {
                $completedOrders = $status['count'];
            } elseif ($status['status'] === 'open') {
                $openOrders = $status['count'];
            }
        }

        foreach ($workOrderStats['by_priority'] as $priority) {
            if ($priority['priority'] === 'critical') {
                $criticalOrders = $priority['count'];
            }
        }

        $completionRate = $totalWorkOrders > 0 ? round(($completedOrders / $totalWorkOrders) * 100, 1) : 0;

        return [
            'total_work_orders' => $totalWorkOrders,
            'open_work_orders' => $openOrders,
            'completed_work_orders' => $completedOrders,
            'critical_work_orders' => $criticalOrders,
            'completion_rate' => $completionRate,
            'total_assets' => $assetStats['total'],
            'work_order_stats' => $workOrderStats,
            'asset_stats' => $assetStats
        ];
    }

    private function getRecentWorkOrders()
    {
        return $this->workOrderModel->orderBy('created_at', 'DESC')->limit(DASHBOARD_RECENT_WORK_ORDERS_LIMIT)->findAll();
    }

    private function getCriticalAssets()
    {
        return $this->assetModel->getCriticalAssets();
    }

    private function getUpcomingTasks()
    {
        return $this->workOrderModel
                    ->where('status', 'open')
                    ->where('scheduled_date >=', date('Y-m-d'))
                    ->orderBy('scheduled_date', 'ASC')
                    ->limit(DASHBOARD_RECENT_WORK_ORDERS_LIMIT)
                    ->findAll();
    }

    public function getStats()
    {
        // API Endpoint für AJAX-Aufrufe
        return $this->response->setJSON($this->getDashboardStats());
    }

    /**
     * Optimierte Batch-Abfrage für Work Order Dashboard-Daten
     */
    private function getWorkOrderDashboardData()
    {
        $today = date('Y-m-d');
        $weekFromNow = date('Y-m-d', strtotime('+' . DASHBOARD_DUE_SOON_DAYS . ' days'));

        // Separate Queries für bessere SQL-Kompatibilität mit JOINs
        $overdueQuery = "
            SELECT 'overdue' as type, wo.*, a.name as asset_name, a.asset_number
            FROM work_orders wo
            LEFT JOIN assets a ON wo.asset_id = a.id
            WHERE wo.scheduled_date < ? AND wo.status != 'completed'
            ORDER BY wo.scheduled_date ASC
            LIMIT " . DASHBOARD_OVERDUE_WORK_ORDERS_LIMIT;

        $dueSoonQuery = "
            SELECT 'due_soon' as type, wo.*, a.name as asset_name, a.asset_number
            FROM work_orders wo
            LEFT JOIN assets a ON wo.asset_id = a.id
            WHERE wo.scheduled_date BETWEEN ? AND ? AND wo.status != 'completed'
            ORDER BY wo.scheduled_date ASC
            LIMIT " . DASHBOARD_DUE_SOON_WORK_ORDERS_LIMIT;

        $overdueResults = $this->workOrderModel->db->query($overdueQuery, [$today])->getResultArray();
        $dueSoonResults = $this->workOrderModel->db->query($dueSoonQuery, [$today, $weekFromNow])->getResultArray();

        // Verarbeite die Ergebnisse
        $overdue = [];
        $dueSoon = [];

        foreach ($overdueResults as $row) {
            unset($row['type']);
            $overdue[] = $row;
        }

        foreach ($dueSoonResults as $row) {
            unset($row['type']);
            $dueSoon[] = $row;
        }

        return [
            'overdue_work_orders' => $overdue,
            'due_soon_work_orders' => $dueSoon
        ];
    }

    /**
     * Optimierte Batch-Abfrage für Maintenance Dashboard-Daten
     */
    private function getMaintenanceDashboardData()
    {
        $today = date('Y-m-d');
        $twoWeeksFromNow = date('Y-m-d', strtotime('+' . DASHBOARD_UPCOMING_MAINTENANCE_DAYS . ' days'));

        // Eine einzige Query für beide Maintenance-Typen
        $query = "
            SELECT
                CASE
                    WHEN pm.next_due < ? THEN 'overdue'
                    ELSE 'upcoming'
                END as type,
                pm.*, a.name as asset_name, a.asset_number
            FROM preventive_maintenance pm
            LEFT JOIN assets a ON pm.asset_id = a.id
            WHERE pm.next_due <= ? AND pm.is_active = 1
            ORDER BY pm.next_due ASC
        ";

        $results = $this->pmModel->db->query($query, [$today, $twoWeeksFromNow])->getResultArray();

        $overdue = [];
        $upcoming = [];

        foreach ($results as $row) {
            if ($row['type'] === 'overdue') {
                unset($row['type']);
                $overdue[] = $row;
            } else {
                unset($row['type']);
                $upcoming[] = $row;
            }
        }

        return [
            'overdue_maintenance' => $overdue,
            'upcoming_maintenance' => $upcoming
        ];
    }
}
