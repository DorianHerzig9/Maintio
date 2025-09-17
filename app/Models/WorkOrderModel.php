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
        'work_order_number' => 'permit_empty|max_length[100]|is_unique[work_orders.work_order_number,id,{id}]',
        'title' => 'required|max_length[200]',
        'type' => 'required|in_list[instandhaltung,instandsetzung,inspektion,notfall]',
        'status' => 'required|in_list[open,in_progress,completed,cancelled,on_hold]',
        'priority' => 'required|in_list[low,medium,high,critical]',
        'created_by_user_id' => 'required|integer'
    ];
    
    protected $validationMessages = [
        'work_order_number' => [
            'is_unique' => 'Diese Arbeitsauftragsnummer ist bereits vergeben'
        ],
        'title' => [
            'required' => 'Titel ist erforderlich'
        ],
        'type' => [
            'required' => 'Typ ist erforderlich'
        ],
        'priority' => [
            'required' => 'Priorität ist erforderlich'
        ],
        'created_by_user_id' => [
            'required' => 'Ersteller ist erforderlich'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get overdue work orders
     */
    public function getOverdueWorkOrders($limit = null)
    {
        $builder = $this->select('work_orders.*, assets.name as asset_name, assets.asset_number,
                                 users.first_name, users.last_name')
                        ->join('assets', 'work_orders.asset_id = assets.id', 'left')
                        ->join('users', 'work_orders.assigned_user_id = users.id', 'left')
                        ->where('work_orders.scheduled_date <', date('Y-m-d H:i:s'))
                        ->whereIn('work_orders.status', ['open', 'in_progress'])
                        ->orderBy('work_orders.scheduled_date', 'ASC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get work orders due soon
     */
    public function getDueSoonWorkOrders($days = 7, $limit = null)
    {
        $builder = $this->select('work_orders.*, assets.name as asset_name, assets.asset_number,
                                 users.first_name, users.last_name')
                        ->join('assets', 'work_orders.asset_id = assets.id', 'left')
                        ->join('users', 'work_orders.assigned_user_id = users.id', 'left')
                        ->where('work_orders.scheduled_date >=', date('Y-m-d H:i:s'))
                        ->where('work_orders.scheduled_date <=', date('Y-m-d H:i:s', strtotime("+{$days} days")))
                        ->whereIn('work_orders.status', ['open', 'in_progress'])
                        ->orderBy('work_orders.scheduled_date', 'ASC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

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
    public function getWorkOrdersWithDetails($limit = null, $offset = null)
    {
        $builder = $this->select('work_orders.id, work_orders.work_order_number, work_orders.title,
                                 work_orders.description, work_orders.type, work_orders.status,
                                 work_orders.priority, work_orders.scheduled_date, work_orders.created_at,
                                 work_orders.updated_at, assets.name as asset_name,
                                 assigned.username as assigned_username, assigned.first_name as assigned_first_name,
                                 assigned.last_name as assigned_last_name,
                                 creator.username as creator_username, creator.first_name as creator_first_name,
                                 creator.last_name as creator_last_name')
                        ->join('assets', 'assets.id = work_orders.asset_id', 'left')
                        ->join('users assigned', 'assigned.id = work_orders.assigned_user_id', 'left')
                        ->join('users creator', 'creator.id = work_orders.created_by_user_id', 'left')
                        ->orderBy('work_orders.created_at', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset ?? 0);
        }

        return $builder->findAll();
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

    /**
     * Get comprehensive work order statistics for dashboard
     */
    public function getWorkOrderStatistics()
    {
        // Use a single query to get all statistics
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        // Get all statistics in one query
        $query = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
                    SUM(CASE WHEN status = 'on_hold' THEN 1 ELSE 0 END) as on_hold_count,
                    SUM(CASE WHEN priority = 'low' THEN 1 ELSE 0 END) as low_priority,
                    SUM(CASE WHEN priority = 'medium' THEN 1 ELSE 0 END) as medium_priority,
                    SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_priority,
                    SUM(CASE WHEN priority = 'critical' THEN 1 ELSE 0 END) as critical_priority,
                    SUM(CASE WHEN type = 'instandhaltung' THEN 1 ELSE 0 END) as instandhaltung_type,
                    SUM(CASE WHEN type = 'instandsetzung' THEN 1 ELSE 0 END) as instandsetzung_type,
                    SUM(CASE WHEN type = 'inspektion' THEN 1 ELSE 0 END) as inspektion_type,
                    SUM(CASE WHEN type = 'notfall' THEN 1 ELSE 0 END) as notfall_type
                  FROM {$this->table}";

        $result = $db->query($query)->getRowArray();

        return [
            'total' => (int) $result['total'],
            'by_status' => [
                ['status' => 'open', 'count' => (int) $result['open_count']],
                ['status' => 'in_progress', 'count' => (int) $result['in_progress_count']],
                ['status' => 'completed', 'count' => (int) $result['completed_count']],
                ['status' => 'cancelled', 'count' => (int) $result['cancelled_count']],
                ['status' => 'on_hold', 'count' => (int) $result['on_hold_count']]
            ],
            'by_priority' => [
                ['priority' => 'low', 'count' => (int) $result['low_priority']],
                ['priority' => 'medium', 'count' => (int) $result['medium_priority']],
                ['priority' => 'high', 'count' => (int) $result['high_priority']],
                ['priority' => 'critical', 'count' => (int) $result['critical_priority']]
            ],
            'by_type' => [
                ['type' => 'instandhaltung', 'count' => (int) $result['instandhaltung_type']],
                ['type' => 'instandsetzung', 'count' => (int) $result['instandsetzung_type']],
                ['type' => 'inspektion', 'count' => (int) $result['inspektion_type']],
                ['type' => 'notfall', 'count' => (int) $result['notfall_type']]
            ]
        ];
    }
    
    public function getByAssetId($assetId)
    {
        return $this->where('asset_id', $assetId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Search work orders with proper escaping
     */
    public function searchWorkOrders($query, $limit = 50)
    {
        if (empty($query) || strlen(trim($query)) < 2) {
            return [];
        }

        // Escape and sanitize the search query
        $escapedQuery = $this->db->escapeLikeString(esc($query));

        return $this->select('id, work_order_number, title, description, status, priority, created_at')
                    ->groupStart()
                        ->like('work_order_number', $escapedQuery, 'both', false)
                        ->orLike('title', $escapedQuery, 'both', false)
                        ->orLike('description', $escapedQuery, 'both', false)
                    ->groupEnd()
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
