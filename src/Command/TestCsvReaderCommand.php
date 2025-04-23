<?php

namespace App\Command;

use App\Service\CsvReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to test reading operations from a CSV file.
 */
#[AsCommand(
    name: 'app:test:csv-reader',
    description: 'Test reading operations from a CSV file.'
)]
class TestCsvReaderCommand extends Command
{
    /**
     * @var CsvReader
     */
    private CsvReader $csvReader;

    /**
     * Constructor.
     *
     * @param CsvReader $csvReader Service to read CSV operations
     */
    public function __construct(CsvReader $csvReader)
    {
        parent::__construct();
        $this->csvReader = $csvReader;
    }

    /**
     * Configures the command input arguments.
     */
    protected function configure(): void
    {
        $this->addArgument('file_path', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int Command exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file_path');

        try {
            foreach ($this->csvReader->read($filePath) as $operation) {
                $output->writeln(sprintf(
                    'Operation: %s | User #%d (%s) | %s | %s %s',
                    $operation->getDate()->format('Y-m-d'),
                    $operation->getUserId(),
                    $operation->getUserType()->value,
                    $operation->getOperationType()->value,
                    $operation->getAmount()->getAmount(),
                    $operation->getAmount()->getCurrency()
                ));
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
