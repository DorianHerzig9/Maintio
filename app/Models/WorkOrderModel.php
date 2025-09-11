<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderModel extends Model
{
    protected $table            = 'work_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'work_order_number', 'title', 'description', 'type', 'status', 'priority',
        'asset_id', 'assigned_user_id', 'created_by_user_id', 'estimated_duration',
        'actual_duration', 'scheduled_date', 'started_at', 'completed_at', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'work_order_number' => 'required|max_length[100]|is_unique[work_orders.work_order_number,id,{id}]',
        'title' => 'required|max_length[200]',
        'type' => 'required|in_list[preventive,corrective,emergency,inspection]',
        'status' => 'required|in_list[open,in_progress,completed,cancelled,on_hold]',
        'priority' => 'required|in_list[low,medium,high,critical]',
        'created_by_user_id' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'work_order_number' => [
            'required' => 'Arbeitsauftragsnummer ist erforderlich',
            'is_unique' => 'Diese Arbeitsauftragsnummer ist bereits vergeben'
        ],
        'title' => [
            'required' => 'Titel ist erforderlich'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateWorkOrderNumber'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate unique work order number
     */
    protected function generateWorkOrderNumber(array $data)
    {
        if (!isset($data['data']['work_order_number']) || empty($data['data']['work_order_number'])) {
            $prefix = 'WO';
            $year = date('Y');
            $month = date('m');
            
            // Get the latest work order number for this month
            $lastOrder = $this->like('work_order_number', $prefix . $year . $month)
                              ->orderBy('id', 'DESC')
                              ->first();
            
            if ($lastOrder) {
                $lastNumber = intval(substr($lastOrder['work_order_number'], -4));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $data['data']['work_order_number'] = $prefix . $year . $month . sprintf('%04d', $newNumber);
        }
        
        return $data;
    }

    /**
     * Get work orders with related data
     */
    public function getWorkOrdersWithDetails()
    {
        return $this->select('work_orders.*, assets.name as asset_name, 
                             assigned.username as assigned_username,
                             creator.username as creator_username')
                    ->join('assets', 'assets.id = work_orders.asset_id', 'left')
                    ->join('users assigned', 'assigned.id = work_orders.assigned_user_id', 'left')
                    ->join('users creator', 'creator.id = work_orders.created_by_user_id', 'left')
                    ->findAll();
    }

    /**
     * Get work orders by status
     */
    public function getWorkOrdersByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }

    /**
     * Get work orders by user
     */
    public function getWorkOrdersByUser($userId)
    {
        return $this->where('assigned_user_id', $userId)->findAll();
    }

    /**
     * Get work order statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total' => $this->countAll(),
            'open' => $this->where('status', 'open')->countAllResults(),
            'in_progress' => $this->where('status', 'in_progress')->countAllResults(),
            'completed' => $this->where('status', 'completed')->countAllResults(),
            'cancelled' => $this->where('status', 'cancelled')->countAllResults(),
            'on_hold' => $this->where('status', 'on_hold')->countAllResults()
        ];
        
        return $stats;
    }
    
    public function getByAssetId($assetId)
    {
        return $this->where('asset_id', $assetId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Search work orders
     */
    public function searchWorkOrders($query)
    {
        return $this->groupStart()
                    ->like('work_order_number', $query)
                    ->orLike('title', $query)
                    ->orLike('description', $query)
                    ->groupEnd()
                    ->findAll();
    }
}
