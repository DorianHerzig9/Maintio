<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\PreventiveMaintenanceModel;
use CodeIgniter\HTTP\ResponseInterface;

class Reports extends BaseController
{
    protected $workOrderModel;
    protected $assetModel;
    protected $userModel;
    protected $preventiveMaintenanceModel;

    public function __construct()
    {
        $this->workOrderModel = new WorkOrderModel();
        $this->assetModel = new AssetModel();
        $this->userModel = new UserModel();
        $this->preventiveMaintenanceModel = new PreventiveMaintenanceModel();
    }

    /**
     * Display reports dashboard
     */
    public function index()
    {
        $data = [
            'page_title' => 'Berichte & Analysen',
            'stats' => $this->getOverallStats(),
            'recentReports' => $this->getRecentReports()
        ];

        return view('reports/index', $data);
    }

    /**
     * Work Orders Report
     */
    public function workOrders()
    {
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
        $status = $this->request->getGet('status') ?? '';
        $technician = $this->request->getGet('technician') ?? '';

        $data = [
            'page_title' => 'Arbeitsaufträge Bericht',
            'workOrders' => $this->getWorkOrdersReport($dateFrom, $dateTo, $status, $technician),
            'stats' => $this->getWorkOrderStats($dateFrom, $dateTo),
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
                'technician' => $technician
            ],
            'technicians' => $this->userModel->getActiveTechnicians(),
            'statusOptions' => $this->getStatusOptions()
        ];

        return view('reports/work_orders', $data);
    }

    /**
     * Assets Report
     */
    public function assets()
    {
        $status = $this->request->getGet('status') ?? '';
        $type = $this->request->getGet('type') ?? '';
        $location = $this->request->getGet('location') ?? '';

        $data = [
            'page_title' => 'Anlagen Bericht',
            'assets' => $this->getAssetsReport($status, $type, $location),
            'stats' => $this->getAssetStats(),
            'filters' => [
                'status' => $status,
                'type' => $type,
                'location' => $location
            ],
            'assetTypes' => $this->getAssetTypes(),
            'locations' => $this->getAssetLocations()
        ];

        return view('reports/assets', $data);
    }

    /**
     * Maintenance Report
     */
    public function maintenance()
    {
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
        $type = $this->request->getGet('type') ?? '';

        $data = [
            'page_title' => 'Wartungsbericht',
            'maintenanceData' => $this->getMaintenanceReport($dateFrom, $dateTo, $type),
            'stats' => $this->getMaintenanceStats($dateFrom, $dateTo),
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'type' => $type
            ],
            'maintenanceTypes' => $this->getMaintenanceTypes()
        ];

        return view('reports/maintenance', $data);
    }

    /**
     * Performance Report
     */
    public function performance()
    {
        $period = $this->request->getGet('period') ?? 'month';
        $technician = $this->request->getGet('technician') ?? '';

        $data = [
            'page_title' => 'Leistungsbericht',
            'performanceData' => $this->getPerformanceReport($period, $technician),
            'filters' => [
                'period' => $period,
                'technician' => $technician
            ],
            'technicians' => $this->userModel->getActiveTechnicians(),
            'periodOptions' => $this->getPeriodOptions()
        ];

        return view('reports/performance', $data);
    }

    /**
     * Export Work Orders Report
     */
    public function exportWorkOrders()
    {
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
        $format = $this->request->getGet('format') ?? 'csv';

        $workOrders = $this->getWorkOrdersReport($dateFrom, $dateTo);

        switch ($format) {
            case 'csv':
                return $this->exportToCSV($workOrders, 'arbeitsauftraege_' . date('Y-m-d'));
            case 'pdf':
                return $this->exportToPDF($workOrders, 'Arbeitsaufträge Bericht');
            case 'excel':
                return $this->exportToExcel($workOrders, 'arbeitsauftraege_' . date('Y-m-d'));
            default:
                return redirect()->back()->with('error', 'Ungültiges Export-Format');
        }
    }

    /**
     * Export Assets Report
     */
    public function exportAssets()
    {
        $format = $this->request->getGet('format') ?? 'csv';
        $assets = $this->getAssetsReport();

        switch ($format) {
            case 'csv':
                return $this->exportToCSV($assets, 'anlagen_' . date('Y-m-d'));
            case 'pdf':
                return $this->exportToPDF($assets, 'Anlagen Bericht');
            case 'excel':
                return $this->exportToExcel($assets, 'anlagen_' . date('Y-m-d'));
            default:
                return redirect()->back()->with('error', 'Ungültiges Export-Format');
        }
    }

    /**
     * Get overall statistics
     */
    private function getOverallStats()
    {
        return [
            'total_work_orders' => $this->workOrderModel->countAll(),
            'open_work_orders' => $this->workOrderModel->where('status', 'open')->countAllResults(),
            'total_assets' => $this->assetModel->countAll(),
            'critical_assets' => $this->assetModel->where('status', 'critical')->countAllResults(),
            'total_users' => $this->userModel->countAll(),
            'active_technicians' => $this->userModel->where('role', 'technician')->where('is_active', 1)->countAllResults()
        ];
    }

    /**
     * Get recent reports
     */
    private function getRecentReports()
    {
        // This would typically come from a reports history table
        return [
            [
                'name' => 'Arbeitsaufträge - Oktober 2023',
                'type' => 'work_orders',
                'created_at' => '2023-11-01 10:30:00',
                'created_by' => 'Admin'
            ],
            [
                'name' => 'Anlagen Status',
                'type' => 'assets',
                'created_at' => '2023-11-01 09:15:00',
                'created_by' => 'Manager'
            ]
        ];
    }

    /**
     * Get work orders report data
     */
    private function getWorkOrdersReport($dateFrom = null, $dateTo = null, $status = null, $technician = null)
    {
        $builder = $this->workOrderModel
            ->select('work_orders.*, assets.asset_name, assets.asset_number, users.first_name, users.last_name')
            ->join('assets', 'work_orders.asset_id = assets.id', 'left')
            ->join('users', 'work_orders.assigned_to = users.id', 'left');

        if ($dateFrom) {
            $builder->where('work_orders.created_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('work_orders.created_at <=', $dateTo . ' 23:59:59');
        }

        if ($status) {
            $builder->where('work_orders.status', $status);
        }

        if ($technician) {
            $builder->where('work_orders.assigned_to', $technician);
        }

        return $builder->orderBy('work_orders.created_at', 'DESC')->findAll();
    }

    /**
     * Get work order statistics
     */
    private function getWorkOrderStats($dateFrom, $dateTo)
    {
        $builder = $this->workOrderModel;

        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        $total = $builder->countAllResults(false);
        $completed = $builder->where('status', 'completed')->countAllResults(false);
        $inProgress = $builder->where('status', 'in_progress')->countAllResults(false);
        $open = $builder->where('status', 'open')->countAllResults();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'open' => $open,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get assets report data
     */
    private function getAssetsReport($status = null, $type = null, $location = null)
    {
        $builder = $this->assetModel;

        if ($status) {
            $builder->where('status', $status);
        }

        if ($type) {
            $builder->where('asset_type', $type);
        }

        if ($location) {
            $builder->where('location', $location);
        }

        return $builder->orderBy('asset_name', 'ASC')->findAll();
    }

    /**
     * Get asset statistics
     */
    private function getAssetStats()
    {
        $total = $this->assetModel->countAll();
        $operational = $this->assetModel->where('status', 'operational')->countAllResults();
        $maintenance = $this->assetModel->where('status', 'maintenance')->countAllResults();
        $outOfService = $this->assetModel->where('status', 'out_of_service')->countAllResults();

        return [
            'total' => $total,
            'operational' => $operational,
            'maintenance' => $maintenance,
            'out_of_service' => $outOfService,
            'operational_rate' => $total > 0 ? round(($operational / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get maintenance report data
     */
    private function getMaintenanceReport($dateFrom, $dateTo, $type = null)
    {
        $builder = $this->preventiveMaintenanceModel
            ->select('preventive_maintenance.*, assets.asset_name, assets.asset_number')
            ->join('assets', 'preventive_maintenance.asset_id = assets.id', 'left');

        if ($dateFrom) {
            $builder->where('next_due_date >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('next_due_date <=', $dateTo);
        }

        return $builder->orderBy('next_due_date', 'ASC')->findAll();
    }

    /**
     * Get maintenance statistics
     */
    private function getMaintenanceStats($dateFrom, $dateTo)
    {
        $total = $this->preventiveMaintenanceModel->countAll();
        $overdue = $this->preventiveMaintenanceModel->where('next_due_date <', date('Y-m-d'))->countAllResults();
        $upcoming = $this->preventiveMaintenanceModel
            ->where('next_due_date >=', date('Y-m-d'))
            ->where('next_due_date <=', date('Y-m-d', strtotime('+30 days')))
            ->countAllResults();

        return [
            'total' => $total,
            'overdue' => $overdue,
            'upcoming' => $upcoming,
            'compliance_rate' => $total > 0 ? round((($total - $overdue) / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get performance report data
     */
    private function getPerformanceReport($period, $technician = null)
    {
        // This would include metrics like completion times, quality scores, etc.
        return [
            'technician_performance' => $this->getTechnicianPerformance($period, $technician),
            'asset_performance' => $this->getAssetPerformance($period),
            'maintenance_performance' => $this->getMaintenancePerformance($period)
        ];
    }

    /**
     * Export data to CSV
     */
    private function exportToCSV($data, $filename)
    {
        $response = $this->response;
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');

        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys($data[0]));

            // Write data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        return $response;
    }

    /**
     * Export data to PDF
     */
    private function exportToPDF($data, $title)
    {
        // PDF export would be implemented here using a library like TCPDF or mPDF
        return redirect()->back()->with('info', 'PDF Export wird in einer zukünftigen Version verfügbar sein');
    }

    /**
     * Export data to Excel
     */
    private function exportToExcel($data, $filename)
    {
        // Excel export would be implemented here using PhpSpreadsheet
        return redirect()->back()->with('info', 'Excel Export wird in einer zukünftigen Version verfügbar sein');
    }

    /**
     * Helper methods for getting options
     */
    private function getStatusOptions()
    {
        return [
            'open' => 'Offen',
            'in_progress' => 'In Bearbeitung',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Storniert'
        ];
    }

    private function getAssetTypes()
    {
        return $this->assetModel->select('asset_type')->distinct()->findAll();
    }

    private function getAssetLocations()
    {
        return $this->assetModel->select('location')->distinct()->findAll();
    }

    private function getMaintenanceTypes()
    {
        return [
            'preventive' => 'Präventiv',
            'corrective' => 'Korrektiv',
            'predictive' => 'Prädiktiv'
        ];
    }

    private function getPeriodOptions()
    {
        return [
            'week' => 'Diese Woche',
            'month' => 'Dieser Monat',
            'quarter' => 'Dieses Quartal',
            'year' => 'Dieses Jahr'
        ];
    }

    private function getTechnicianPerformance($period, $technician)
    {
        // Implementation for technician performance metrics
        return [];
    }

    private function getAssetPerformance($period)
    {
        // Implementation for asset performance metrics
        return [];
    }

    private function getMaintenancePerformance($period)
    {
        // Implementation for maintenance performance metrics
        return [];
    }
}