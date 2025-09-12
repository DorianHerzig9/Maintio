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
        'status' => 'permit_empty|in_list[pending,in_progress,completed,skipped]',
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
        if (empty($components) || !is_array($components)) {
            return true; // No components to add is not an error
        }
        
        $data = [];
        foreach ($components as $index => $component) {
            $componentData = [
                'work_order_id' => (int) $workOrderId,
                'kks_number' => trim($component['kks_number']),
                'component_name' => trim($component['component_name']),
                'description' => !empty($component['description']) ? trim($component['description']) : null,
                'status' => 'pending',
                'order_position' => $index + 1
            ];
            
            // Validate each component before adding to batch
            if (empty($componentData['kks_number']) || empty($componentData['component_name'])) {
                log_message('error', 'Invalid component data: ' . print_r($component, true));
                continue;
            }
            
            $data[] = $componentData;
        }
        
        if (empty($data)) {
            log_message('warning', 'No valid components to insert');
            return true; // No valid components is not an error
        }
        
        try {
            $result = $this->insertBatch($data);
            if (!$result) {
                log_message('error', 'insertBatch failed with errors: ' . print_r($this->errors(), true));
            }
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Exception in addComponentsToWorkOrder: ' . $e->getMessage());
            return false;
        }
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
