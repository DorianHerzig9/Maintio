<?php

namespace Src\Domain;

class ScheduleValidationService
{
    private ScheduleCalculationService $scheduleCalculationService;

    public function __construct(ScheduleCalculationService $scheduleCalculationService)
    {
        $this->scheduleCalculationService = $scheduleCalculationService;
    }

    /**
     * Validate schedule creation data
     */
    public function validateScheduleData(array $data): array
    {
        $errors = [];

        // Required fields
        if (empty($data['asset_id'])) {
            $errors['asset_id'] = 'Anlage ist erforderlich';
        }

        if (empty($data['schedule_name'])) {
            $errors['schedule_name'] = 'Name ist erforderlich';
        }

        if (empty($data['interval_type'])) {
            $errors['interval_type'] = 'Intervalltyp ist erforderlich';
        } elseif (!$this->scheduleCalculationService->isValidIntervalType($data['interval_type'])) {
            $errors['interval_type'] = 'Ungültiger Intervalltyp';
        }

        if (empty($data['interval_value']) || $data['interval_value'] <= 0) {
            $errors['interval_value'] = 'Intervallwert muss größer als 0 sein';
        }

        if (empty($data['priority'])) {
            $errors['priority'] = 'Priorität ist erforderlich';
        } elseif (!$this->isValidPriority($data['priority'])) {
            $errors['priority'] = 'Ungültige Priorität';
        }

        // Business rules
        if (!empty($data['estimated_duration']) && $data['estimated_duration'] < 0) {
            $errors['estimated_duration'] = 'Geschätzte Dauer kann nicht negativ sein';
        }

        if (!empty($data['lead_time_days']) && $data['lead_time_days'] < 0) {
            $errors['lead_time_days'] = 'Vorlaufzeit kann nicht negativ sein';
        }

        return $errors;
    }

    /**
     * Validate priority value
     */
    public function isValidPriority(string $priority): bool
    {
        $validPriorities = ['low', 'medium', 'high', 'critical'];
        return in_array($priority, $validPriorities);
    }

    /**
     * Validate schedule update data
     */
    public function validateScheduleUpdate(array $currentSchedule, array $updateData): array
    {
        $errors = $this->validateScheduleData($updateData);

        // Additional update-specific validations
        if (isset($updateData['is_active']) && !$updateData['is_active'] && $currentSchedule['auto_generate_work_orders']) {
            $errors['is_active'] = 'Inaktive Pläne können keine automatischen Arbeitsaufträge generieren';
        }

        return $errors;
    }

    /**
     * Check if schedule can be completed
     */
    public function canMarkAsCompleted(array $schedule): bool
    {
        return $schedule['is_active']
               && $this->scheduleCalculationService->isOverdue(new \DateTime($schedule['next_due']));
    }

    /**
     * Sanitize and prepare schedule data
     */
    public function sanitizeScheduleData(array $data): array
    {
        $sanitized = [];

        // Required fields
        $sanitized['asset_id'] = (int)($data['asset_id'] ?? 0);
        $sanitized['schedule_name'] = trim($data['schedule_name'] ?? '');
        $sanitized['interval_type'] = trim($data['interval_type'] ?? '');
        $sanitized['interval_value'] = (int)($data['interval_value'] ?? 0);
        $sanitized['priority'] = trim($data['priority'] ?? 'medium');

        // Optional fields
        $sanitized['description'] = trim($data['description'] ?? '');
        $sanitized['task_details'] = trim($data['task_details'] ?? '');
        $sanitized['estimated_duration'] = !empty($data['estimated_duration']) ? (int)$data['estimated_duration'] : null;
        $sanitized['assigned_user_id'] = !empty($data['assigned_user_id']) ? (int)$data['assigned_user_id'] : null;
        $sanitized['category'] = trim($data['category'] ?? '');
        $sanitized['required_tools'] = trim($data['required_tools'] ?? '');
        $sanitized['required_parts'] = trim($data['required_parts'] ?? '');
        $sanitized['safety_notes'] = trim($data['safety_notes'] ?? '');

        // Boolean fields
        $sanitized['auto_generate_work_orders'] = !empty($data['auto_generate_work_orders']);
        $sanitized['is_active'] = !empty($data['is_active']);

        // Lead time with default
        $sanitized['lead_time_days'] = (int)($data['lead_time_days'] ?? 7);

        return $sanitized;
    }

    /**
     * Get validation rules for CodeIgniter
     */
    public function getValidationRules(): array
    {
        return [
            'asset_id' => 'required|integer',
            'schedule_name' => 'required|max_length[200]',
            'interval_type' => 'required|in_list[daily,weekly,monthly,quarterly,annually,hours,cycles,kilometers]',
            'interval_value' => 'required|integer|greater_than[0]',
            'priority' => 'required|in_list[low,medium,high,critical]'
        ];
    }
}