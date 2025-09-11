<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username', 'email', 'password', 'first_name', 'last_name', 
        'role', 'status', 'last_login'
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
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|max_length[150]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'first_name' => 'required|max_length[100]',
        'last_name'  => 'required|max_length[100]',
        'role'       => 'required|in_list[administrator,techniker,benutzer]',
        'status'     => 'required|in_list[active,inactive]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'required' => 'Benutzername ist erforderlich',
            'min_length' => 'Benutzername muss mindestens 3 Zeichen lang sein',
            'is_unique' => 'Dieser Benutzername ist bereits vergeben'
        ],
        'email' => [
            'required' => 'E-Mail-Adresse ist erforderlich',
            'valid_email' => 'Bitte eine gültige E-Mail-Adresse eingeben',
            'is_unique' => 'Diese E-Mail-Adresse ist bereits registriert'
        ],
        'password' => [
            'required' => 'Passwort ist erforderlich',
            'min_length' => 'Passwort muss mindestens 6 Zeichen lang sein'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Hash password before inserting/updating
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Verify user password
     */
    public function verifyPassword($username, $password)
    {
        $user = $this->where('username', $username)->where('status', 'active')->first();
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return $user;
        }
        
        return false;
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->where('role', $role)->where('status', 'active')->findAll();
    }

    /**
     * Get user without password field
     */
    public function getUserSafe($id)
    {
        $user = $this->find($id);
        if ($user) {
            unset($user['password']);
        }
        return $user;
    }
}
