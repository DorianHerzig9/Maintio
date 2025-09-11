<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssetModel;
use App\Models\WorkOrderModel;
use CodeIgniter\HTTP\ResponseInterface;

class Assets extends BaseController
{
    protected $assetModel;
    protected $workOrderModel;

    public function __construct()
    {
        $this->assetModel = new AssetModel();
        $this->workOrderModel = new WorkOrderModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Anlagen',
            'assets' => $this->assetModel->findAll(),
            'status_filter' => $this->request->getGet('status'),
            'priority_filter' => $this->request->getGet('priority')
        ];

        return view('assets/index', $data);
    }

    public function show($id)
    {
        $asset = $this->assetModel->find($id);
        
        if (!$asset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anlage nicht gefunden');
        }

        // Arbeitsaufträge für diese Anlage
        $workOrders = $this->workOrderModel->where('asset_id', $id)
                                           ->orderBy('created_at', 'DESC')
                                           ->findAll();

        $data = [
            'page_title' => 'Anlagen Details',
            'asset' => $asset,
            'work_orders' => $workOrders
        ];

        return view('assets/show', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Neue Anlage'
        ];

        return view('assets/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[200]',
            'asset_number' => 'required|max_length[100]|is_unique[assets.asset_number]',
            'type' => 'required|max_length[100]',
            'location' => 'required|max_length[200]',
            'status' => 'required|in_list[operational,maintenance,out_of_order,decommissioned]',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'manufacturer' => 'permit_empty|max_length[150]',
            'model' => 'permit_empty|max_length[150]',
            'serial_number' => 'permit_empty|max_length[150]',
            'installation_date' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'asset_number' => $this->request->getPost('asset_number'),
            'type' => $this->request->getPost('type'),
            'location' => $this->request->getPost('location'),
            'status' => $this->request->getPost('status'),
            'priority' => $this->request->getPost('priority'),
            'manufacturer' => $this->request->getPost('manufacturer') ?: null,
            'model' => $this->request->getPost('model') ?: null,
            'serial_number' => $this->request->getPost('serial_number') ?: null,
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'description' => $this->request->getPost('description')
        ];

        if ($this->assetModel->insert($data)) {
            return redirect()->to('/assets')->with('success', 'Anlage erfolgreich erstellt');
        } else {
            return redirect()->back()->withInput()->with('error', 'Fehler beim Erstellen der Anlage');
        }
    }

    public function edit($id)
    {
        $asset = $this->assetModel->find($id);
        
        if (!$asset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anlage nicht gefunden');
        }

        $data = [
            'page_title' => 'Anlage bearbeiten',
            'asset' => $asset
        ];

        return view('assets/edit', $data);
    }

    public function update($id)
    {
        $asset = $this->assetModel->find($id);
        
        if (!$asset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anlage nicht gefunden');
        }

        $rules = [
            'name' => 'required|max_length[200]',
            'asset_number' => "required|max_length[100]|is_unique[assets.asset_number,id,{$id}]",
            'type' => 'required|max_length[100]',
            'location' => 'required|max_length[200]',
            'status' => 'required|in_list[operational,maintenance,out_of_order,decommissioned]',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'manufacturer' => 'permit_empty|max_length[150]',
            'model' => 'permit_empty|max_length[150]',
            'serial_number' => 'permit_empty|max_length[150]',
            'installation_date' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'asset_number' => $this->request->getPost('asset_number'),
            'type' => $this->request->getPost('type'),
            'location' => $this->request->getPost('location'),
            'status' => $this->request->getPost('status'),
            'priority' => $this->request->getPost('priority'),
            'manufacturer' => $this->request->getPost('manufacturer') ?: null,
            'model' => $this->request->getPost('model') ?: null,
            'serial_number' => $this->request->getPost('serial_number') ?: null,
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'description' => $this->request->getPost('description')
        ];

        if ($this->assetModel->update($id, $data)) {
            return redirect()->to('/assets')->with('success', 'Anlage erfolgreich aktualisiert');
        } else {
            return redirect()->back()->withInput()->with('error', 'Fehler beim Aktualisieren der Anlage');
        }
    }

    public function delete($id)
    {
        $asset = $this->assetModel->find($id);
        
        if (!$asset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anlage nicht gefunden');
        }

        // Prüfen ob Arbeitsaufträge zugeordnet sind
        $relatedWorkOrders = $this->workOrderModel->where('asset_id', $id)->countAllResults();
        
        if ($relatedWorkOrders > 0) {
            return redirect()->to('/assets')->with('error', 'Anlage kann nicht gelöscht werden, da noch Arbeitsaufträge zugeordnet sind');
        }

        if ($this->assetModel->delete($id)) {
            return redirect()->to('/assets')->with('success', 'Anlage erfolgreich gelöscht');
        } else {
            return redirect()->to('/assets')->with('error', 'Fehler beim Löschen der Anlage');
        }
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        $assets = $this->assetModel->searchAssets($query);
        
        return $this->response->setJSON($assets);
    }
}
