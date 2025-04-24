<?php

namespace App\Service;

use App\Entity\Money;
use App\Exception\ExchangeRateFetchFailedException;

/**
 * Service responsible for currency exchange operations.
 */
class ExchangeRateService
{
    /**
     * @var array<string, float>
     */
    private array $rates = [];

    /**
     * ExchangeRateService constructor.
     *
     * @param bool $autoFetch Whether to fetch rates on initialization
     */
    public function __construct(bool $autoFetch = true)
    {
        if ($autoFetch) {
            $this->fetchRates();
        }
    }

    /**
     * Fetches exchange rates from external API.
     *
     * @throws ExchangeRateFetchFailedException
     */
    public function fetchRates(): void
    {
        $retry = 3;

        while ($retry-- > 0) {
            try {
                $response = file_get_contents('https://developers.paysera.com/tasks/api/currency-exchange-rates');
                $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);


                if (!isset($data['rates'])) {
                    throw new \RuntimeException('Rates key not found in response.');
                }

                $this->rates = $data['rates'];
                $this->rates['EUR'] = 1;

                return;
            } catch (\Throwable $e) {

                if ($retry === 0) {
                    throw new ExchangeRateFetchFailedException('API unavailable');
                }
            }
        }
    }

    /**
     * Converts a money amount to EUR.
     *
     * @param Money $money
     * @return Money
     */
    public function convertToEur(Money $money): Money
    {
        $currency = $money->getCurrency();

        if (!isset($this->rates[$currency])) {
            throw new \InvalidArgumentException("Unknown currency: {$currency}");
        }

        $amountEur = (float) $money->getAmount() / $this->rates[$currency];

        return new Money((string) $amountEur, 'EUR');
    }

    /**
     * Converts a EUR amount to target currency.
     *
     * @param float $amountEur Amount in EUR
     * @param string $targetCurrency currency code
     * @return Money
     */
    public function convertFromEur(float $amountEur, string $targetCurrency): Money
    {
        $targetCurrency = strtoupper($targetCurrency);

        if (!isset($this->rates[$targetCurrency])) {
            throw new \InvalidArgumentException("Unknown target currency: {$targetCurrency}");
        }

        $convertedAmount = $amountEur * $this->rates[$targetCurrency];
        return new Money((string) $convertedAmount, $targetCurrency);
    }
}
