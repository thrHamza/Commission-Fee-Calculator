<?php

namespace App\Service\Calculator;

use App\Entity\Operation;
use App\Service\FeeRounder;

/**
 * Calculator for business user withdrawal operations.
 */
class BusinessWithdrawCalculator implements FeeCalculatorInterface
{
    /**
     * The fixed fee rate for business withdrawals (0.5%).
     */
    private const FEE_RATE = 0.005;

    /**
     * Constructor.
     *
     * @param FeeRounder $feeRounder Service responsible for rounding fees.
     */
    public function __construct(private readonly FeeRounder $feeRounder)
    {
    }

    /**
     * Calculates the fee for a business withdrawal operation.
     *
     * @param Operation $operation The withdrawal operation.
     *
     * @return string The rounded fee amount as a string.
     */
    public function calculate(Operation $operation): string
    {
        $amount = (float) $operation->getAmount()->getAmount();
        $fee = $amount * self::FEE_RATE;

        return $this->feeRounder->round($fee, $operation->getAmount()->getCurrency());
    }
}
