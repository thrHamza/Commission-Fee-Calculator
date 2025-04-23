<?php
namespace App\Enum;

/**
 * Provides common enum helper methods.
 */
trait EnumValidationTrait
{
    /**
     * Return all the raw enum values.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    /**
     * Check if a given string matches one of the enum’s values (case‑insensitive).
     *
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        return in_array(
            strtolower($value),
            array_map(fn($e) => $e->value, self::cases()),
            true
        );
    }
}
