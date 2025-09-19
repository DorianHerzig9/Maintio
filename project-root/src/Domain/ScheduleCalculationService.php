<?php

namespace Src\Domain;

class ScheduleCalculationService
{
    /**
     * Calculate next due date based on interval type and value
     */
    public function calculateNextDueDate(string $intervalType, int $intervalValue, ?\DateTime $fromDate = null): \DateTime
    {
        $date = $fromDate ? clone $fromDate : new \DateTime();

        switch ($intervalType) {
            case 'daily':
                $date->add(new \DateInterval("P{$intervalValue}D"));
                break;
            case 'weekly':
                $date->add(new \DateInterval("P" . ($intervalValue * 7) . "D"));
                break;
            case 'monthly':
                $date->add(new \DateInterval("P{$intervalValue}M"));
                break;
            case 'quarterly':
                $date->add(new \DateInterval("P" . ($intervalValue * 3) . "M"));
                break;
            case 'annually':
                $date->add(new \DateInterval("P{$intervalValue}Y"));
                break;
            case 'hours':
                $date->add(new \DateInterval("PT{$intervalValue}H"));
                break;
            case 'cycles':
            case 'kilometers':
                // For usage-based intervals, default to 30 days as fallback
                $date->add(new \DateInterval("P30D"));
                break;
            default:
                $date->add(new \DateInterval("P30D"));
                break;
        }

        return $date;
    }

    /**
     * Check if maintenance schedule is overdue
     */
    public function isOverdue(\DateTime $nextDue, ?\DateTime $currentDate = null): bool
    {
        $current = $currentDate ?: new \DateTime();
        return $nextDue < $current;
    }

    /**
     * Calculate days until next maintenance
     */
    public function getDaysUntilDue(\DateTime $nextDue, ?\DateTime $currentDate = null): int
    {
        $current = $currentDate ?: new \DateTime();
        $diff = $current->diff($nextDue);

        return $nextDue < $current ? -$diff->days : $diff->days;
    }

    /**
     * Check if maintenance is due within specified days
     */
    public function isDueWithinDays(\DateTime $nextDue, int $days, ?\DateTime $currentDate = null): bool
    {
        $current = $currentDate ?: new \DateTime();
        $futureDate = clone $current;
        $futureDate->add(new \DateInterval("P{$days}D"));

        return $nextDue >= $current && $nextDue <= $futureDate;
    }

    /**
     * Validate interval type
     */
    public function isValidIntervalType(string $intervalType): bool
    {
        $validTypes = ['daily', 'weekly', 'monthly', 'quarterly', 'annually', 'hours', 'cycles', 'kilometers'];
        return in_array($intervalType, $validTypes);
    }

    /**
     * Get human-readable interval text
     */
    public function getIntervalText(string $intervalType): string
    {
        $texts = [
            'daily' => 'Tage',
            'weekly' => 'Wochen',
            'monthly' => 'Monate',
            'quarterly' => 'Quartale',
            'annually' => 'Jahre',
            'hours' => 'Stunden',
            'cycles' => 'Zyklen',
            'kilometers' => 'Kilometer'
        ];

        return $texts[$intervalType] ?? 'Unbekannt';
    }
}