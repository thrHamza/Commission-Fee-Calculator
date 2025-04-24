<?php

namespace App\Command;

use App\Service\CommissionCalculator;
use App\Service\CsvReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to calculate commission fees from a CSV file.
 */
#[AsCommand(name: 'app:calculate-commission')]
class CalculateCommissionCommand extends Command
{
    /**
     * @param CsvReader $csvReader CSV reader service for parsing input file
     * @param CommissionCalculator $commissionCalculator Calculator service for computing fees
     */
    public function __construct(
        private readonly CsvReader $csvReader,
        private readonly CommissionCalculator $commissionCalculator
    ) {
        parent::__construct();
    }

    /**
     * Configures the command's name, description, and expected arguments.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('file_path', InputArgument::REQUIRED, 'Path to CSV file')
            ->setDescription('Calculates commission fees for financial operations');
    }

    /**
     * Executes the commission calculation and outputs the result.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int Exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('file_path');

        try {
            foreach ($this->csvReader->read($filename) as $operation) {
                $fee = $this->commissionCalculator->calculate($operation);
                $output->writeln($fee);
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

}
