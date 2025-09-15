<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table            = 'assets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'asset_number', 'type', 'location', 'status', 'priority',
        'manufacturer', 'model', 'serial_number', 'installation_date',
        'purchase_price', 'description', 'svg_position_x', 'svg_position_y'
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
        'name' => 'required|max_length[200]',
        'asset_number' => 'required|max_length[100]|is_unique[assets.asset_number,id,{id}]',
        'type' => 'required|max_length[100]',
        'location' => 'required|max_length[200]',
        'status' => 'required|in_list[operational,maintenance,out_of_order,decommissioned]',
        'priority' => 'required|in_list[low,medium,high,critical]'
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Anlagenname ist erforderlich'
        ],
        'asset_number' => [
            'required' => 'Anlagennummer ist erforderlich',
            'is_unique' => 'Diese Anlagennummer ist bereits vergeben'
        ],
        'type' => [
            'required' => 'Anlagentyp ist erforderlich'
        ],
        'location' => [
            'required' => 'Standort ist erforderlich'
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
     * Get assets by status
     */
    public function getAssetsByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }

    /**
     * Get critical assets
     */
    public function getCriticalAssets()
    {
        return $this->where('priority', 'critical')->findAll();
    }

    /**
     * Search assets
     */
    public function searchAssets($query)
    {
        return $this->groupStart()
                    ->like('name', $query)
                    ->orLike('asset_number', $query)
                    ->orLike('type', $query)
                    ->orLike('location', $query)
                    ->groupEnd()
                    ->findAll();
    }

    /**
     * Get asset statistics
     */
    public function getAssetStatistics()
    {
        $total = $this->countAll();
        $byStatus = $this->select('status, COUNT(*) as count')
                         ->groupBy('status')
                         ->findAll();
        
        $byPriority = $this->select('priority, COUNT(*) as count')
                           ->groupBy('priority')
                           ->findAll();

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'by_priority' => $byPriority
        ];
    }
}
