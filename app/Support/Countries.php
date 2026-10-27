<?php

namespace App\Support;

/**
 * A practical ISO country list for the advertiser geo-exclusion picker,
 * grouped by tier so "Tier 1 except Canada" style choices are intuitive.
 */
class Countries
{
    public const LIST = [
        'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada', 'AU' => 'Australia',
        'DE' => 'Germany', 'FR' => 'France', 'NL' => 'Netherlands', 'SE' => 'Sweden',
        'NO' => 'Norway', 'DK' => 'Denmark', 'IE' => 'Ireland', 'CH' => 'Switzerland',
        'AT' => 'Austria', 'BE' => 'Belgium', 'FI' => 'Finland', 'NZ' => 'New Zealand',

        'ES' => 'Spain', 'IT' => 'Italy', 'PT' => 'Portugal', 'PL' => 'Poland',
        'CZ' => 'Czechia', 'HU' => 'Hungary', 'RO' => 'Romania', 'GR' => 'Greece',
        'TR' => 'Turkey', 'AE' => 'United Arab Emirates', 'SA' => 'Saudi Arabia', 'IL' => 'Israel',
        'SG' => 'Singapore', 'HK' => 'Hong Kong', 'JP' => 'Japan', 'KR' => 'South Korea',

        'IN' => 'India', 'PK' => 'Pakistan', 'BD' => 'Bangladesh', 'ID' => 'Indonesia',
        'PH' => 'Philippines', 'VN' => 'Vietnam', 'TH' => 'Thailand', 'MY' => 'Malaysia',
        'BR' => 'Brazil', 'MX' => 'Mexico', 'AR' => 'Argentina', 'CO' => 'Colombia',
        'CL' => 'Chile', 'PE' => 'Peru', 'NG' => 'Nigeria', 'EG' => 'Egypt',
        'ZA' => 'South Africa', 'KE' => 'Kenya', 'MA' => 'Morocco', 'DZ' => 'Algeria',
        'RU' => 'Russia', 'UA' => 'Ukraine', 'CN' => 'China', 'TW' => 'Taiwan',
        'IR' => 'Iran', 'IQ' => 'Iraq', 'LK' => 'Sri Lanka', 'NP' => 'Nepal',
    ];

    public static function name(string $code): string
    {
        return self::LIST[strtoupper($code)] ?? strtoupper($code);
    }

    /** ['Tier 1' => ['US'=>'United States', ...], 'Tier 2' => [...], 'Other' => [...]] */
    public static function groupedByTier(): array
    {
        $groups = ['Tier 1' => [], 'Tier 2' => [], 'Tier 3 / Other' => []];
        foreach (self::LIST as $code => $name) {
            $tier = CountryTiers::tierOf($code);
            $key = $tier === 1 ? 'Tier 1' : ($tier === 2 ? 'Tier 2' : 'Tier 3 / Other');
            $groups[$key][$code] = $name;
        }
        foreach ($groups as &$g) {
            asort($g);
        }
        return $groups;
    }
}
