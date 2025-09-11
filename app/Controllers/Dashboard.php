<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AssetModel;
use App\Models\WorkOrderModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $assetModel;
    protected $workOrderModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->assetModel = new AssetModel();
        $this->workOrderModel = new WorkOrderModel();
    }

    public function index()
    {
        // Dashboard Statistiken sammeln
        $data = [
            'page_title' => 'Maintio Dashboard',
            'stats' => $this->getDashboardStats(),
            'recent_work_orders' => $this->getRecentWorkOrders(),
            'critical_assets' => $this->getCriticalAssets(),
            'upcoming_tasks' => $this->getUpcomingTasks()
        ];

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
        return $this->workOrderModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
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
                    ->limit(5)
                    ->findAll();
    }

    public function getStats()
    {
        // API Endpoint für AJAX-Aufrufe
        return $this->response->setJSON($this->getDashboardStats());
    }
}
