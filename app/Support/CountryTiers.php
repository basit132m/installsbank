<?php

namespace App\Support;

/**
 * Single source of truth for country → tier mapping and the geo-targeting
 * rules used by advertiser install-campaign links.
 */
class CountryTiers
{
    public const TIER1 = ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'NL', 'SE', 'NO', 'DK'];
    public const TIER2 = ['ES', 'IT', 'PT', 'PL', 'CZ', 'HU', 'RO', 'GR', 'TR', 'AE'];

    public const MODE_MIXED  = 'mixed';   // worldwide
    public const MODE_TIER1  = 'tier1';   // tier 1 only
    public const MODE_TIER23 = 'tier23';  // tier 2 & 3

    public static function modes(): array
    {
        return [
            self::MODE_MIXED  => 'Mixed (worldwide)',
            self::MODE_TIER1  => 'Tier 1 only',
            self::MODE_TIER23 => 'Tier 2 & 3',
        ];
    }

    public static function modeLabel(?string $mode): string
    {
        return self::modes()[$mode] ?? self::modes()[self::MODE_MIXED];
    }

    public static function tierOf(?string $code): int
    {
        $code = strtoupper((string) $code);
        if (in_array($code, self::TIER1, true)) return 1;
        if (in_array($code, self::TIER2, true)) return 2;
        return 3;
    }

    /** Does the country satisfy the geo mode (before applying exclusions)? */
    public static function matchesMode(?string $code, ?string $mode): bool
    {
        return match ($mode) {
            self::MODE_TIER1  => self::tierOf($code) === 1,
            self::MODE_TIER23 => in_array(self::tierOf($code), [2, 3], true),
            default           => true, // mixed
        };
    }

    /**
     * Final allow decision for a visitor: matches the mode AND not excluded.
     * Unknown geo (XX/empty) is allowed only in Mixed mode, since we can't
     * verify its tier — this keeps tier campaigns clean.
     */
    public static function isAllowed(?string $code, ?string $mode, array $excluded = []): bool
    {
        $code = strtoupper((string) $code);

        if ($code === '' || $code === 'XX') {
            return ($mode ?? self::MODE_MIXED) === self::MODE_MIXED;
        }

        $excluded = array_map('strtoupper', $excluded);
        if (in_array($code, $excluded, true)) {
            return false;
        }

        return self::matchesMode($code, $mode);
    }
}
