<?php

namespace App\Entity;

use App\Enum\OperationType;
use App\Enum\UserType;
use DateTime;

/**
 * Represents operation (deposit/withdrawal).
 */
class Operation
{
    /**
     * Date of the operation.
     *
     * @var DateTime
     */
    private DateTime $date;

    /**
     * User identifier.
     *
     * @var int
     */
    private int $userId;

    /**
     * Type of user.
     *
     * @var UserType
     */
    private UserType $userType;

    /**
     * Type of operation.
     *
     * @var OperationType
     */
    private OperationType $operationType;

    /**
     * Amount of the operation.
     *
     * @var Money
     */
    private Money $amount;

    /**
     * @param DateTime      $date          Operation date
     * @param int           $userId        User identifier
     * @param UserType      $userType      Type of user
     * @param OperationType $operationType Type of operation
     * @param Money         $amount        Money amount
     */
    public function __construct(
        DateTime $date,
        int $userId,
        UserType $userType,
        OperationType $operationType,
        Money $amount
    ) {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
    }

    /**
     * Gets the operation date.
     *
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * Gets the user identifier.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Gets the user type.
     *
     * @return UserType
     */
    public function getUserType(): UserType
    {
        return $this->userType;
    }

    /**
     * Gets the operation type.
     *
     * @return OperationType
     */
    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    /**
     * Gets the monetary amount.
     *
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }
}