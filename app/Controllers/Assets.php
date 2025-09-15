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
            'installation_date' => 'permit_empty|valid_date',
            'purchase_price' => 'permit_empty|decimal'
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
            'purchase_price' => $this->request->getPost('purchase_price') ?: null,
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
            'asset_number' => 'required|max_length[100]',
            'type' => 'required|max_length[100]',
            'location' => 'required|max_length[200]',
            'status' => 'required|in_list[operational,maintenance,out_of_order,decommissioned]',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'manufacturer' => 'permit_empty|max_length[150]',
            'model' => 'permit_empty|max_length[150]',
            'serial_number' => 'permit_empty|max_length[150]',
            'installation_date' => 'permit_empty|valid_date',
            'purchase_price' => 'permit_empty|decimal'
        ];

        // Check for unique asset_number only if it changed
        $newAssetNumber = $this->request->getPost('asset_number');
        $currentAssetNumber = $asset['asset_number'];
        
        log_message('debug', "Current asset number: '{$currentAssetNumber}', New asset number: '{$newAssetNumber}'");
        
        if ($newAssetNumber !== $currentAssetNumber) {
            $rules['asset_number'] .= "|is_unique[assets.asset_number]";
            log_message('debug', "Asset number changed, adding unique validation");
        } else {
            log_message('debug', "Asset number unchanged, skipping unique validation");
        }

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
            'purchase_price' => $this->request->getPost('purchase_price') ?: null,
            'description' => $this->request->getPost('description')
        ];

        try {
            // Temporarily disable model validation since we're doing custom validation
            $this->assetModel->skipValidation(true);
            if ($this->assetModel->update($id, $data)) {
                return redirect()->to('/assets')->with('success', 'Anlage erfolgreich aktualisiert');
            } else {
                $errors = $this->assetModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->withInput()->with('errors', $errors);
                }
                return redirect()->back()->withInput()->with('error', 'Fehler beim Aktualisieren der Anlage');
            }
        } catch (\Exception $e) {
            log_message('error', 'Asset update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Fehler beim Aktualisieren der Anlage: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $asset = $this->assetModel->find($id);
        
        if (!$asset) {
            if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                return $this->response->setStatusCode(404)
                                     ->setJSON(['success' => false, 'message' => 'Anlage nicht gefunden']);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anlage nicht gefunden');
        }

        try {
            // Prüfen ob Arbeitsaufträge zugeordnet sind
            $relatedWorkOrders = $this->workOrderModel->where('asset_id', $id)->countAllResults();
            
            if ($relatedWorkOrders > 0) {
                if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                    return $this->response->setStatusCode(409)
                                         ->setJSON(['success' => false, 'message' => 'Anlage kann nicht gelöscht werden, da noch Arbeitsaufträge zugeordnet sind']);
                }
                return redirect()->to('/assets')->with('error', 'Anlage kann nicht gelöscht werden, da noch Arbeitsaufträge zugeordnet sind');
            }

            if ($this->assetModel->delete($id)) {
                if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                    return $this->response->setStatusCode(200)
                                         ->setJSON(['success' => true, 'message' => 'Anlage erfolgreich gelöscht']);
                }
                return redirect()->to('/assets')->with('success', 'Anlage erfolgreich gelöscht');
            } else {
                if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                    return $this->response->setStatusCode(500)
                                         ->setJSON(['success' => false, 'message' => 'Fehler beim Löschen der Anlage']);
                }
                return redirect()->to('/assets')->with('error', 'Fehler beim Löschen der Anlage');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error deleting asset ' . $id . ': ' . $e->getMessage());
            if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
                return $this->response->setStatusCode(500)
                                     ->setJSON(['success' => false, 'message' => 'Fehler beim Löschen der Anlage: ' . $e->getMessage()]);
            }
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
