<?php
/**
 * File containing the expUserAgent class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a person reads about a signed-in session (user/sessions): the browser, the operating system and the kind of
 * device a User-Agent header names, and an approximate address in place of the exact one.
 *
 *   expUserAgent::parse( $_SERVER['HTTP_USER_AGENT'] );
 *   // array( 'browser' => 'Firefox', 'browser_version' => '131', 'os' => 'Windows', 'os_version' => '10/11',
 *   //        'device' => 'desktop' )
 *   expUserAgent::approximateIP( '203.0.113.57' );       // '203.0.113.x'
 *   expUserAgent::approximateIP( '2001:db8:85a3::8a2e' ); // '2001:db8:85a3::/48'
 *
 * Only the header is read; nothing is looked up. A header that names nothing known is "Unknown" (device
 * "unknown"); programs (curl, Python, WebDAV and SOAP clients) are device "app"; crawlers are "bot". The address is
 * shown cut to its network (IPv4 /24, IPv6 /48): enough for a person to recognize "that was me at home", without
 * showing the exact address to someone looking over a shoulder. The stored address is the one the request came from.
 *
 * Without state, so the same answers in a persistent worker (Velocity). Tests: tests/tests/kernel/classes/user/expUserSessionTest.php
 * Guide: doc/eng-us/features/6.0/user-sessions.md
 *
 * @since 6.0.15
 */
class expUserAgent
{
    /** The device kinds parse() answers */
    const DEVICES = array( 'desktop', 'mobile', 'tablet', 'app', 'bot', 'unknown' );

    /** The longest header kept (the column is 512 characters) */
    const MAX_LENGTH = 512;

    /**
     * The browser, operating system and device kind a User-Agent header names.
     *
     * @param string|null $ua
     * @return array browser, browser_version, os, os_version, device
     */
    public static function parse( $ua )
    {
        $ua = is_string( $ua ) ? trim( $ua ) : '';
        $out = array( 'browser' => 'Unknown', 'browser_version' => '', 'os' => 'Unknown', 'os_version' => '', 'device' => 'unknown' );
        if ( $ua === '' )
            return $out;

        // crawlers and programs first: they often copy a browser's header around their own name
        if ( preg_match( '#(googlebot|bingbot|yandex(bot)?|baiduspider|duckduckbot|applebot|slurp|crawler|spider|\bbot\b)#i', $ua ) )
        {
            $out['device'] = 'bot';
            $out['browser'] = preg_match( '#(googlebot|bingbot|yandexbot|baiduspider|duckduckbot|applebot)#i', $ua, $m ) ? ucfirst( strtolower( $m[1] ) ) : 'Bot';
            return $out;
        }
        $apps = array(
            '#^curl/([\d.]+)#i' => 'curl', '#^Wget/([\d.]+)#i' => 'Wget', '#python-requests/([\d.]+)#i' => 'Python requests',
            '#^Python-urllib/([\d.]+)#i' => 'Python', '#^Java/([\d._]+)#i' => 'Java', '#^okhttp/([\d.]+)#i' => 'OkHttp',
            '#GuzzleHttp/([\d.]+)#i' => 'Guzzle', '#^PostmanRuntime/([\d.]+)#i' => 'Postman', '#^Microsoft-WebDAV-MiniRedir/([\d.]+)#i' => 'Windows WebDAV',
            '#^WebDAVFS/([\d.]+)#i' => 'macOS WebDAV', '#^davfs2/([\d.]+)#i' => 'davfs2', '#^gvfs/([\d.]+)#i' => 'GNOME files',
            '#^Cyberduck/([\d.]+)#i' => 'Cyberduck', '#^NuSOAP/([\d.]+)#i' => 'NuSOAP', '#^PHP-SOAP/([\d.]+)#i' => 'PHP SOAP',
            '#^Exponential#i' => 'Exponential',
        );
        foreach ( $apps as $pattern => $name )
        {
            if ( preg_match( $pattern, $ua, $m ) )
            {
                $out['browser'] = $name;
                $out['browser_version'] = isset( $m[1] ) ? self::major( $m[1] ) : '';
                $out['device'] = 'app';
                list( $out['os'], $out['os_version'] ) = self::os( $ua );
                return $out;
            }
        }

        list( $out['os'], $out['os_version'] ) = self::os( $ua );
        list( $out['browser'], $out['browser_version'] ) = self::browser( $ua );

        if ( preg_match( '#iPad|Tablet|PlayBook|Silk/|Kindle|Nexus (7|9|10)\b|SM-T\d#i', $ua )
             || ( stripos( $ua, 'Android' ) !== false && stripos( $ua, 'Mobile' ) === false ) )
            $out['device'] = 'tablet';
        elseif ( preg_match( '#Mobi|iPhone|iPod|Windows Phone|BlackBerry|Opera Mini|IEMobile#i', $ua ) )
            $out['device'] = 'mobile';
        elseif ( $out['browser'] !== 'Unknown' || $out['os'] !== 'Unknown' )
            $out['device'] = 'desktop';
        return $out;
    }

    /**
     * The operating system: name and version.
     *
     * @param string $ua
     * @return string[]
     */
    protected static function os( $ua )
    {
        if ( preg_match( '#Windows Phone(?: OS)? ([\d.]+)#i', $ua, $m ) )
            return array( 'Windows Phone', self::major( $m[1] ) );
        if ( preg_match( '#Windows NT ([\d.]+)#i', $ua, $m ) )
        {
            // Windows 11 sends NT 10.0 as well; only client hints tell them apart
            $names = array( '10.0' => '10/11', '6.3' => '8.1', '6.2' => '8', '6.1' => '7', '6.0' => 'Vista', '5.1' => 'XP' );
            return array( 'Windows', isset( $names[$m[1]] ) ? $names[$m[1]] : $m[1] );
        }
        if ( preg_match( '#(iPhone|iPad|iPod).*? OS ([\d_]+)#i', $ua, $m ) )
            return array( stripos( $m[1], 'iPad' ) !== false ? 'iPadOS' : 'iOS', self::major( str_replace( '_', '.', $m[2] ) ) );
        if ( preg_match( '#Android ([\d.]+)#i', $ua, $m ) )
            return array( 'Android', self::major( $m[1] ) );
        if ( stripos( $ua, 'Android' ) !== false )
            return array( 'Android', '' );
        if ( preg_match( '#CrOS#', $ua ) )
            return array( 'ChromeOS', '' );
        if ( preg_match( '#Mac OS X ([\d_.]+)#i', $ua, $m ) )
            return array( 'macOS', implode( '.', array_slice( explode( '.', str_replace( '_', '.', $m[1] ) ), 0, 2 ) ) );
        if ( preg_match( '#Macintosh|Darwin#i', $ua ) )
            return array( 'macOS', '' );
        if ( preg_match( '#Ubuntu#i', $ua ) )
            return array( 'Ubuntu', '' );
        if ( preg_match( '#Fedora#i', $ua ) )
            return array( 'Fedora', '' );
        if ( preg_match( '#Linux|X11#i', $ua ) )
            return array( 'Linux', '' );
        if ( preg_match( '#FreeBSD|OpenBSD|NetBSD#i', $ua, $m ) )
            return array( $m[0], '' );
        return array( 'Unknown', '' );
    }

    /**
     * The browser: name and major version. The order matters: Edge, Opera, Samsung and the others also say Chrome
     * and Safari, Chrome also says Safari.
     *
     * @param string $ua
     * @return string[]
     */
    protected static function browser( $ua )
    {
        $rules = array(
            '#Edg(?:e|A|iOS)?/([\d.]+)#' => 'Edge',
            '#(?:OPR|Opera)/([\d.]+)#' => 'Opera',
            '#SamsungBrowser/([\d.]+)#' => 'Samsung Internet',
            '#YaBrowser/([\d.]+)#' => 'Yandex Browser',
            '#Vivaldi/([\d.]+)#' => 'Vivaldi',
            '#Brave/([\d.]+)#' => 'Brave',
            '#(?:Firefox|FxiOS)/([\d.]+)#' => 'Firefox',
            '#(?:Chrome|CriOS|Chromium)/([\d.]+)#' => 'Chrome',
            '#Version/([\d.]+).*Safari/#' => 'Safari',
            '#(?:MSIE |Trident/.*rv:)([\d.]+)#' => 'Internet Explorer',
        );
        foreach ( $rules as $pattern => $name )
            if ( preg_match( $pattern, $ua, $m ) )
                return array( $name, self::major( $m[1] ) );
        if ( preg_match( '#AppleWebKit/.*Mobile/#', $ua ) )
            return array( 'Safari', '' );
        return array( 'Unknown', '' );
    }

    /**
     * The first part of a version ("131.0.6778" gives "131", "17.4.1" gives "17").
     *
     * @param string $version
     * @return string
     */
    protected static function major( $version )
    {
        $parts = explode( '.', (string)$version );
        return (string)$parts[0];
    }

    /**
     * One line for a person: "Firefox 131 on Windows 10/11", in the interface language ("Firefox 131 unter
     * Windows 10/11"). The names of the browser and the system are not translated.
     *
     * @param array $parsed what parse() answered
     * @return string
     */
    public static function label( array $parsed )
    {
        $browser = trim( $parsed['browser'] . ' ' . $parsed['browser_version'] );
        $os = trim( $parsed['os'] . ' ' . $parsed['os_version'] );
        if ( $parsed['os'] === 'Unknown' )
            return $browser;
        return ezpI18n::tr( 'kernel/user/sessions', '%browser on %os', 'a browser and the operating system it runs on, e.g. "Firefox 131 on Windows 10/11"',
            array( '%browser' => $browser, '%os' => $os ) );
    }

    /**
     * The network of an address, for showing: IPv4 to its /24 ("203.0.113.x"), IPv6 to its /48 ("2001:db8:85a3::/48").
     * An IPv4 address in IPv6 form is shown as IPv4. Anything that is not an address is shown as it is, cut short.
     *
     * @param string $ip
     * @return string
     */
    public static function approximateIP( $ip )
    {
        $ip = trim( (string)$ip );
        if ( $ip === '' )
            return '';
        if ( stripos( $ip, '::ffff:' ) === 0 && filter_var( substr( $ip, 7 ), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
            $ip = substr( $ip, 7 );
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
        {
            $parts = explode( '.', $ip );
            return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.x';
        }
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) )
        {
            $packed = inet_pton( $ip );
            if ( $packed === false )
                return '';
            $network = substr( $packed, 0, 6 ) . str_repeat( "\0", 10 );
            return inet_ntop( $network ) . '/48';
        }
        return mb_substr( $ip, 0, 64 );
    }

    /**
     * A header as it is stored: printable, at most MAX_LENGTH characters.
     *
     * @param string|null $ua
     * @return string
     */
    public static function clean( $ua )
    {
        $ua = preg_replace( '/[\x00-\x1F\x7F]+/', ' ', (string)$ua );
        if ( !mb_check_encoding( $ua, 'UTF-8' ) )
            $ua = mb_convert_encoding( $ua, 'UTF-8', 'ISO-8859-1' );
        return mb_substr( trim( $ua ), 0, self::MAX_LENGTH );
    }
}
?>
