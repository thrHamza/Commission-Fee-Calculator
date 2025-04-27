# Commission Fee Calculator

A Symfony console application that processes financial transactions from CSV files and calculates commission fees according to specific business rules.

## Features

- Process deposit and withdrawal operations
- Handle different commission rules for:
    - Private clients (weekly free withdrawal limits)
    - Business clients (fixed percentage fees)
- Automatic currency conversion using exchange rates
- Currency-specific rounding rules
- Comprehensive validation of input data

## Requirements

- PHP 8.1+
- Symfony 6.4.*
- Composer (for dependency management)

## Installation

1. Clone repository
```
git clone https://github.com/thrHamza/commission-fee-calculator.git
cd commission-calculator
```
2. Install dependencies
```
composer install
```

## Configuration

Environment variables (.env):
```
# Exchange rate API endpoint
EXCHANGE_RATE_API_URL=https://api.exchangerate.host/latest
```

# Usage

1. Process CSV file:
```
symfony console app:calculate-commission path/to/input.csv
```

Sample Input (input.csv):
```
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
...
```

Expected Output:
```
0.60
3.00
...
```

#### **Fixtures**:
- tests/Fixtures/input.csv - Sample transaction data
- tests/Fixtures/expected_output.txt - Expected results
***

2. Available Console Commands

Dump parsed Operation objects for sanity checking
```
symfony console app:test:csv-reader
```
Fetch live rates & convert a sample amount
```
symfony console app:test:exchange-rate
```

## Testing

Run the automated test suite:
```
php bin/phpunit
```

Expected output:
```
OK (1 tests, 1 assertions)
```

## Code Examples

#### Adding New Currency (ex. GBP)

1.  Add currency to `OperationValidator` class validation:

```
// src/Validator/OperationValidator.php
    private function validateCurrency(string $currency): void
    {
        $currency = strtoupper(trim($currency));
        if (!in_array($currency, ['EUR', 'USD', 'JPY', 'GBP'], true)) {
            throw new InvalidOperationException(
                sprintf('Invalid currency. Expected: EUR, USD, JPY, GBP, got: %s', $currency)
            );
        }
    }
```

2.  Update rounding rules in `FeeRounder`:
```
// src/Service/FeeRounder.php
    private function getPrecision(string $currency): int
    {
        $precision = [
        'JPY' => 0, 
        'EUR' => 2, 
        'USD' => 2,
        'GBP' => 0 // uses 0 decimals
        ];

        return $precision[$currency] ?? 2;
    }
```