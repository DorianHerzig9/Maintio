<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'username', 'email', 'first_name', 'last_name', 'password_hash',
        'role', 'department', 'phone', 'status', 'last_login',
        'avatar', 'preferences'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'integer',
        'last_login' => '?datetime',
        'preferences' => '?json'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'first_name' => 'required|alpha_space|max_length[100]',
        'last_name' => 'required|alpha_space|max_length[100]',
        'password' => 'required|min_length[6]',
        'password_hash' => 'permit_empty',
        'role' => 'required|in_list[admin,manager,technician,viewer]',
        'department' => 'permit_empty|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'status' => 'permit_empty|in_list[active,inactive]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'required' => 'Benutzername ist erforderlich',
            'is_unique' => 'Dieser Benutzername ist bereits vergeben',
            'min_length' => 'Benutzername muss mindestens 3 Zeichen lang sein'
        ],
        'email' => [
            'required' => 'E-Mail-Adresse ist erforderlich',
            'valid_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein',
            'is_unique' => 'Diese E-Mail-Adresse ist bereits registriert'
        ],
        'first_name' => [
            'required' => 'Vorname ist erforderlich'
        ],
        'last_name' => [
            'required' => 'Nachname ist erforderlich'
        ],
        'password' => [
            'required' => 'Passwort ist erforderlich',
            'min_length' => 'Passwort muss mindestens 6 Zeichen lang sein'
        ],
        'role' => [
            'required' => 'Rolle ist erforderlich'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
        } elseif (!isset($data['data']['password_hash']) || empty($data['data']['password_hash'])) {
            // Ensure password_hash is never empty for new users
            if (!isset($data['data']['id'])) {
                throw new \InvalidArgumentException('Passwort ist erforderlich');
            }
        }
        return $data;
    }

    /**
     * Get all users with their role information
     */
    public function getUsersWithRole()
    {
        return $this->select('users.*,
                            CASE
                                WHEN role = \'admin\' THEN \'Administrator\'
                                WHEN role = \'manager\' THEN \'Manager\'
                                WHEN role = \'technician\' THEN \'Techniker\'
                                WHEN role = \'viewer\' THEN \'Betrachter\'
                                ELSE role
                            END as role_name')
                    ->where('deleted_at', null)
                    ->orderBy('last_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get user by username or email
     */
    public function getUserByLogin($login)
    {
        return $this->where('username', $login)
                    ->orWhere('email', $login)
                    ->where('status', 'active')
                    ->where('deleted_at', null)
                    ->first();
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin($userId)
    {
        if ($userId) {
            return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
        }
        return false;
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->where('role', $role)
                    ->where('status', 'active')
                    ->where('deleted_at', null)
                    ->findAll();
    }

    /**
     * Get active technicians for work order assignment
     */
    public function getActiveTechnicians()
    {
        return $this->select('id, username, first_name, last_name, email')
                    ->where('role', 'technician')
                    ->where('status', 'active')
                    ->where('deleted_at', null)
                    ->orderBy('first_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get user statistics optimized with single query
     */
    public function getUserStats()
    {
        $db = \Config\Database::connect();

        $query = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_count,
                    SUM(CASE WHEN role = 'manager' THEN 1 ELSE 0 END) as manager_count,
                    SUM(CASE WHEN role = 'technician' THEN 1 ELSE 0 END) as technician_count,
                    SUM(CASE WHEN role = 'viewer' THEN 1 ELSE 0 END) as viewer_count
                  FROM {$this->table}
                  WHERE deleted_at IS NULL";

        $result = $db->query($query)->getRowArray();

        return [
            'total' => (int) $result['total'],
            'active' => (int) $result['active'],
            'inactive' => (int) $result['inactive'],
            'roles' => [
                ['role' => 'admin', 'count' => (int) $result['admin_count']],
                ['role' => 'manager', 'count' => (int) $result['manager_count']],
                ['role' => 'technician', 'count' => (int) $result['technician_count']],
                ['role' => 'viewer', 'count' => (int) $result['viewer_count']]
            ]
        ];
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Get user safely without sensitive data like password hash
     */
    public function getUserSafe($userId)
    {
        return $this->select('id, username, first_name, last_name, email, role, department, phone, status, created_at, updated_at')
                    ->where('id', $userId)
                    ->where('deleted_at', null)
                    ->first();
    }
}
