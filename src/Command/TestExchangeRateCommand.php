<?php

namespace App\Command;

use App\Entity\Money;
use App\Service\ExchangeRateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test:exchange-rate',
    description: 'Test fetching exchange rates from the external API.',
)]
class TestExchangeRateCommand extends Command
{
    private ExchangeRateService $exchangeRateService;

    /**
     * @param ExchangeRateService $exchangeRateService
     */
    public function __construct(ExchangeRateService $exchangeRateService)
    {
        parent::__construct();
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Executes the exchange rate test command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->exchangeRateService->fetchRates();
            $money = new Money('100.00', 'USD');
            $converted = $this->exchangeRateService->convertToEur($money);

            $output->writeln(sprintf(
                'Converted %s %s to EUR: %s',
                $money->getAmount(),
                $money->getCurrency(),
                $converted->getAmount()
            ));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
