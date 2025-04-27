<?php


namespace App\Tests;

use App\Command\CalculateCommissionCommand;
use App\Service\Calculator\BusinessWithdrawCalculator;
use App\Service\Calculator\DepositCalculator;
use App\Service\Calculator\PrivateWithdrawCalculator;
use App\Service\Checker\WeeklyWithdrawalChecker;
use App\Service\CommissionCalculator;
use App\Service\CsvReader;
use App\Service\ExchangeRateService;
use App\Service\FeeRounder;
use App\Service\Tracker\WeeklyWithdrawalTracker;
use App\Validator\OperationValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test: read operations from CSV, calculate fees
 * and compare against expected output.
 */
class CommissionCalculatorTest extends TestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // Configure exchange service with test rates
        $exchangeService = new ExchangeRateService(false,false);
        $exchangeService->setRates([
            'USD' => 1.1497,
            'JPY' => 129.53,
            'EUR' => 1,
        ]);

        // Initialize components
        $weeklyWithdrawalTracker = new WeeklyWithdrawalTracker();
        $weeklyWithdrawalChecker = new WeeklyWithdrawalChecker($weeklyWithdrawalTracker);
        $privateWithdrawCalculator = new PrivateWithdrawCalculator(
            $weeklyWithdrawalTracker,
            $weeklyWithdrawalChecker,
            $exchangeService,
            new FeeRounder()
        );

        $commissionCalculator = new CommissionCalculator(
            new DepositCalculator(new FeeRounder()),
            new BusinessWithdrawCalculator(new FeeRounder()),
            $privateWithdrawCalculator
        );

        $this->commandTester = new CommandTester(
            (new Application())->add(new CalculateCommissionCommand(
                new CsvReader(new OperationValidator()),
                $commissionCalculator
            ))
        );
    }

    /**
     *  Processes CSV input and matches expected output exactly.
     *
     * @return void
     */
    public function testCSVInputMatchesExpectedOutput(): void
    {
        $inputFile = __DIR__ . '/Fixtures/input.csv';

        // Read and normalize expected output.
        $expected = array_map(
            'trim',
            file(__DIR__ . '/Fixtures/expected_output.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        );

        // Execute the command.
        $this->commandTester->execute([
            'file_path' => $inputFile,
        ]);

        // Split and normalize actual output.
        $actual = preg_split('/\R/', trim($this->commandTester->getDisplay())) ?: [];
        $actual = array_map('trim', $actual);

        $this->assertSame(
            $expected,
            $actual,
            "Output Data:\nExpected:\n" . implode("\n", $expected)
            . "\nActual:\n" . implode("\n", $actual)
        );
    }

}