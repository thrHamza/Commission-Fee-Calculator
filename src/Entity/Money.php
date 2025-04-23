<?php

namespace App\Entity;

use InvalidArgumentException;

/**
 * Represents a money value with currency.
 */
class Money
{
    /**
     * Decimal amount represented as string for precision.
     *
     * @var string
     */
    protected string $amount;

    /**
     * Currency code.
     *
     * @var string
     */
    protected string $currency;

    /**
     * @param string $amount   Decimal amount as string
     * @param string $currency Currency code
     */
    public function __construct(string $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    /**
     * Gets the decimal amount as string.
     *
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Gets the currency code.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * String representation of money (amount + currency).
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getAmount() . ' ' . $this->getCurrency();
    }
}