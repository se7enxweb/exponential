<?php
/**
 * File containing the expInstallTimeZone class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The time zone a new installation writes to site.ini [TimeZoneSettings] TimeZone (eZStepCreateSites, the web
 * setup, the kickstarter and exp:install alike).
 *
 * Before 6.0.15 the setting was left empty, so a site ran in whatever zone the server's PHP had: in a container
 * image that is often UTC or America/Los_Angeles, and a German site's "today" then began at 09:00. The order:
 *
 *  1. the zone asked for: exp:install --timezone=<zone>, kickstart.ini [language_options] TimeZone;
 *  2. the zone of the primary language's country, when that country has one zone, or one that is clearly its main
 *     one (ger-DE: Europe/Berlin, eng-GB: Europe/London, fre-FR: Europe/Paris, por-BR: none, eng-US: none);
 *  3. PHP's date.timezone, when php.ini sets one;
 *  4. UTC.
 *
 * A country that spans several zones of real weight (the United States, Canada, Australia, Brazil, Russia, Mexico,
 * Indonesia, ...) is never guessed: its sites get date.timezone or UTC, and the installer says so.
 *
 * @since 6.0.15
 * @package kernel
 */
class expInstallTimeZone
{
    /**
     * Countries with more than one zone in the time zone database and one that is clearly theirs (the others are
     * small territories or share its offset).
     */
    const MAIN_ZONES = array(
        'AR' => 'America/Argentina/Buenos_Aires', 'CL' => 'America/Santiago', 'CN' => 'Asia/Shanghai',
        'CY' => 'Asia/Nicosia', 'DE' => 'Europe/Berlin', 'DK' => 'Europe/Copenhagen', 'EC' => 'America/Guayaquil',
        'ES' => 'Europe/Madrid', 'FR' => 'Europe/Paris', 'GB' => 'Europe/London', 'MY' => 'Asia/Kuala_Lumpur',
        'NL' => 'Europe/Amsterdam', 'NO' => 'Europe/Oslo', 'NZ' => 'Pacific/Auckland', 'PG' => 'Pacific/Port_Moresby',
        'PT' => 'Europe/Lisbon', 'UA' => 'Europe/Kyiv', 'UZ' => 'Asia/Tashkent',
    );

    /** Locales whose country part is not the ISO country code (ser-SR is Serbian, SR is Suriname). */
    const LOCALE_ZONES = array(
        'ser-SR' => 'Europe/Belgrade',
    );

    /** Older spellings, for a PHP whose time zone database is older than the name. */
    const ALIASES = array( 'Europe/Kyiv' => 'Europe/Kiev' );

    /**
     * Whether $zone is a time zone this PHP knows (an identifier such as Europe/Berlin, or UTC).
     *
     * @param string $zone
     * @return bool
     */
    public static function isValid( $zone )
    {
        $zone = trim( (string)$zone );
        if ( $zone === '' )
            return false;
        if ( strcasecmp( $zone, 'UTC' ) === 0 )
            return true;
        return in_array( $zone, DateTimeZone::listIdentifiers( DateTimeZone::ALL_WITH_BC ), true );
    }

    /**
     * The zone of a locale's country (ger-DE, eng-GB, nor-NO), or null when the country has several zones of real
     * weight or is not known.
     *
     * @param string $locale
     * @return string|null
     */
    public static function fromLocale( $locale )
    {
        $locale = trim( (string)$locale );
        if ( isset( self::LOCALE_ZONES[$locale] ) )
            return self::known( self::LOCALE_ZONES[$locale] );
        if ( !preg_match( '/^[a-z]{2,3}-([A-Za-z]{2})(@.*)?$/', $locale, $m ) )
            return null;
        $country = strtoupper( $m[1] );
        if ( isset( self::MAIN_ZONES[$country] ) )
            return self::known( self::MAIN_ZONES[$country] );
        $zones = @DateTimeZone::listIdentifiers( DateTimeZone::PER_COUNTRY, $country );
        if ( is_array( $zones ) && count( $zones ) === 1 )
            return $zones[0];
        return null;
    }

    /**
     * The zone a new installation gets, and where it came from.
     *
     * @param string $requested the zone asked for (exp:install --timezone, kickstart.ini), '' when none
     * @param string $locale the primary language
     * @param string $phpZone php.ini date.timezone ('' when not set)
     * @return array array( 'zone' => string, 'reason' => 'requested'|'language'|'php'|'default' )
     */
    public static function choose( $requested, $locale, $phpZone = '' )
    {
        $requested = trim( (string)$requested );
        if ( $requested !== '' && self::isValid( $requested ) )
            return array( 'zone' => $requested, 'reason' => 'requested' );
        $fromLocale = self::fromLocale( $locale );
        if ( $fromLocale !== null )
            return array( 'zone' => $fromLocale, 'reason' => 'language' );
        $phpZone = trim( (string)$phpZone );
        if ( $phpZone !== '' && self::isValid( $phpZone ) )
            return array( 'zone' => $phpZone, 'reason' => 'php' );
        return array( 'zone' => 'UTC', 'reason' => 'default' );
    }

    /**
     * $zone, or its older spelling when this PHP knows only that one.
     *
     * @param string $zone
     * @return string|null
     */
    protected static function known( $zone )
    {
        if ( self::isValid( $zone ) )
            return $zone;
        if ( isset( self::ALIASES[$zone] ) && self::isValid( self::ALIASES[$zone] ) )
            return self::ALIASES[$zone];
        return null;
    }
}
?>
