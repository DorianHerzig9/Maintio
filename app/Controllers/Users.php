<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display list of users
     */
    public function index()
    {
        $data = [
            'page_title' => 'Benutzerverwaltung',
            'users' => $this->userModel->getUsersWithRole(),
            'stats' => $this->userModel->getUserStats()
        ];

        return view('users/index', $data);
    }

    /**
     * Show user creation form
     */
    public function create()
    {
        $data = [
            'page_title' => 'Neuen Benutzer erstellen',
            'roles' => $this->getRoles(),
            'departments' => $this->getDepartments()
        ];

        return view('users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $rules = [
            'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'first_name' => 'required|alpha_space|max_length[100]',
            'last_name' => 'required|alpha_space|max_length[100]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role' => 'required|in_list[admin,manager,technician,viewer]',
            'department' => 'permit_empty|max_length[100]',
            'phone' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'password' => $this->request->getPost('password'),
                'role' => $this->request->getPost('role'),
                'department' => $this->request->getPost('department'),
                'phone' => $this->request->getPost('phone'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            $this->userModel->insert($userData);

            return redirect()->to('/users')->with('success', 'Benutzer erfolgreich erstellt');
        } catch (\Exception $e) {
            log_message('error', 'User creation error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Fehler beim Erstellen des Benutzers: ' . $e->getMessage());
        }
    }

    /**
     * Display user details
     */
    public function show($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Benutzer nicht gefunden');
        }

        $data = [
            'page_title' => 'Benutzer Details',
            'user' => $user,
            'role_name' => $this->getRoleName($user['role'])
        ];

        return view('users/show', $data);
    }

    /**
     * Show user edit form
     */
    public function edit($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Benutzer nicht gefunden');
        }

        $data = [
            'page_title' => 'Benutzer bearbeiten',
            'user' => $user,
            'roles' => $this->getRoles(),
            'departments' => $this->getDepartments()
        ];

        return view('users/edit', $data);
    }

    /**
     * Update user
     */
    public function update($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Benutzer nicht gefunden');
        }

        $rules = [
            'username' => "required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'first_name' => 'required|alpha_space|max_length[100]',
            'last_name' => 'required|alpha_space|max_length[100]',
            'role' => 'required|in_list[admin,manager,technician,viewer]',
            'department' => 'permit_empty|max_length[100]',
            'phone' => 'permit_empty|max_length[20]'
        ];

        // Only validate password if provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'role' => $this->request->getPost('role'),
                'department' => $this->request->getPost('department'),
                'phone' => $this->request->getPost('phone'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Only update password if provided
            if ($this->request->getPost('password')) {
                $userData['password'] = $this->request->getPost('password');
            }

            $this->userModel->update($id, $userData);

            return redirect()->to('/users')->with('success', 'Benutzer erfolgreich aktualisiert');
        } catch (\Exception $e) {
            log_message('error', 'User update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Fehler beim Aktualisieren des Benutzers: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function delete($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'Benutzer nicht gefunden');
        }

        try {
            $this->userModel->delete($id);
            return redirect()->to('/users')->with('success', 'Benutzer erfolgreich gelöscht');
        } catch (\Exception $e) {
            log_message('error', 'User deletion error: ' . $e->getMessage());
            return redirect()->to('/users')->with('error', 'Fehler beim Löschen des Benutzers: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Benutzer nicht gefunden']);
        }

        try {
            $newStatus = $user['is_active'] ? 0 : 1;
            $this->userModel->update($id, ['is_active' => $newStatus]);

            $message = $newStatus ? 'Benutzer aktiviert' : 'Benutzer deaktiviert';
            return $this->response->setJSON(['success' => true, 'message' => $message, 'status' => $newStatus]);
        } catch (\Exception $e) {
            log_message('error', 'User status toggle error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Fehler beim Ändern des Status']);
        }
    }

    /**
     * Get available roles
     */
    private function getRoles()
    {
        return [
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'technician' => 'Techniker',
            'viewer' => 'Betrachter'
        ];
    }

    /**
     * Get role name
     */
    private function getRoleName($role)
    {
        $roles = $this->getRoles();
        return $roles[$role] ?? $role;
    }

    /**
     * Get departments
     */
    private function getDepartments()
    {
        return [
            'Maintenance' => 'Instandhaltung',
            'Engineering' => 'Technik',
            'Operations' => 'Betrieb',
            'Management' => 'Management',
            'IT' => 'IT',
            'Quality' => 'Qualität',
            'Safety' => 'Sicherheit'
        ];
    }
}