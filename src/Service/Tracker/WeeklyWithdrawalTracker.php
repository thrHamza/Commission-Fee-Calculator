<?php

namespace App\Service\Tracker;

/**
 * Tracks the number and total amount of weekly withdrawals per user.
 *
 *
 *  Tracks per-user weekly withdrawals using composite keys:
 *  {user_id}_{ISO_week_number} (ex. "4_2025-W17")
 *
 *  Storage structure:
 *  [
 *    "4_2025-W17" => ['count' => 2, 'total' => 500.00]
 *  ]
 */
class WeeklyWithdrawalTracker
{
    /**
     * @var array<string, array{count: int, total: float}>
     */
    private array $storage = [];

    /**
     * Tracks a withdrawal operation.
     *
     * @param int    $userId   The ID of the user.
     * @param string $weekKey  The ISO week identifier (ex, "2025-17").
     * @param float  $amount   The amount withdrawn in EUR.
     *
     * @return void
     */
    public function trackWithdrawal(int $userId, string $weekKey, float $amount): void
    {
        $key = $this->getStorageKey($userId, $weekKey);

        $this->storage[$key] = [
            'count' => ($this->storage[$key]['count'] ?? 0) + 1,
            'total' => ($this->storage[$key]['total'] ?? 0.0) + $amount,
        ];
    }

    /**
     * Retrieves the number of withdrawals and total amount for a user in a given week.
     *
     * @param int    $userId  The ID of the user.
     * @param string $weekKey The ISO week identifier.
     *
     * @return array{count: int, total: float}
     */
    public function getWeeklyData(int $userId, string $weekKey): array
    {
        return $this->storage[$this->getStorageKey($userId, $weekKey)] ?? [
            'count' => 0,
            'total' => 0.0,
        ];
    }

    /**
     * Builds a unique key for the user-week combination.
     *
     * @param int    $userId  The ID of the user.
     * @param string $weekKey The ISO week identifier.
     *
     * @return string The combined storage key.
     */
    private function getStorageKey(int $userId, string $weekKey): string
    {
        return sprintf('%d_%s', $userId, $weekKey);
    }
}
