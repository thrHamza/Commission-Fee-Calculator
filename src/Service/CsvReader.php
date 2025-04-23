<?php

namespace App\Service;

use App\Entity\Money;
use App\Entity\Operation;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Validator\OperationValidator;
use DateTime;
use Generator;
use RuntimeException;

/**
 * Service responsible for reading operations from CSV files.
 */
class CsvReader
{
    /**
     * @var OperationValidator
     */
    private OperationValidator $validator;

    /**
     * @param OperationValidator $validator
     */
    public function __construct(OperationValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Reads CSV file and yields Operation objects.
     *
     * @param string $filePath Path to CSV file
     * @return Generator Operation[]
     *
     * @throws RuntimeException When file cannot be read
     */
    public function read(string $filePath): Generator
    {
        $this->validateFile($filePath);

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new RuntimeException("Could not open file: {$filePath}");
        }

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $this->validator->validate($row);
                yield $this->createOperationFromRow($row);
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * Validates file existence and readability.
     *
     * @param string $filePath
     * @throws RuntimeException
     */
    private function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new RuntimeException("File not readable: {$filePath}");
        }
    }

    /**
     * Creates Operation from validated CSV row.
     *
     * @param array $row
     * @return Operation
     */
    private function createOperationFromRow(array $row): Operation
    {
        return new Operation(
            DateTime::createFromFormat('Y-m-d', $row[0]),
            (int) $row[1],
            UserType::from(trim($row[2])),
            OperationType::from(trim($row[3])),
            new Money($row[4], trim($row[5]))
        );
    }
}