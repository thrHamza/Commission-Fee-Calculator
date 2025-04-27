<?php

namespace App\Service;

/**
 * Service responsible for rounding fee amounts based on currency rules.
 *
 *  Currency-specific rounding:
 *  - JPY: Round up to whole numbers (0 decimals)
 *  - Others: Round up to 2 decimal places
 *
 *  Uses mathematical ceiling with multiplier technique:
 *  1. Multiply by 10^decimals
 *  2. Apply ceil()
 *  3. Divide back
 */
class FeeRounder
{
    /**
     * Rounds a money amount according to currency-specific precision.
     *
     * @param float  $amount   The raw fee amount to round.
     * @param string $currency The currency code (ex, "EUR", "USD", "JPY").
     *
     * @return string Rounded fee as a string formatted with the correct precision.
     */
    public function round(float $amount, string $currency): string
    {
        $precision = $this->getPrecision($currency);
        $multiplier = 10 ** $precision;
        $rounded = ceil($amount * $multiplier) / $multiplier;

        return number_format($rounded, $precision, '.', '');
    }

    /**
     * Gets the rounding precision for a specific currency.
     *
     * @param string $currency The currency code.
     *
     * @return int The number of decimal places.
     */
    private function getPrecision(string $currency): int
    {
        $precision = ['JPY' => 0, 'EUR' => 2, 'USD' => 2];
        return $precision[$currency] ?? 2;
    }
}
