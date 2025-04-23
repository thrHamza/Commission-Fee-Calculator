<?php

namespace App\Enum;

enum UserType: string
{
    use EnumValidationTrait;

    case PRIVATE = 'private';
    case BUSINESS = 'business';
}