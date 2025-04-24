<?php

namespace App\Service\Checker;

use App\Service\Tracker\WeeklyWithdrawalTracker;

/**
 * Checks if a user's weekly withdrawals exceed free limits.
 */
class WeeklyWithdrawalChecker
{
    private const FREE_WITHDRAWALS = 3;
    private const FREE_AMOUNT = 1000.00;

    /**
     * @param WeeklyWithdrawalTracker $tracker
     */
    public function __construct(private readonly WeeklyWithdrawalTracker $tracker)
    {
    }

    /**
     * Calculates the amount that exceeds the weekly free withdrawal limit.
     *
     * @param int    $userId   The ID of the user.
     * @param string $weekKey  The ISO week identifier (ex, "2025-17").
     * @param float  $amount   The current operation amount in EUR.
     *
     * @return float The amount that exceeds the free threshold.
     */
    public function calculateExcess(int $userId, string $weekKey, float $amount): float
    {
        $history = $this->tracker->getWeeklyData($userId, $weekKey);
        $remainingFree = self::FREE_AMOUNT - $history['total'];

        if ($history['count'] >= self::FREE_WITHDRAWALS) {
            return $amount;
        }

        if ($remainingFree <= 0) {
            return $amount;
        }

        $applicableFree = self::FREE_AMOUNT - $history['total'];
        $excess = $amount - $applicableFree;

        return max($excess, 0.0);
    }
}
