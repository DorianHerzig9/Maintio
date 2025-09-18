<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\PreventiveMaintenanceModel;
use App\Libraries\PDFExporter;
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
     * Custom Reports Builder
     */
    public function custom()
    {
        $data = [
            'page_title' => 'Benutzerdefinierte Berichte',
            'reportTypes' => $this->getCustomReportTypes(),
            'fields' => $this->getAvailableFields(),
            'filterOptions' => $this->getFilterOptions()
        ];

        return view('reports/custom', $data);
    }

    /**
     * Generate Custom Report
     */
    public function generateCustom()
    {
        $reportType = $this->request->getPost('report_type');
        $selectedFields = $this->request->getPost('fields') ?? [];
        $filters = $this->request->getPost('filters') ?? [];
        $reportName = $this->request->getPost('report_name') ?? 'Benutzerdefinierter Bericht';

        $data = $this->buildCustomReport($reportType, $selectedFields, $filters);

        $result = [
            'page_title' => $reportName,
            'reportData' => $data,
            'reportType' => $reportType,
            'selectedFields' => $selectedFields,
            'filters' => $filters,
            'reportName' => $reportName
        ];

        return view('reports/custom_result', $result);
    }

    /**
     * Scheduled Reports Management
     */
    public function scheduled()
    {
        $data = [
            'page_title' => 'Geplante Berichte',
            'scheduledReports' => $this->getScheduledReports(),
            'reportTypes' => $this->getCustomReportTypes(),
            'scheduleOptions' => $this->getScheduleOptions(),
            'users' => $this->userModel->findAll()
        ];

        return view('reports/scheduled', $data);
    }

    /**
     * Create Scheduled Report
     */
    public function createScheduled()
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'report_type' => $this->request->getPost('report_type'),
            'schedule_type' => $this->request->getPost('schedule_type'),
            'schedule_value' => $this->request->getPost('schedule_value'),
            'recipients' => $this->request->getPost('recipients'),
            'filters' => json_encode($this->request->getPost('filters') ?? []),
            'format' => $this->request->getPost('format') ?? 'csv',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'next_run' => $this->calculateNextRun($this->request->getPost('schedule_type'), $this->request->getPost('schedule_value'))
        ];

        // Save to database (would need a scheduled_reports table)
        // For now, just redirect with success message
        return redirect()->to('reports/scheduled')->with('success', 'Geplanter Bericht wurde erfolgreich erstellt.');
    }

    /**
     * Export Custom Report
     */
    public function exportCustom()
    {
        // Get report parameters from session or request
        $reportType = $this->request->getGet('report_type');
        $selectedFields = $this->request->getGet('fields') ? explode(',', $this->request->getGet('fields')) : [];
        $filters = $this->request->getGet('filters') ?? [];
        $format = $this->request->getGet('format') ?? 'csv';
        $reportName = $this->request->getGet('report_name') ?? 'Benutzerdefinierter Bericht';

        // Build the report data
        $data = $this->buildCustomReport($reportType, $selectedFields, $filters);

        switch ($format) {
            case 'csv':
                return $this->exportCustomToCSV($data, $selectedFields, $reportName);
            case 'pdf':
                return $this->exportCustomToPDF($data, $selectedFields, $reportName, $reportType, $filters);
            default:
                return redirect()->back()->with('error', 'Ungültiges Export-Format');
        }
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
                $filters = [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'status' => $this->request->getGet('status'),
                    'technician' => $this->request->getGet('technician')
                ];
                return $this->exportToPDF($workOrders, 'Arbeitsaufträge Bericht', 'work_orders', $filters);
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
                $filters = [
                    'status' => $this->request->getGet('status'),
                    'type' => $this->request->getGet('type'),
                    'location' => $this->request->getGet('location')
                ];
                return $this->exportToPDF($assets, 'Anlagen Bericht', 'assets', $filters);
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
            ->select('work_orders.*, assets.name as asset_name, assets.asset_number, users.first_name, users.last_name')
            ->join('assets', 'work_orders.asset_id = assets.id', 'left')
            ->join('users', 'work_orders.assigned_user_id = users.id', 'left');

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
            $builder->where('work_orders.assigned_user_id', $technician);
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
        $builder = $this->assetModel->select('id, name, asset_number, type, location, status, priority, manufacturer, model, serial_number, installation_date, purchase_price, description, created_at, updated_at');

        if ($status) {
            $builder->where('status', $status);
        }

        if ($type) {
            $builder->where('type', $type);
        }

        if ($location) {
            $builder->where('location', $location);
        }

        return $builder->orderBy('name', 'ASC')->findAll();
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
            ->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number')
            ->join('assets', 'preventive_maintenance.asset_id = assets.id', 'left');

        if ($dateFrom) {
            $builder->where('next_due >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('next_due <=', $dateTo);
        }

        return $builder->orderBy('next_due', 'ASC')->findAll();
    }

    /**
     * Get maintenance statistics
     */
    private function getMaintenanceStats($dateFrom, $dateTo)
    {
        $total = $this->preventiveMaintenanceModel->countAll();
        $overdue = $this->preventiveMaintenanceModel->where('next_due <', date('Y-m-d'))->countAllResults();
        $upcoming = $this->preventiveMaintenanceModel
            ->where('next_due >=', date('Y-m-d'))
            ->where('next_due <=', date('Y-m-d', strtotime('+30 days')))
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
        $response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');

        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (!empty($data)) {
            // Write headers with German column names
            $headers = $this->getGermanHeaders(array_keys($data[0]));
            fputcsv($output, $headers, ';');

            // Write data
            foreach ($data as $row) {
                // Format data for better readability
                $formattedRow = $this->formatRowForCSV($row);
                fputcsv($output, $formattedRow, ';');
            }
        }

        fclose($output);
        return $response;
    }

    private function getGermanHeaders($keys)
    {
        $translations = [
            'id' => 'ID',
            'work_order_number' => 'Auftragsnummer',
            'title' => 'Titel',
            'description' => 'Beschreibung',
            'status' => 'Status',
            'priority' => 'Priorität',
            'created_at' => 'Erstellt am',
            'due_date' => 'Fällig am',
            'completed_at' => 'Abgeschlossen am',
            'asset_name' => 'Anlage',
            'asset_number' => 'Anlagennummer',
            'first_name' => 'Vorname',
            'last_name' => 'Nachname',
            'name' => 'Name',
            'type' => 'Typ',
            'location' => 'Standort',
            'manufacturer' => 'Hersteller',
            'installation_date' => 'Installationsdatum'
        ];

        return array_map(function($key) use ($translations) {
            return $translations[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }, $keys);
    }

    private function formatRowForCSV($row)
    {
        return array_map(function($value) {
            if (is_null($value)) {
                return '';
            }

            // Format dates
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return date('d.m.Y', strtotime($value));
            }

            // Format datetime
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value)) {
                return date('d.m.Y H:i', strtotime($value));
            }

            return $value;
        }, $row);
    }

    /**
     * Export data to PDF
     */
    private function exportToPDF($data, $title, $type = 'work_orders', $filters = [])
    {
        try {
            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }

            $pdf = new PDFExporter($title);
            $pdf->AddPage();

            // Add filter information
            if (!empty($filters)) {
                $filterInfo = [];
                foreach ($filters as $key => $value) {
                    if (!empty($value)) {
                        switch ($key) {
                            case 'date_from':
                                $filterInfo['Von Datum'] = date('d.m.Y', strtotime($value));
                                break;
                            case 'date_to':
                                $filterInfo['Bis Datum'] = date('d.m.Y', strtotime($value));
                                break;
                            case 'status':
                                $filterInfo['Status'] = $this->getStatusText($value);
                                break;
                            case 'technician':
                                $filterInfo['Techniker'] = $this->getTechnicianName($value);
                                break;
                            case 'type':
                                $filterInfo['Typ'] = $value;
                                break;
                            case 'location':
                                $filterInfo['Standort'] = $value;
                                break;
                        }
                    }
                }
                if (!empty($filterInfo)) {
                    $pdf->addFilterInfo($filterInfo);
                }
            }

            // Add statistics
            if ($type === 'work_orders') {
                $stats = $this->getWorkOrderStats($filters['date_from'] ?? null, $filters['date_to'] ?? null);
                $pdf->addStatistics([
                    'Gesamt Aufträge' => $stats['total'],
                    'Abgeschlossen' => $stats['completed'],
                    'In Bearbeitung' => $stats['in_progress'],
                    'Offen' => $stats['open'],
                    'Abschlussrate' => $stats['completion_rate'] . '%'
                ]);
            } elseif ($type === 'assets') {
                $stats = $this->getAssetStats();
                $pdf->addStatistics([
                    'Gesamt Anlagen' => $stats['total'],
                    'Betriebsbereit' => $stats['operational'],
                    'In Wartung' => $stats['maintenance'],
                    'Außer Betrieb' => $stats['out_of_service'],
                    'Verfügbarkeitsrate' => $stats['operational_rate'] . '%'
                ]);
            }

            // Add table
            if ($type === 'work_orders') {
                $headers = ['Nr.', 'Titel', 'Anlage', 'Techniker', 'Status', 'Priorität', 'Erstellt'];
                $tableData = [];

                foreach ($data as $order) {
                    $tableData[] = [
                        $order['work_order_number'] ?? '-',
                        $this->truncateText($order['title'] ?? '-', 20),
                        $this->truncateText($order['asset_name'] ?? '-', 15),
                        $this->truncateText(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''), 15),
                        $this->getStatusText($order['status'] ?? ''),
                        $this->getPriorityText($order['priority'] ?? 'medium'),
                        isset($order['created_at']) ? date('d.m.Y', strtotime($order['created_at'])) : '-'
                    ];
                }

                $pdf->addTable($headers, $tableData, [25, 35, 25, 25, 20, 20, 15]);

            } elseif ($type === 'assets') {
                $headers = ['Name', 'Typ', 'Standort', 'Status', 'Hersteller'];
                $tableData = [];

                foreach ($data as $asset) {
                    $tableData[] = [
                        $this->truncateText($asset['name'] ?? '-', 25),
                        $this->truncateText($asset['type'] ?? '-', 15),
                        $this->truncateText($asset['location'] ?? '-', 20),
                        $this->getAssetStatusText($asset['status'] ?? ''),
                        $this->truncateText($asset['manufacturer'] ?? '-', 20)
                    ];
                }

                $pdf->addTable($headers, $tableData, [40, 25, 30, 25, 45]);
            }

            // Generate filename
            $filename = strtolower(str_replace([' ', 'ä', 'ö', 'ü', 'ß'], ['_', 'ae', 'oe', 'ue', 'ss'], $title)) . '_' . date('Y-m-d') . '.pdf';

            // Set proper headers for PDF output
            $this->response->setHeader('Content-Type', 'application/pdf');
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
            $this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $this->response->setHeader('Pragma', 'public');

            // Output PDF directly
            $pdf->Output($filename, 'I');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Fehler beim Erstellen des PDF: ' . $e->getMessage());
        }
    }

    private function truncateText($text, $length)
    {
        return strlen($text) > $length ? substr($text, 0, $length - 3) . '...' : $text;
    }

    private function getStatusText($status)
    {
        $statusMap = [
            'open' => 'Offen',
            'in_progress' => 'In Bearbeitung',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Storniert'
        ];
        return $statusMap[$status] ?? $status;
    }

    private function getPriorityText($priority)
    {
        $priorityMap = [
            'low' => 'Niedrig',
            'medium' => 'Mittel',
            'high' => 'Hoch',
            'urgent' => 'Dringend'
        ];
        return $priorityMap[$priority] ?? $priority;
    }

    private function getAssetStatusText($status)
    {
        $statusMap = [
            'operational' => 'Betriebsbereit',
            'maintenance' => 'In Wartung',
            'out_of_order' => 'Defekt',
            'decommissioned' => 'Stillgelegt'
        ];
        return $statusMap[$status] ?? $status;
    }

    private function getTechnicianName($technicianId)
    {
        if (empty($technicianId)) {
            return 'Alle Techniker';
        }

        $technician = $this->userModel->find($technicianId);
        if ($technician) {
            return $technician['first_name'] . ' ' . $technician['last_name'];
        }

        return 'Unbekannt';
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
        return $this->assetModel->select('type')->distinct()->findAll();
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
        // For now, return empty array to test the view
        return [];
    }

    private function getAssetPerformance($period)
    {
        // For now, return empty array to test the view
        return [];
    }

    private function getMaintenancePerformance($period)
    {
        $data = [
            'compliance_rate' => $this->getMaintenanceComplianceRate($period),
            'overdue_maintenance' => $this->getOverdueMaintenance(),
            'upcoming_maintenance' => $this->getUpcomingMaintenance(30),
            'maintenance_costs' => $this->getMaintenanceCosts($period)
        ];

        return $data;
    }

    private function getPeriodWhereClause($period)
    {
        switch ($period) {
            case 'week':
                return 'work_orders.created_at >= datetime("now", "-7 days")';
            case 'month':
                return 'work_orders.created_at >= datetime("now", "-1 month")';
            case 'quarter':
                return 'work_orders.created_at >= datetime("now", "-3 months")';
            case 'year':
                return 'work_orders.created_at >= datetime("now", "-1 year")';
            default:
                return null;
        }
    }

    private function getMaintenanceComplianceRate($period)
    {
        return 85; // Return fixed value for testing
    }

    private function getOverdueMaintenance()
    {
        return []; // Return empty array for testing
    }

    private function getUpcomingMaintenance($days = 30)
    {
        return []; // Return empty array for testing
    }

    private function getMaintenanceCosts($period)
    {
        // This would require a maintenance_costs table or cost tracking in work orders
        // For now, return placeholder data
        return [
            'total_costs' => 0,
            'average_cost' => 0,
            'cost_breakdown' => []
        ];
    }

    /**
     * Get available report types for custom reports
     */
    private function getCustomReportTypes()
    {
        return [
            'work_orders' => 'Arbeitsaufträge',
            'assets' => 'Anlagen',
            'maintenance' => 'Wartung',
            'users' => 'Benutzer',
            'combined' => 'Kombiniert'
        ];
    }

    /**
     * Get available fields for custom reports
     */
    private function getAvailableFields()
    {
        return [
            'work_orders' => [
                'work_order_number' => 'Auftragsnummer',
                'title' => 'Titel',
                'description' => 'Beschreibung',
                'status' => 'Status',
                'priority' => 'Priorität',
                'created_at' => 'Erstellt am',
                'scheduled_date' => 'Geplant für',
                'completed_at' => 'Abgeschlossen am',
                'asset_name' => 'Anlage',
                'technician_name' => 'Techniker'
            ],
            'assets' => [
                'name' => 'Name',
                'asset_number' => 'Anlagennummer',
                'type' => 'Typ',
                'location' => 'Standort',
                'status' => 'Status',
                'manufacturer' => 'Hersteller',
                'model' => 'Modell',
                'installation_date' => 'Installiert am',
                'purchase_price' => 'Kaufpreis'
            ],
            'maintenance' => [
                'schedule_name' => 'Wartungsname',
                'asset_name' => 'Anlage',
                'interval_type' => 'Intervall-Typ',
                'interval_value' => 'Intervall-Wert',
                'next_due' => 'Nächste Wartung',
                'last_completed' => 'Letzte Wartung',
                'priority' => 'Priorität'
            ]
        ];
    }

    /**
     * Get filter options for custom reports
     */
    private function getFilterOptions()
    {
        return [
            'date_range' => 'Zeitraum',
            'status' => 'Status',
            'priority' => 'Priorität',
            'technician' => 'Techniker',
            'asset_type' => 'Anlagen-Typ',
            'location' => 'Standort'
        ];
    }

    /**
     * Build custom report based on selections
     */
    private function buildCustomReport($reportType, $selectedFields, $filters)
    {
        switch ($reportType) {
            case 'work_orders':
                return $this->buildWorkOrdersCustomReport($selectedFields, $filters);
            case 'assets':
                return $this->buildAssetsCustomReport($selectedFields, $filters);
            case 'maintenance':
                return $this->buildMaintenanceCustomReport($selectedFields, $filters);
            default:
                return [];
        }
    }

    private function buildWorkOrdersCustomReport($selectedFields, $filters)
    {
        $builder = $this->workOrderModel
            ->select('work_orders.*, assets.name as asset_name, users.first_name, users.last_name')
            ->join('assets', 'work_orders.asset_id = assets.id', 'left')
            ->join('users', 'work_orders.assigned_user_id = users.id', 'left');

        // Apply filters
        if (!empty($filters['date_from'])) {
            $builder->where('work_orders.created_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('work_orders.created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        if (!empty($filters['status'])) {
            $builder->where('work_orders.status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $builder->where('work_orders.priority', $filters['priority']);
        }

        return $builder->findAll();
    }

    private function buildAssetsCustomReport($selectedFields, $filters)
    {
        $builder = $this->assetModel;

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        if (!empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }
        if (!empty($filters['location'])) {
            $builder->where('location', $filters['location']);
        }

        return $builder->findAll();
    }

    private function buildMaintenanceCustomReport($selectedFields, $filters)
    {
        $builder = $this->preventiveMaintenanceModel
            ->select('preventive_maintenance.*, assets.name as asset_name')
            ->join('assets', 'preventive_maintenance.asset_id = assets.id', 'left');

        if (!empty($filters['date_from'])) {
            $builder->where('next_due >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('next_due <=', $filters['date_to']);
        }

        return $builder->findAll();
    }

    /**
     * Get scheduled reports
     */
    private function getScheduledReports()
    {
        // For now, return sample data
        return [
            [
                'id' => 1,
                'name' => 'Wöchentlicher Wartungsbericht',
                'report_type' => 'maintenance',
                'schedule_type' => 'weekly',
                'schedule_value' => 'monday',
                'format' => 'pdf',
                'recipients' => 'manager@company.com',
                'last_run' => '2025-01-15 08:00:00',
                'next_run' => '2025-01-22 08:00:00',
                'is_active' => 1
            ],
            [
                'id' => 2,
                'name' => 'Monatlicher Anlagenbericht',
                'report_type' => 'assets',
                'schedule_type' => 'monthly',
                'schedule_value' => '1',
                'format' => 'csv',
                'recipients' => 'admin@company.com',
                'last_run' => '2025-01-01 09:00:00',
                'next_run' => '2025-02-01 09:00:00',
                'is_active' => 1
            ]
        ];
    }

    /**
     * Get schedule options
     */
    private function getScheduleOptions()
    {
        return [
            'daily' => 'Täglich',
            'weekly' => 'Wöchentlich',
            'monthly' => 'Monatlich',
            'quarterly' => 'Vierteljährlich'
        ];
    }

    /**
     * Calculate next run time for scheduled reports
     */
    private function calculateNextRun($scheduleType, $scheduleValue)
    {
        $now = new \DateTime();

        switch ($scheduleType) {
            case 'daily':
                return $now->modify('+1 day')->format('Y-m-d H:i:s');
            case 'weekly':
                return $now->modify('next ' . $scheduleValue)->format('Y-m-d H:i:s');
            case 'monthly':
                return $now->modify('first day of next month')->modify('+' . ($scheduleValue - 1) . ' days')->format('Y-m-d H:i:s');
            case 'quarterly':
                return $now->modify('+3 months')->format('Y-m-d H:i:s');
            default:
                return $now->modify('+1 day')->format('Y-m-d H:i:s');
        }
    }

    /**
     * Export custom report to CSV
     */
    private function exportCustomToCSV($data, $selectedFields, $reportName)
    {
        $response = $this->response;
        $filename = strtolower(str_replace([' ', 'ä', 'ö', 'ü', 'ß'], ['_', 'ae', 'oe', 'ue', 'ss'], $reportName)) . '_' . date('Y-m-d');

        $response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');

        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (!empty($data)) {
            // Write headers
            if (!empty($selectedFields)) {
                $headers = $this->getCustomReportHeaders($selectedFields);
            } else {
                $headers = $this->getGermanHeaders(array_keys($data[0]));
            }
            fputcsv($output, $headers, ';');

            // Write data
            foreach ($data as $row) {
                $formattedRow = [];

                if (!empty($selectedFields)) {
                    foreach ($selectedFields as $field) {
                        if (isset($row[$field])) {
                            $formattedRow[] = $this->formatValueForCSV($row[$field]);
                        } else {
                            $formattedRow[] = '';
                        }
                    }
                } else {
                    foreach ($row as $value) {
                        $formattedRow[] = $this->formatValueForCSV($value);
                    }
                }

                fputcsv($output, $formattedRow, ';');
            }
        }

        fclose($output);
        return $response;
    }

    /**
     * Export custom report to PDF
     */
    private function exportCustomToPDF($data, $selectedFields, $reportName, $reportType, $filters)
    {
        try {
            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }

            $pdf = new PDFExporter($reportName);
            $pdf->AddPage();

            // Add filter information
            if (!empty($filters)) {
                $filterInfo = [];
                foreach ($filters as $key => $value) {
                    if (!empty($value)) {
                        switch ($key) {
                            case 'date_from':
                                $filterInfo['Von Datum'] = date('d.m.Y', strtotime($value));
                                break;
                            case 'date_to':
                                $filterInfo['Bis Datum'] = date('d.m.Y', strtotime($value));
                                break;
                            case 'status':
                                $filterInfo['Status'] = $this->getStatusText($value);
                                break;
                            case 'priority':
                                $filterInfo['Priorität'] = $this->getPriorityText($value);
                                break;
                        }
                    }
                }
                if (!empty($filterInfo)) {
                    $pdf->addFilterInfo($filterInfo);
                }
            }

            // Add table with selected fields
            if (!empty($data)) {
                if (!empty($selectedFields)) {
                    $headers = $this->getCustomReportHeaders($selectedFields);
                    $tableData = [];

                    foreach ($data as $row) {
                        $tableRow = [];
                        foreach ($selectedFields as $field) {
                            if (isset($row[$field])) {
                                $value = $this->formatValueForPDF($row[$field], $field);
                                $tableRow[] = $this->truncateText($value, 20);
                            } else {
                                $tableRow[] = '-';
                            }
                        }
                        $tableData[] = $tableRow;
                    }

                    // Calculate column widths based on number of columns
                    $numCols = count($headers);
                    $colWidth = max(15, floor(165 / $numCols));
                    $columnWidths = array_fill(0, $numCols, $colWidth);

                    $pdf->addTable($headers, $tableData, $columnWidths);
                }
            }

            // Generate filename
            $filename = strtolower(str_replace([' ', 'ä', 'ö', 'ü', 'ß'], ['_', 'ae', 'oe', 'ue', 'ss'], $reportName)) . '_' . date('Y-m-d') . '.pdf';

            // Set proper headers for PDF output
            $this->response->setHeader('Content-Type', 'application/pdf');
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
            $this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $this->response->setHeader('Pragma', 'public');

            // Output PDF directly
            $pdf->Output($filename, 'I');
            exit();

        } catch (\Exception $e) {
            log_message('error', 'Custom PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Fehler beim Erstellen des PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get headers for custom reports
     */
    private function getCustomReportHeaders($selectedFields)
    {
        $fieldTranslations = [
            'work_order_number' => 'Auftragsnummer',
            'title' => 'Titel',
            'description' => 'Beschreibung',
            'status' => 'Status',
            'priority' => 'Priorität',
            'created_at' => 'Erstellt am',
            'scheduled_date' => 'Geplant für',
            'completed_at' => 'Abgeschlossen am',
            'asset_name' => 'Anlage',
            'technician_name' => 'Techniker',
            'first_name' => 'Vorname',
            'last_name' => 'Nachname',
            'name' => 'Name',
            'asset_number' => 'Anlagennummer',
            'type' => 'Typ',
            'location' => 'Standort',
            'manufacturer' => 'Hersteller',
            'model' => 'Modell',
            'installation_date' => 'Installiert am',
            'purchase_price' => 'Kaufpreis',
            'schedule_name' => 'Wartungsname',
            'interval_type' => 'Intervall-Typ',
            'interval_value' => 'Intervall-Wert',
            'next_due' => 'Nächste Wartung',
            'last_completed' => 'Letzte Wartung'
        ];

        $headers = [];
        foreach ($selectedFields as $field) {
            $headers[] = $fieldTranslations[$field] ?? ucfirst(str_replace('_', ' ', $field));
        }

        return $headers;
    }

    /**
     * Format value for CSV export
     */
    private function formatValueForCSV($value)
    {
        if (is_null($value)) {
            return '';
        }

        // Format dates
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return date('d.m.Y', strtotime($value));
        }

        // Format datetime
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value)) {
            return date('d.m.Y H:i', strtotime($value));
        }

        return $value;
    }

    /**
     * Format value for PDF export
     */
    private function formatValueForPDF($value, $field)
    {
        if (is_null($value)) {
            return '-';
        }

        // Format specific fields
        if (in_array($field, ['created_at', 'updated_at', 'scheduled_date', 'completed_at', 'next_due', 'last_completed', 'installation_date'])) {
            return $value ? date('d.m.Y', strtotime($value)) : '-';
        } elseif ($field === 'status') {
            return $this->getStatusText($value);
        } elseif ($field === 'priority') {
            return $this->getPriorityText($value);
        }

        return $value;
    }
}