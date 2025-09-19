<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'setting_key', 'setting_value', 'is_global'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_global' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'setting_key' => 'required|max_length[100]',
        'setting_value' => 'permit_empty',
        'is_global' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'setting_key' => [
            'required' => 'Setting key ist erforderlich'
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
     * Get a setting value
     */
    public function getSetting($key, $userId = null, $default = null)
    {
        $builder = $this->where('setting_key', $key);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        } else {
            $builder->where('is_global', true);
        }
        
        $setting = $builder->first();
        
        return $setting ? $setting['setting_value'] : $default;
    }

    /**
     * Set a setting value
     */
    public function setSetting($key, $value, $userId = null, $isGlobal = false)
    {
        $data = [
            'setting_key' => $key,
            'setting_value' => $value,
            'user_id' => $userId,
            'is_global' => $isGlobal
        ];

        $existing = $this->where('setting_key', $key);
        
        if ($userId) {
            $existing->where('user_id', $userId);
        } else {
            $existing->where('is_global', true);
        }
        
        $existingSetting = $existing->first();

        if ($existingSetting) {
            return $this->update($existingSetting['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Get user settings
     */
    public function getUserSettings($userId)
    {
        $settings = $this->where('user_id', $userId)->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }

    /**
     * Get global settings
     */
    public function getGlobalSettings()
    {
        $settings = $this->where('is_global', true)->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }

    /**
     * Delete a setting
     */
    public function deleteSetting($key, $userId = null)
    {
        $builder = $this->where('setting_key', $key);
        
        if ($userId) {
            $builder->where('user_id', $userId);
        } else {
            $builder->where('is_global', true);
        }
        
        return $builder->delete();
    }
}