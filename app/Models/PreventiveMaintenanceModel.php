<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class PreventiveMaintenanceModel extends Model
{
    protected $table            = 'preventive_maintenance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'asset_id', 'schedule_name', 'description', 'task_details',
        'interval_type', 'interval_value', 'priority', 'estimated_duration',
        'auto_generate_work_orders', 'lead_time_days', 'last_completed',
        'next_due', 'is_active', 'assigned_user_id', 'category',
        'required_tools', 'required_parts', 'safety_notes'
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
        'asset_id' => 'required|integer',
        'schedule_name' => 'required|max_length[200]',
        'interval_type' => 'required|in_list[daily,weekly,monthly,quarterly,annually,hours,cycles,kilometers]',
        'interval_value' => 'required|integer|greater_than[0]',
        'priority' => 'required|in_list[low,medium,high,critical]',
        'lead_time_days' => 'integer|greater_than_equal_to[0]'
    ];
    
    protected $validationMessages = [
        'asset_id' => [
            'required' => 'Anlage ist erforderlich',
            'integer' => 'Ungültige Anlage'
        ],
        'schedule_name' => [
            'required' => 'Name des Instandhaltungsplans ist erforderlich'
        ],
        'interval_type' => [
            'required' => 'Intervalltyp ist erforderlich',
            'in_list' => 'Ungültiger Intervalltyp'
        ],
        'interval_value' => [
            'required' => 'Intervallwert ist erforderlich',
            'integer' => 'Intervallwert muss eine Zahl sein',
            'greater_than' => 'Intervallwert muss größer als 0 sein'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateNextDue'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['calculateNextDue'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Calculate next due date before insert/update
     */
    protected function calculateNextDue(array $data)
    {
        if (isset($data['data']['interval_type']) && isset($data['data']['interval_value'])) {
            $baseDate = isset($data['data']['last_completed']) && $data['data']['last_completed'] 
                       ? new DateTime($data['data']['last_completed'])
                       : new DateTime();
            
            $data['data']['next_due'] = $this->calculateNextDueDate(
                $baseDate,
                $data['data']['interval_type'],
                $data['data']['interval_value']
            );
        }
        
        return $data;
    }

    /**
     * Calculate next due date based on interval
     */
    public function calculateNextDueDate(DateTime $baseDate, string $intervalType, int $intervalValue): string
    {
        $nextDue = clone $baseDate;
        
        switch ($intervalType) {
            case 'daily':
                $nextDue->modify("+{$intervalValue} days");
                break;
            case 'weekly':
                $weeks = $intervalValue * 7;
                $nextDue->modify("+{$weeks} days");
                break;
            case 'monthly':
                $nextDue->modify("+{$intervalValue} months");
                break;
            case 'quarterly':
                $months = $intervalValue * 3;
                $nextDue->modify("+{$months} months");
                break;
            case 'annually':
                $nextDue->modify("+{$intervalValue} years");
                break;
            case 'hours':
            case 'cycles':
            case 'kilometers':
                // For usage-based maintenance, we estimate based on average usage
                // This would typically integrate with usage tracking systems
                // For now, we'll use a 30-day default interval
                $nextDue->modify("+30 days");
                break;
            default:
                $nextDue->modify("+30 days");
        }
        
        return $nextDue->format('Y-m-d H:i:s');
    }

    /**
     * Get overdue maintenance schedules
     */
    public function getOverdueSchedules()
    {
        return $this->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number')
                    ->join('assets', 'assets.id = preventive_maintenance.asset_id')
                    ->where('preventive_maintenance.is_active', 1)
                    ->where('preventive_maintenance.next_due <', date('Y-m-d H:i:s'))
                    ->orderBy('preventive_maintenance.next_due', 'ASC')
                    ->findAll();
    }

    /**
     * Get upcoming maintenance schedules
     */
    public function getUpcomingSchedules($days = 30)
    {
        $futureDate = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        
        return $this->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number')
                    ->join('assets', 'assets.id = preventive_maintenance.asset_id')
                    ->where('preventive_maintenance.is_active', 1)
                    ->where('preventive_maintenance.next_due >=', date('Y-m-d H:i:s'))
                    ->where('preventive_maintenance.next_due <=', $futureDate)
                    ->orderBy('preventive_maintenance.next_due', 'ASC')
                    ->findAll();
    }

    /**
     * Get schedules that need work order generation
     */
    public function getSchedulesNeedingWorkOrders()
    {
        $leadTimeDate = date('Y-m-d H:i:s', strtotime('+30 days')); // Max lead time
        
        return $this->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number')
                    ->join('assets', 'assets.id = preventive_maintenance.asset_id')
                    ->where('preventive_maintenance.is_active', 1)
                    ->where('preventive_maintenance.auto_generate_work_orders', 1)
                    ->where('DATE_SUB(preventive_maintenance.next_due, INTERVAL preventive_maintenance.lead_time_days DAY) <=', date('Y-m-d H:i:s'))
                    ->where('preventive_maintenance.next_due >=', date('Y-m-d H:i:s'))
                    ->findAll();
    }

    /**
     * Mark maintenance as completed and calculate next due date
     */
    public function markAsCompleted(int $id, DateTime $completedDate = null): bool
    {
        $schedule = $this->find($id);
        if (!$schedule) {
            return false;
        }

        $completedDate = $completedDate ?? new DateTime();
        
        $nextDue = $this->calculateNextDueDate(
            $completedDate,
            $schedule['interval_type'],
            $schedule['interval_value']
        );

        return $this->update($id, [
            'last_completed' => $completedDate->format('Y-m-d H:i:s'),
            'next_due' => $nextDue
        ]);
    }

    /**
     * Get maintenance schedules for a specific asset
     */
    public function getSchedulesForAsset(int $assetId)
    {
        return $this->where('asset_id', $assetId)
                    ->where('is_active', 1)
                    ->orderBy('next_due', 'ASC')
                    ->findAll();
    }

    /**
     * Get maintenance history for an asset
     */
    public function getMaintenanceHistory(int $assetId)
    {
        return $this->where('asset_id', $assetId)
                    ->where('last_completed IS NOT NULL')
                    ->orderBy('last_completed', 'DESC')
                    ->findAll();
    }

    /**
     * Get preventive maintenance statistics
     */
    public function getMaintenanceStatistics()
    {
        $total = $this->where('is_active', 1)->countAllResults();
        
        $overdue = $this->where('is_active', 1)
                        ->where('next_due <', date('Y-m-d H:i:s'))
                        ->countAllResults();
        
        $upcoming = $this->where('is_active', 1)
                         ->where('next_due >=', date('Y-m-d H:i:s'))
                         ->where('next_due <=', date('Y-m-d H:i:s', strtotime('+30 days')))
                         ->countAllResults();
        
        $byPriority = $this->select('priority, COUNT(*) as count')
                           ->where('is_active', 1)
                           ->groupBy('priority')
                           ->findAll();
        
        $byCategory = $this->select('category, COUNT(*) as count')
                           ->where('is_active', 1)
                           ->where('category IS NOT NULL')
                           ->groupBy('category')
                           ->findAll();

        return [
            'total_active' => $total,
            'overdue' => $overdue,
            'upcoming_30_days' => $upcoming,
            'by_priority' => $byPriority,
            'by_category' => $byCategory
        ];
    }

    /**
     * Search preventive maintenance schedules
     */
    public function searchSchedules($query)
    {
        return $this->select('preventive_maintenance.*, assets.name as asset_name, assets.asset_number')
                    ->join('assets', 'assets.id = preventive_maintenance.asset_id')
                    ->groupStart()
                    ->like('preventive_maintenance.schedule_name', $query)
                    ->orLike('preventive_maintenance.description', $query)
                    ->orLike('preventive_maintenance.category', $query)
                    ->orLike('assets.name', $query)
                    ->orLike('assets.asset_number', $query)
                    ->groupEnd()
                    ->findAll();
    }

    /**
     * Get interval type text for display
     */
    public static function getIntervalTypeText($intervalType): string
    {
        $types = [
            'daily' => 'Täglich',
            'weekly' => 'Wöchentlich',
            'monthly' => 'Monatlich',
            'quarterly' => 'Quartalsweise',
            'annually' => 'Jährlich',
            'hours' => 'Betriebsstunden',
            'cycles' => 'Zyklen',
            'kilometers' => 'Kilometer'
        ];

        return $types[$intervalType] ?? ucfirst($intervalType);
    }

    /**
     * Get priority color for display
     */
    public static function getPriorityColor($priority): string
    {
        $colors = [
            'low' => 'success',
            'medium' => 'warning', 
            'high' => 'danger',
            'critical' => 'dark'
        ];

        return $colors[$priority] ?? 'secondary';
    }
}