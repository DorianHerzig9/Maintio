<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\WorkOrderComponentModel;
use CodeIgniter\HTTP\ResponseInterface;

class WorkOrders extends BaseController
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

    public function index()
    {
        $data = [
            'page_title' => 'Arbeitsaufträge',
            'work_orders' => $this->workOrderModel->getWorkOrdersWithDetails(),
            'status_filter' => $this->request->getGet('status'),
            'priority_filter' => $this->request->getGet('priority')
        ];

        return view('work_orders/index', $data);
    }

    public function show($id)
    {
        $workOrder = $this->workOrderModel->find($id);
        
        if (!$workOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        // Load components
        $components = $this->componentModel->getComponentsByWorkOrder($id);
        $componentStats = $this->componentModel->getComponentStats($id);

        $data = [
            'page_title' => 'Arbeitsauftrag Details',
            'work_order' => $workOrder,
            'components' => $components,
            'component_stats' => $componentStats,
            'asset' => $workOrder['asset_id'] ? $this->assetModel->find($workOrder['asset_id']) : null,
            'assigned_user' => $workOrder['assigned_user_id'] ? $this->userModel->getUserSafe($workOrder['assigned_user_id']) : null,
            'created_by' => $this->userModel->getUserSafe($workOrder['created_by_user_id'])
        ];

        return view('work_orders/show', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Neuer Arbeitsauftrag',
            'assets' => $this->assetModel->findAll(),
            'users' => $this->userModel->findAll() // Alle Benutzer laden
        ];

        return view('work_orders/create', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|max_length[200]',
            'description' => 'max_length[1000]',
            'type' => 'required|in_list[preventive,corrective,emergency,inspection]',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'asset_id' => 'permit_empty|integer',
            'assigned_user_id' => 'permit_empty|integer',
            'scheduled_date' => 'permit_empty|valid_date',
            'estimated_duration' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'status' => 'open',
            'priority' => $this->request->getPost('priority'),
            'asset_id' => $this->request->getPost('asset_id') ?: null,
            'assigned_user_id' => $this->request->getPost('assigned_user_id') ?: null,
            'created_by_user_id' => 1, // TODO: Aktueller Benutzer aus Session
            'scheduled_date' => $this->request->getPost('scheduled_date') ?: null,
            'estimated_duration' => $this->request->getPost('estimated_duration') ?: null
        ];

        try {
            if ($this->workOrderModel->insert($data)) {
                return redirect()->to('/work-orders')->with('success', 'Arbeitsauftrag erfolgreich erstellt');
            } else {
                $errors = $this->workOrderModel->errors();
                return redirect()->back()->withInput()->with('errors', $errors);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error creating work order: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Fehler beim Erstellen des Arbeitsauftrags: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $workOrder = $this->workOrderModel->find($id);
        
        if (!$workOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        $data = [
            'page_title' => 'Arbeitsauftrag bearbeiten',
            'work_order' => $workOrder,
            'assets' => $this->assetModel->findAll(),
            'users' => $this->userModel->findAll() // Alle Benutzer laden
        ];

        return view('work_orders/edit', $data);
    }

    public function update($id)
    {
        $workOrder = $this->workOrderModel->find($id);
        
        if (!$workOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        $rules = [
            'title' => 'required|max_length[200]',
            'description' => 'max_length[1000]',
            'type' => 'required|in_list[preventive,corrective,emergency,inspection]',
            'status' => 'required|in_list[open,in_progress,completed,cancelled,on_hold]',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'asset_id' => 'permit_empty|integer',
            'assigned_user_id' => 'permit_empty|integer',
            'scheduled_date' => 'permit_empty|valid_date',
            'estimated_duration' => 'permit_empty|integer',
            'actual_duration' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
            'priority' => $this->request->getPost('priority'),
            'asset_id' => $this->request->getPost('asset_id') ?: null,
            'assigned_user_id' => $this->request->getPost('assigned_user_id') ?: null,
            'scheduled_date' => $this->request->getPost('scheduled_date') ?: null,
            'estimated_duration' => $this->request->getPost('estimated_duration') ?: null,
            'actual_duration' => $this->request->getPost('actual_duration') ?: null,
            'notes' => $this->request->getPost('notes')
        ];

        // Status-spezifische Logik
        if ($data['status'] === 'in_progress' && !$workOrder['started_at']) {
            $data['started_at'] = date('Y-m-d H:i:s');
        } elseif ($data['status'] === 'completed' && !$workOrder['completed_at']) {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($this->workOrderModel->update($id, $data)) {
            return redirect()->to('/work-orders')->with('success', 'Arbeitsauftrag erfolgreich aktualisiert');
        } else {
            return redirect()->back()->withInput()->with('error', 'Fehler beim Aktualisieren des Arbeitsauftrags');
        }
    }

    public function delete($id)
    {
        $workOrder = $this->workOrderModel->find($id);
        
        if (!$workOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Arbeitsauftrag nicht gefunden');
        }

        if ($this->workOrderModel->delete($id)) {
            return redirect()->to('/work-orders')->with('success', 'Arbeitsauftrag erfolgreich gelöscht');
        } else {
            return redirect()->to('/work-orders')->with('error', 'Fehler beim Löschen des Arbeitsauftrags');
        }
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        $workOrders = $this->workOrderModel->searchWorkOrders($query);
        
        return $this->response->setJSON($workOrders);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $workOrder = $this->workOrderModel->find($id);
        
        if (!$workOrder) {
            return $this->response->setJSON(['success' => false, 'message' => 'Arbeitsauftrag nicht gefunden']);
        }

        $data = ['status' => $status];

        // Status-spezifische Logik
        if ($status === 'in_progress' && !$workOrder['started_at']) {
            $data['started_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'completed' && !$workOrder['completed_at']) {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($this->workOrderModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status erfolgreich aktualisiert']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Fehler beim Aktualisieren des Status']);
        }
    }

    public function updateComponentStatus($workOrderId, $componentId)
    {
        $status = $this->request->getPost('status');
        $component = $this->componentModel->find($componentId);
        
        if (!$component || $component['work_order_id'] != $workOrderId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Komponente nicht gefunden']);
        }

        if ($this->componentModel->updateComponentStatus($componentId, $status)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Komponentenstatus erfolgreich aktualisiert']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Fehler beim Aktualisieren des Komponentenstatus']);
        }
    }
}
