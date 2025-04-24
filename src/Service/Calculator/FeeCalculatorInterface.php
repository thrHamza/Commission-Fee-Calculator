<?php

namespace App\Service\Calculator;

use App\Entity\Operation;

/**
 * Interface FeeCalculatorInterface
 *
 * Defines the contract for fee calculation on operations.
 */
interface FeeCalculatorInterface
{
    /**
     * Calculates the fee for a given operation.
     *
     * @param Operation $operation The operation to calculate the fee for.
     *
     * @return string The calculated fee as a string.
     */
    public function calculate(Operation $operation): string;
}
