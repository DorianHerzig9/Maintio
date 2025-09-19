<?php

namespace Src\Domain;

class MaintenanceStatisticsService
{
    private ScheduleCalculationService $scheduleCalculationService;

    public function __construct(ScheduleCalculationService $scheduleCalculationService)
    {
        $this->scheduleCalculationService = $scheduleCalculationService;
    }

    /**
     * Calculate maintenance statistics from schedule data
     */
    public function calculateStatistics(array $schedules): array
    {
        $stats = [
            'total_active' => 0,
            'overdue' => 0,
            'upcoming_7_days' => 0,
            'upcoming_30_days' => 0,
            'by_priority' => [
                'critical' => 0,
                'high' => 0,
                'medium' => 0,
                'low' => 0
            ]
        ];

        foreach ($schedules as $schedule) {
            if (!$schedule['is_active']) {
                continue;
            }

            $stats['total_active']++;

            // Check priority
            $priority = $schedule['priority'] ?? 'medium';
            if (isset($stats['by_priority'][$priority])) {
                $stats['by_priority'][$priority]++;
            }

            // Check due dates
            $nextDue = new \DateTime($schedule['next_due']);

            if ($this->scheduleCalculationService->isOverdue($nextDue)) {
                $stats['overdue']++;
            } elseif ($this->scheduleCalculationService->isDueWithinDays($nextDue, 7)) {
                $stats['upcoming_7_days']++;
            } elseif ($this->scheduleCalculationService->isDueWithinDays($nextDue, 30)) {
                $stats['upcoming_30_days']++;
            }
        }

        return $stats;
    }

    /**
     * Filter schedules by status
     */
    public function filterSchedulesByStatus(array $schedules, string $status): array
    {
        return array_filter($schedules, function($schedule) use ($status) {
            $nextDue = new \DateTime($schedule['next_due']);

            switch ($status) {
                case 'overdue':
                    return $this->scheduleCalculationService->isOverdue($nextDue);
                case 'upcoming':
                    return $this->scheduleCalculationService->isDueWithinDays($nextDue, 30);
                case 'active':
                    return $schedule['is_active'];
                default:
                    return true;
            }
        });
    }

    /**
     * Get overdue schedules
     */
    public function getOverdueSchedules(array $schedules): array
    {
        return $this->filterSchedulesByStatus($schedules, 'overdue');
    }

    /**
     * Get upcoming schedules within specified days
     */
    public function getUpcomingSchedules(array $schedules, int $days = 30): array
    {
        return array_filter($schedules, function($schedule) use ($days) {
            $nextDue = new \DateTime($schedule['next_due']);
            return $this->scheduleCalculationService->isDueWithinDays($nextDue, $days)
                   && !$this->scheduleCalculationService->isOverdue($nextDue);
        });
    }

    /**
     * Get priority color for display
     */
    public function getPriorityColor(string $priority): string
    {
        $colors = [
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary'
        ];

        return $colors[$priority] ?? 'info';
    }

    /**
     * Transform statistics for display
     */
    public function formatStatisticsForDisplay(array $stats): array
    {
        $formatted = $stats;

        // Transform by_priority array for easier frontend consumption
        $priorityStats = [];
        foreach ($stats['by_priority'] as $priority => $count) {
            $priorityStats[] = [
                'priority' => $priority,
                'count' => $count,
                'color' => $this->getPriorityColor($priority)
            ];
        }

        $formatted['by_priority'] = $priorityStats;

        return $formatted;
    }
}