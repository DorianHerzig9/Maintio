<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderComponentModel extends Model
{
    protected $table            = 'work_order_components';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'work_order_id', 'kks_number', 'component_name', 'description', 
        'status', 'order_position'
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
        'work_order_id' => 'required|integer',
        'kks_number' => 'required|max_length[100]',
        'component_name' => 'required|max_length[200]',
        'status' => 'required|in_list[pending,in_progress,completed,skipped]',
        'order_position' => 'permit_empty|integer'
    ];
    
    protected $validationMessages = [
        'work_order_id' => [
            'required' => 'Arbeitsauftrag ist erforderlich'
        ],
        'kks_number' => [
            'required' => 'KKS-Nummer ist erforderlich'
        ],
        'component_name' => [
            'required' => 'Komponentenname ist erforderlich'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get components for a work order
     */
    public function getComponentsByWorkOrder($workOrderId)
    {
        return $this->where('work_order_id', $workOrderId)
                    ->orderBy('order_position', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Add multiple components to a work order
     */
    public function addComponentsToWorkOrder($workOrderId, $components)
    {
        $data = [];
        foreach ($components as $index => $component) {
            $data[] = [
                'work_order_id' => $workOrderId,
                'kks_number' => $component['kks_number'],
                'component_name' => $component['component_name'],
                'description' => $component['description'] ?? null,
                'status' => 'pending',
                'order_position' => $index + 1
            ];
        }
        
        return $this->insertBatch($data);
    }

    /**
     * Update component status
     */
    public function updateComponentStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get component statistics for a work order
     */
    public function getComponentStats($workOrderId)
    {
        $total = $this->where('work_order_id', $workOrderId)->countAllResults();
        $completed = $this->where('work_order_id', $workOrderId)
                          ->where('status', 'completed')
                          ->countAllResults();
        $inProgress = $this->where('work_order_id', $workOrderId)
                           ->where('status', 'in_progress')
                           ->countAllResults();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => $total - $completed - $inProgress,
            'completion_percentage' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
        ];
    }
}
