<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'email' => 'admin@maintio.com',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'role' => 'admin',
                'department' => 'IT',
                'phone' => '+41 44 123 45 67',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'manager',
                'email' => 'manager@maintio.com',
                'password_hash' => password_hash('manager123', PASSWORD_DEFAULT),
                'first_name' => 'Max',
                'last_name' => 'Mustermann',
                'role' => 'manager',
                'department' => 'Management',
                'phone' => '+41 44 123 45 68',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'techniker1',
                'email' => 'techniker1@maintio.com',
                'password_hash' => password_hash('tech123', PASSWORD_DEFAULT),
                'first_name' => 'Hans',
                'last_name' => 'Müller',
                'role' => 'technician',
                'department' => 'Maintenance',
                'phone' => '+41 44 123 45 69',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'techniker2',
                'email' => 'techniker2@maintio.com',
                'password_hash' => password_hash('tech123', PASSWORD_DEFAULT),
                'first_name' => 'Anna',
                'last_name' => 'Schmidt',
                'role' => 'technician',
                'department' => 'Maintenance',
                'phone' => '+41 44 123 45 70',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'viewer',
                'email' => 'viewer@maintio.com',
                'password_hash' => password_hash('view123', PASSWORD_DEFAULT),
                'first_name' => 'Lisa',
                'last_name' => 'Weber',
                'role' => 'viewer',
                'department' => 'Quality',
                'phone' => '+41 44 123 45 71',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data
        $this->db->table('users')->insertBatch($data);
    }
}