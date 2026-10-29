<?php
/**
 * File containing the expUserGeoIP class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The approximate place of an address, from a GeoIP database on this server only, for the signed-in sessions
 * ("Your devices") and the "new location" check. Nothing is ever asked of another server.
 *
 * Off unless [UserSessionSettings] GeoIPDatabase names a database file in the MaxMind DB format (.mmdb: GeoLite2 or
 * GeoIP2 City or Country, DB-IP Lite ...), relative to the installation or absolute, and a reader for that format is
 * installed: the PHP extension maxminddb or the Composer package maxmind-db/reader (both provide
 * MaxMind\Db\Reader). Without one, sessions have no place and every page works as before.
 *
 * Addresses of private and reserved networks are never looked up. The reader is kept per process and file (the file
 * is only read), so a persistent worker opens it once; a file replaced by a newer one is opened again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserGeoIP
{
    /** @var array|null array( path, mtime, reader ) */
    protected static $reader = null;

    /** @var callable|null a lookup that replaces the database (tests): function( $ip ): array|null */
    protected static $lookup = null;

    /**
     * Replaces the database by a function (tests); null restores it.
     *
     * @param callable|null $lookup function( string $ip ): array|null, the record as the database would give it
     */
    public static function setLookup( $lookup )
    {
        self::$lookup = is_callable( $lookup ) ? $lookup : null;
    }

    /** @return string the database file ('' when none is configured) */
    public static function databaseFile()
    {
        if ( !class_exists( 'eZINI' ) )
            return '';
        $ini = eZINI::instance();
        $file = $ini->hasVariable( 'UserSessionSettings', 'GeoIPDatabase' ) ? trim( (string)$ini->variable( 'UserSessionSettings', 'GeoIPDatabase' ) ) : '';
        if ( $file === '' )
            return '';
        if ( $file[0] !== '/' && class_exists( 'eZSys' ) )
            $file = eZSys::rootDir() . '/' . $file;
        return $file;
    }

    /** @return string the language of the names ([UserSessionSettings] GeoIPLanguage, en) */
    public static function language()
    {
        $lang = 'en';
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'UserSessionSettings', 'GeoIPLanguage' ) && trim( (string)$ini->variable( 'UserSessionSettings', 'GeoIPLanguage' ) ) !== '' )
                $lang = trim( (string)$ini->variable( 'UserSessionSettings', 'GeoIPLanguage' ) );
        }
        return $lang;
    }

    /**
     * Whether a place can be looked up at all: a database configured and readable and a reader installed.
     *
     * @return bool
     */
    public static function available()
    {
        if ( self::$lookup !== null )
            return true;
        $file = self::databaseFile();
        return $file !== '' && is_readable( $file ) && class_exists( 'MaxMind\Db\Reader' );
    }

    /**
     * Whether an address may be looked up: a public IPv4 or IPv6 address.
     *
     * @param string $ip
     * @return bool
     */
    public static function isPublic( $ip )
    {
        return filter_var( (string)$ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false;
    }

    /**
     * The place of an address.
     *
     * @param string $ip the full address (looked up before it is shortened for storage)
     * @return array|null country (ISO 3166 two letters, '' when unknown), location ("Berlin, Germany"); null when
     *                    nothing is known
     */
    public static function lookup( $ip )
    {
        $ip = trim( (string)$ip );
        if ( stripos( $ip, '::ffff:' ) === 0 && filter_var( substr( $ip, 7 ), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
            $ip = substr( $ip, 7 );
        if ( !self::isPublic( $ip ) )
            return null;
        try
        {
            if ( self::$lookup !== null )
                $record = call_user_func( self::$lookup, $ip );
            else
            {
                $reader = self::reader();
                $record = $reader ? $reader->get( $ip ) : null;
            }
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'GeoIP lookup failed: ' . $e->getMessage(), __METHOD__ );
            return null;
        }
        return is_array( $record ) ? self::fromRecord( $record, self::language() ) : null;
    }

    /**
     * The country and place of a record of the database (the GeoIP2/GeoLite2 City or Country layout).
     *
     * @param array $record
     * @param string $lang the language of the names, English when the record has none in it
     * @return array|null country, location; null when the record names no place
     */
    public static function fromRecord( array $record, $lang = 'en' )
    {
        $name = function ( $part ) use ( $lang ) {
            if ( !is_array( $part ) || !isset( $part['names'] ) || !is_array( $part['names'] ) )
                return '';
            foreach ( array( $lang, 'en' ) as $l )
                if ( isset( $part['names'][$l] ) && is_string( $part['names'][$l] ) && $part['names'][$l] !== '' )
                    return $part['names'][$l];
            return '';
        };
        $countryPart = isset( $record['country'] ) ? $record['country'] : ( isset( $record['registered_country'] ) ? $record['registered_country'] : null );
        $country = is_array( $countryPart ) && isset( $countryPart['iso_code'] ) && is_string( $countryPart['iso_code'] ) ? strtoupper( $countryPart['iso_code'] ) : '';
        if ( !preg_match( '/^[A-Z]{2}$/', $country ) )
            $country = '';
        $parts = array();
        $city = isset( $record['city'] ) ? $name( $record['city'] ) : '';
        if ( $city !== '' )
            $parts[] = $city;
        if ( isset( $record['subdivisions'][0] ) )
        {
            $region = $name( $record['subdivisions'][0] );
            if ( $region !== '' && $region !== $city )
                $parts[] = $region;
        }
        $countryName = $name( $countryPart );
        if ( $countryName !== '' )
            $parts[] = $countryName;
        elseif ( $country !== '' )
            $parts[] = $country;
        if ( $country === '' && !$parts )
            return null;
        return array( 'country' => $country, 'location' => mb_substr( implode( ', ', $parts ), 0, 128 ) );
    }

    /** @return object|null the reader of the database file, opened once per process and file version */
    protected static function reader()
    {
        $file = self::databaseFile();
        if ( $file === '' || !is_readable( $file ) || !class_exists( 'MaxMind\Db\Reader' ) )
            return null;
        $mtime = (int)@filemtime( $file );
        if ( self::$reader !== null && self::$reader[0] === $file && self::$reader[1] === $mtime )
            return self::$reader[2];
        $class = 'MaxMind\Db\Reader';
        $reader = new $class( $file );
        self::$reader = array( $file, $mtime, $reader );
        return $reader;
    }
}
?>
