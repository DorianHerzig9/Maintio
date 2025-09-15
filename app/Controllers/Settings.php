<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $userModel;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Get global settings
        $globalSettings = $this->settingsModel->getGlobalSettings();

        // Default settings if they don't exist
        $defaultSettings = [
            'app_name' => 'Asset Management System',
            'company_name' => 'Ihr Unternehmen',
            'company_address' => '',
            'company_email' => '',
            'company_phone' => '',
            'timezone' => 'Europe/Zurich',
            'date_format' => 'd.m.Y',
            'time_format' => 'H:i',
            'language' => 'de',
            'maintenance_interval_default' => '30',
            'notification_email' => '',
            'email_notifications' => '1',
            'auto_assign_work_orders' => '0',
            'work_order_prefix' => 'WO-',
            'asset_prefix' => 'A-'
        ];

        // Merge with existing settings
        $settings = array_merge($defaultSettings, $globalSettings);

        // Load language from settings or session
        $language = $settings['language'] ?? session()->get('language', 'de');
        session()->set('language', $language);

        $data = [
            'page_title' => $language === 'en' ? 'System Settings' : 'Systemeinstellungen',
            'settings' => $settings
        ];

        return view('settings/index', $data);
    }

    public function update()
    {
        $rules = [
            'app_name' => 'required|max_length[200]',
            'company_name' => 'required|max_length[200]',
            'company_address' => 'permit_empty|max_length[500]',
            'company_email' => 'permit_empty|valid_email',
            'company_phone' => 'permit_empty|max_length[50]',
            'timezone' => 'required',
            'date_format' => 'required',
            'time_format' => 'required',
            'language' => 'required|in_list[de,en]',
            'maintenance_interval_default' => 'required|integer|greater_than[0]',
            'notification_email' => 'permit_empty|valid_email',
            'email_notifications' => 'permit_empty|in_list[0,1]',
            'auto_assign_work_orders' => 'permit_empty|in_list[0,1]',
            'work_order_prefix' => 'permit_empty|max_length[10]',
            'asset_prefix' => 'permit_empty|max_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Get all post data
            $postData = $this->request->getPost();

            // Save each setting
            foreach ($postData as $key => $value) {
                if ($key !== 'csrf_token_name' && $key !== csrf_token()) {
                    $this->settingsModel->setSetting($key, $value, null, true);
                }
            }

            // Set the language in session for immediate effect
            if (isset($postData['language'])) {
                session()->set('language', $postData['language']);
            }

            $successMessage = session()->get('language') === 'en'
                ? 'Settings saved successfully'
                : 'Einstellungen erfolgreich gespeichert';

            return redirect()->to('/settings')->with('success', $successMessage);
        } catch (\Exception $e) {
            log_message('error', 'Settings update error: ' . $e->getMessage());

            $errorMessage = session()->get('language') === 'en'
                ? 'Error saving settings: ' . $e->getMessage()
                : 'Fehler beim Speichern der Einstellungen: ' . $e->getMessage();

            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }

    public function profile()
    {
        // User-specific settings (placeholder for future implementation)
        $data = [
            'page_title' => 'Benutzereinstellungen'
        ];

        return view('settings/profile', $data);
    }

    public function backup()
    {
        // Database backup functionality (placeholder for future implementation)
        $data = [
            'page_title' => 'Backup & Wiederherstellung'
        ];

        return view('settings/backup', $data);
    }

    public function logs()
    {
        // System logs view (placeholder for future implementation)
        $data = [
            'page_title' => 'Systemlogs'
        ];

        return view('settings/logs', $data);
    }
}