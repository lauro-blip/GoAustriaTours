<?php

namespace App\Support;

class Countries
{
    /** ISO region => [dial code, label]. US first because the target market is US travellers. */
    public const LIST = [
        'US' => ['+1', 'United States'], 'CA' => ['+1', 'Canada'], 'GB' => ['+44', 'United Kingdom'],
        'IE' => ['+353', 'Ireland'], 'AU' => ['+61', 'Australia'], 'NZ' => ['+64', 'New Zealand'],
        'AT' => ['+43', 'Austria'], 'DE' => ['+49', 'Germany'], 'FR' => ['+33', 'France'],
        'IT' => ['+39', 'Italy'], 'ES' => ['+34', 'Spain'], 'NL' => ['+31', 'Netherlands'],
        'CH' => ['+41', 'Switzerland'], 'SE' => ['+46', 'Sweden'], 'NO' => ['+47', 'Norway'],
        'DK' => ['+45', 'Denmark'], 'BE' => ['+32', 'Belgium'], 'PT' => ['+351', 'Portugal'],
        'MX' => ['+52', 'Mexico'], 'BR' => ['+55', 'Brazil'], 'IN' => ['+91', 'India'],
        'JP' => ['+81', 'Japan'], 'SG' => ['+65', 'Singapore'], 'ZA' => ['+27', 'South Africa'],
        'AE' => ['+971', 'United Arab Emirates'], 'IL' => ['+972', 'Israel'], 'PH' => ['+63', 'Philippines'],
    ];

    public static function options(): array
    {
        return self::LIST;
    }
}
