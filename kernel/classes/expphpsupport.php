<?php
/**
 * File containing the expPhpSupport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The PHP support policy of Exponential, in one place for the installers, the setup wizard and the system report.
 *
 * Supported: PHP 8.0 to 8.5. Usable without support: PHP 7.4 (Exponential 6.0.15 and later). Older PHP is refused.
 *
 * "Usable without support" means: the software is designed to run on PHP 7.4, Composer installs the packages that
 * run there, but there is no bug-fix or security commitment specific to PHP 7.4, and the features that need PHP 8
 * (Velocity, Netgen Layouts and a few extensions) are not available. See
 * doc/eng-us/help/why-exponential-supports-php-7-4.md.
 */
class expPhpSupport
{
    /** The oldest supported PHP: the setup.ini [phpversion] SupportedVersion default */
    const SUPPORTED_MINIMUM = '8.0.0';

    /** The oldest PHP the software is designed to be usable on, without support: [phpversion] MinimumVersion */
    const USABLE_MINIMUM = '7.4.0';

    /** The standard sentence, the same everywhere it is shown */
    const STATEMENT = 'Supported: PHP 8.0 to 8.5. Usable without support: PHP 7.4 (Exponential 6.0.15 and later).';

    /**
     * 'supported', 'unsupported' (usable without support) or 'refused'.
     *
     * @param string|null $version PHP_VERSION when null
     * @param string|null $supported the oldest supported version, default SUPPORTED_MINIMUM
     * @param string|null $usable the oldest usable version, default USABLE_MINIMUM
     * @return string
     */
    public static function status( $version = null, $supported = null, $usable = null )
    {
        $version = $version === null ? PHP_VERSION : (string)$version;
        if ( version_compare( $version, $usable ?: self::USABLE_MINIMUM, '<' ) )
            return 'refused';
        if ( version_compare( $version, $supported ?: self::SUPPORTED_MINIMUM, '<' ) )
            return 'unsupported';
        return 'supported';
    }

    /**
     * The notice for the running PHP, or '' on a supported one.
     *
     * @param string|null $version PHP_VERSION when null
     * @return string
     */
    public static function notice( $version = null )
    {
        $version = $version === null ? PHP_VERSION : (string)$version;
        switch ( self::status( $version ) )
        {
            case 'refused':
                return 'PHP ' . $version . ' is too old for Exponential. ' . self::STATEMENT;
            case 'unsupported':
                return 'PHP ' . $version . ' is not supported: Exponential is usable on it without support (no bug-fix or security'
                    . ' commitment for this PHP version; use it at your own risk), and the features that need PHP 8 (Velocity,'
                    . ' Netgen Layouts and a few extensions) are not available. ' . self::STATEMENT;
        }
        return '';
    }
}
