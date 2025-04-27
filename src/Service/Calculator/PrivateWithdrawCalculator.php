<?php

namespace App\Service\Calculator;

use App\Entity\Operation;
use App\Service\Checker\WeeklyWithdrawalChecker;
use App\Service\ExchangeRateService;
use App\Service\FeeRounder;
use App\Service\Tracker\WeeklyWithdrawalTracker;

/**
 * Calculator for private user withdrawal operations.
 *
 *
 * Handles the 1000 EUR/week free limit rule:
 * - First 3 withdrawals free (amounts summed in EUR)
 * - 0.3% fee on excess amounts
 * - Week reset every Monday
 *
 * Currency conversion applied before limit checks.
 */
class PrivateWithdrawCalculator implements FeeCalculatorInterface
{
    /**
     * The fee rate applied to the excess amount (0.3%).
     */
    private const FEE_RATE = 0.003;

    /**
     * Constructor.
     *
     * @param WeeklyWithdrawalTracker $tracker Service to track weekly withdrawals.
     * @param WeeklyWithdrawalChecker $checker Service to check weekly limits.
     * @param ExchangeRateService     $exchangeService Currency conversion service.
     * @param FeeRounder              $feeRounder Fee rounding service.
     */
    public function __construct(
        private readonly WeeklyWithdrawalTracker $tracker,
        private readonly WeeklyWithdrawalChecker $checker,
        private readonly ExchangeRateService $exchangeService,
        private readonly FeeRounder $feeRounder
    ) {
    }

    /**
     * Calculates the fee for a private user withdrawal operation.
     *
     * @param Operation $operation The withdrawal operation.
     *
     * @return string The calculated and rounded fee.
     */
    public function calculate(Operation $operation): string
    {
        $money = $operation->getAmount();
        $amountEur = $this->exchangeService->convertToEur($money);
        $weekKey = $this->getWeekKey($operation->getDate());

        $excessAmount = $this->checker->calculateExcess(
            $operation->getUserId(),
            $weekKey,
            (float) $amountEur->getAmount()
        );

        $feeEur = $excessAmount * self::FEE_RATE;
        $fee = $this->exchangeService->convertFromEur($feeEur, $money->getCurrency());

        $this->tracker->trackWithdrawal(
            $operation->getUserId(),
            $weekKey,
            (float) $amountEur->getAmount()
        );

        return $this->feeRounder->round($fee->getAmount(), $money->getCurrency());
    }

    /**
     * Generates a unique key based on the year and ISO week number.
     *
     * @param \DateTimeInterface $date The operation date.
     *
     * @return string Week key in "YYYY-WW" format.
     */
    private function getWeekKey(\DateTimeInterface $date): string
    {
        return $date->format('o-W');
    }
}
