<?php

namespace App\Enum;

enum OperationType: string
{
    use EnumValidationTrait;

    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}