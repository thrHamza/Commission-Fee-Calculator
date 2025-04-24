<?php

namespace App\Service;

use App\Entity\Operation;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Service\Calculator\BusinessWithdrawCalculator;
use App\Service\Calculator\DepositCalculator;
use App\Service\Calculator\FeeCalculatorInterface;
use App\Service\Calculator\PrivateWithdrawCalculator;

/**
 * Service responsible for selecting the correct fee calculator
 * and computing the commission fee for an operation.
 */
class CommissionCalculator
{
    public function __construct(
        private readonly DepositCalculator $depositCalculator,
        private readonly BusinessWithdrawCalculator $businessWithdrawCalculator,
        private readonly PrivateWithdrawCalculator $privateWithdrawCalculator
    ) {
    }

    /**
     * Calculates the commission fee for a given operation.
     *
     * @param Operation $operation The operation to calculate the fee for.
     *
     * @return string The calculated fee as a string.
     */
    public function calculate(Operation $operation): string
    {
        return $this->getCalculator($operation)->calculate($operation);
    }

    /**
     * Selects the appropriate fee calculator based on operation type and user type.
     *
     * @param Operation $operation The operation to analyze.
     *
     * @return FeeCalculatorInterface The appropriate calculator instance.
     */
    private function getCalculator(Operation $operation): FeeCalculatorInterface
    {
        if ($operation->getOperationType() === OperationType::DEPOSIT) {
            return $this->depositCalculator;
        }

        return match ($operation->getUserType()) {
            UserType::BUSINESS => $this->businessWithdrawCalculator,
            UserType::PRIVATE => $this->privateWithdrawCalculator,
        };
    }
}
