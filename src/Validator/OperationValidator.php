<?php

namespace App\Validator;

use App\Enum\OperationType;
use App\Enum\UserType;
use App\Exception\InvalidOperationException;
use DateTime;

/**
 * Validates CSV rows for operation data integrity.
 */
class OperationValidator
{
    private const REQUIRED_COLUMNS = 6;
    private const DATE_FORMAT = 'Y-m-d';

    /**
     * @param array $row CSV row data
     * @throws InvalidOperationException
     */
    public function validate(array $row): void
    {
        $this->validateColumnCount($row);
        $this->validateDate($row[0]);
        $this->validateUserId($row[1]);
        $this->validateUserType($row[2]);
        $this->validateOperationType($row[3]);
        $this->validateAmount($row[4]);
        $this->validateCurrency($row[5]);
    }

    /**
     * @param array $row
     * @throws InvalidOperationException
     */
    private function validateColumnCount(array $row): void
    {
        if (count($row) !== self::REQUIRED_COLUMNS) {
            throw new InvalidOperationException(
                sprintf('Invalid column count. Expected %d, got %d', self::REQUIRED_COLUMNS, count($row))
            );
        }
    }

    /**
     * @param string $date
     * @throws InvalidOperationException
     */
    private function validateDate(string $date): void
    {
        $d = DateTime::createFromFormat(self::DATE_FORMAT, $date);
        if (!$d || $d->format(self::DATE_FORMAT) !== $date) {
            throw new InvalidOperationException("Invalid date format. Expected Y-m-d, got: {$date}");
        }
    }

    /**
     * @param string $userId
     * @throws InvalidOperationException
     */
    private function validateUserId(string $userId): void
    {
        if (!ctype_digit($userId)) {
            throw new InvalidOperationException("User ID must be numeric, got: {$userId}");
        }
    }

    /**
     * @param string $userType
     * @throws InvalidOperationException
     */
    private function validateUserType(string $userType): void
    {
        if (!UserType::isValid($userType)) {
            throw new InvalidOperationException(
                sprintf('Invalid user type. Expected: %s, got: %s',
                    implode(', ', UserType::values()),
                    $userType
                )
            );
        }
    }

    /**
     * @param string $operationType
     * @throws InvalidOperationException
     */
    private function validateOperationType(string $operationType): void
    {
        if (!OperationType::isValid($operationType)) {
            throw new InvalidOperationException(
                sprintf('Invalid operation type. Expected: %s, got: %s',
                    implode(', ', OperationType::values()),
                    $operationType
                )
            );
        }
    }

    /**
     * @param string $amount
     * @throws InvalidOperationException
     */
    private function validateAmount(string $amount): void
    {
        if (!is_numeric($amount)) {
            throw new InvalidOperationException("Amount must be numeric, got: {$amount}");
        }
    }

    /**
     * @param string $currency
     * @throws InvalidOperationException
     */
    private function validateCurrency(string $currency): void
    {
        $currency = strtoupper(trim($currency));
        if (!in_array($currency, ['EUR', 'USD', 'JPY'], true)) {
            throw new InvalidOperationException(
                sprintf('Invalid currency. Expected: EUR, USD, JPY, got: %s', $currency)
            );
        }
    }
}