<?php

namespace App\Service\Calculator;

use App\Entity\Operation;
use App\Service\FeeRounder;

/**
 * Service responsible for calculating fees on deposit operations.
 */
class DepositCalculator implements FeeCalculatorInterface
{
    /**
     * The fixed fee rate for deposits (0.03%).
     */
    private const FEE_RATE = 0.0003;

    /**
     * Constructor.
     *
     * @param FeeRounder $feeRounder Service to round calculated fees.
     */
    public function __construct(private readonly FeeRounder $feeRounder)
    {
    }

    /**
     * Calculates the fee for a deposit operation.
     *
     * @param Operation $operation The deposit operation.
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
