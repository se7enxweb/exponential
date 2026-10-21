<?php
/**
 * File containing the ezpSiteAccessURL class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The address of a siteaccess's front page, worked out from the siteaccess
 * settings and the request the visitor is on: the admin's "open the site"
 * link, for instance.
 *
 * - Siteaccess: the one asked for, else site.ini [SiteSettings] DefaultAccess.
 * - Scheme: the one of this request, as eZSys::isSSLNow() has it (forwarded
 *   headers included).
 * - Address: the first of these that the matching rules (MatchOrder with
 *   host_uri, host and uri, as eZSiteAccess::match() applies them) lead to
 *   the siteaccess with no part of the path left over, which would otherwise
 *   be read as a module and answer 404:
 *   1. this host with a [SiteAccessSettings] HostUriMatchMapItems entry of
 *      the siteaccess for it, then this host with its URI element (or its
 *      URIMatchMapItems key; nothing for the default with
 *      RemoveSiteAccessIfDefaultAccess);
 *   2. its own [SiteSettings] SiteURL (host, port and path, the index file
 *      left out of the path), unless it is empty or localhost;
 *   3. a host that HostMatchMapItems maps to it, without a path;
 *   4. the host and URI of a HostUriMatchMapItems entry of it.
 *   When none of them can be shown to lead there: its URI on this host,
 *   else its SiteURL as written, else this host.
 * - Port: the visitor's port, when the address has none of its own and is
 *   served by this installation (this host, or a host of the host maps) --
 *   an installation reached both on 443 and on 8080 keeps the visitor on the
 *   one they are on.
 * - The index file when URLs carry one.
 *
 * The rules are read from the settings without siteaccess overrides, as
 * eZSiteAccess::match() reads them, so the admin's own settings do not change
 * where the site is. Which site an admin belongs to is the one thing it does
 * say: front() reads site.ini [SiteSettings] FrontSiteAccess of the current
 * siteaccess.
 */
class ezpSiteAccessURL
{
    /**
     * The front page of the site the current siteaccess belongs to: site.ini
     * [SiteSettings] FrontSiteAccess of the current siteaccess (an admin
     * siteaccess names its site there), else DefaultAccess. An installation
     * that serves several sites from one tree gives each admin its own site.
     * A FrontSiteAccess that is not in AvailableSiteAccessList falls back to
     * DefaultAccess with a warning in the debug output.
     *
     * @return string|false the URL (ending in /), or false when neither has one
     */
    public static function front()
    {
        $ini = eZINI::instance( 'site.ini' );
        $front = $ini->hasVariable( 'SiteSettings', 'FrontSiteAccess' )
               ? trim( (string)$ini->variable( 'SiteSettings', 'FrontSiteAccess' ) )
               : '';
        if ( $front !== '' )
        {
            $url = self::root( $front );
            if ( $url !== false )
                return $url;
            // once per value and request: the admin layouts ask several times per page. The memo belongs to one
            // request (eZExecution::requestKey()): a persistent Velocity worker serves many, and each warns again.
            static $warned = array(), $warnedRequest = null;
            $request = eZExecution::requestKey();
            if ( $warnedRequest !== $request )
            {
                $warned = array();
                $warnedRequest = $request;
            }
            if ( !isset( $warned[$front] ) )
            {
                $warned[$front] = true;
                eZDebug::writeWarning( "site.ini [SiteSettings] FrontSiteAccess=$front is not in AvailableSiteAccessList, the link goes to DefaultAccess", __METHOD__ );
            }
        }
        return self::root();
    }

    /**
     * The name of the siteaccess front() links to: FrontSiteAccess of the
     * current siteaccess when it is in AvailableSiteAccessList, else
     * DefaultAccess. Without the warning, which front() gives.
     *
     * @return string the siteaccess name; an empty string when DefaultAccess
     *                is not set either
     */
    public static function frontSiteAccess()
    {
        $ini = eZINI::instance( 'site.ini' );
        $front = $ini->hasVariable( 'SiteSettings', 'FrontSiteAccess' )
               ? trim( (string)$ini->variable( 'SiteSettings', 'FrontSiteAccess' ) )
               : '';
        if ( $front !== '' && self::root( $front ) !== false )
            return $front;
        $settings = new eZINI( 'site.ini', 'settings', null, null, true );
        return (string)$settings->variable( 'SiteSettings', 'DefaultAccess' );
    }

    /**
     * @param string|null $siteaccess null: [SiteSettings] DefaultAccess
     * @return string|false the URL (ending in /), or false for a siteaccess
     *                      that is not in AvailableSiteAccessList
     */
    public static function root( $siteaccess = null )
    {
        $ini = new eZINI( 'site.ini', 'settings', null, null, true );
        $default = (string)$ini->variable( 'SiteSettings', 'DefaultAccess' );
        $siteaccess = $siteaccess !== null && $siteaccess !== '' ? (string)$siteaccess : $default;
        $available = (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        if ( $siteaccess === '' || !in_array( $siteaccess, $available, true ) )
            return false;

        $scheme = eZSys::isSSLNow() ? 'https' : 'http';
        list( $currentHost, $currentPort ) = self::splitHost( (string)eZSys::hostname() );
        if ( $currentPort === null )
        {
            // eZSys::hostname() carries no port on every server (not under
            // Velocity); the port the request arrived on then says it. Read
            // directly rather than through eZSys::serverPort(), which keeps its
            // answer in a global that a persistent worker would carry on.
            $serverPort = (int)eZSys::serverVariable( 'SERVER_PORT', true );
            if ( $serverPort > 0 )
                $currentPort = (string)$serverPort;
        }

        $order = $ini->variable( 'SiteAccessSettings', 'MatchOrder' );
        $order = array_values( array_filter( array_map( 'trim', is_array( $order ) ? $order : explode( ';', (string)$order ) ) ) );
        $uriSegment = in_array( 'uri', $order, true ) ? self::uriSegment( $ini, $siteaccess ) : null;
        $hostUriItems = in_array( 'host_uri', $order, true ) ? self::hostUriItems( $ini ) : array();
        $mappedHosts = array();
        foreach ( self::mapItems( $ini, 'HostMatchMapItems' ) as $item )
        {
            if ( $item[1] === $siteaccess )
                $mappedHosts[] = $item[0];
        }
        $siteURL = trim( (string)eZSiteAccess::getIni( $siteaccess, 'site.ini' )->variable( 'SiteSettings', 'SiteURL' ) );
        $siteURL = trim( preg_replace( '#^[a-z][a-z0-9+.-]*://#i', '', $siteURL ), '/' );
        $siteURLHost = $siteURL !== '' ? strtok( $siteURL, '/' ) : '';
        $siteURLPath = $siteURL !== '' ? (string)substr( $siteURL, strlen( $siteURLHost ) ) : '';
        // a SiteURL that carries the index file (example.com/index.php/eng) says the path after it
        $siteURLPath = self::trimIndex( $siteURLPath );
        list( $siteURLHostName, $siteURLPort ) = self::splitHost( $siteURLHost );
        $siteURLReal = $siteURLHostName !== '' && !in_array( strtolower( $siteURLHostName ), array( 'localhost', '127.0.0.1' ), true );

        // The addresses the siteaccess may have, the most fitting first; the
        // first one that the matching rules lead to the siteaccess with no
        // part of the path left over is the one. Each is array( host, port
        // or null, path, served here: keeps the visitor's port ).
        $candidates = array();
        $uriCandidate = null;
        // 1. on this host: a host_uri entry of the siteaccess, then its URI
        foreach ( $hostUriItems as $item )
        {
            if ( $item[2] === $siteaccess && $item[0] !== '' && eZSiteAccess::hostMatches( $currentHost, $item[0], $item[3] ) )
                $candidates[] = array( $currentHost, null, $item[1], true );
        }
        if ( $uriSegment !== null )
        {
            $removed = $ini->variable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess' ) === 'enabled' && $siteaccess === $default;
            $uriCandidate = array( $currentHost, null, $removed ? '' : $uriSegment, true );
            $candidates[] = $uriCandidate;
        }
        // 2. its own [SiteSettings] SiteURL
        if ( $siteURLReal )
            $candidates[] = array( $siteURLHostName, $siteURLPort, $siteURLPath, strcasecmp( $siteURLHostName, $currentHost ) === 0 );
        // 3. a host the host map gives it
        foreach ( $mappedHosts as $mappedHost )
        {
            list( $host, $port ) = self::splitHost( $mappedHost );
            $candidates[] = array( strtolower( $host ), $port, '', true );
        }
        // 4. a host_uri entry of it on another host (a host written out, strict, start or end)
        foreach ( $hostUriItems as $item )
        {
            if ( $item[2] === $siteaccess && $item[0] !== '' && $item[3] !== 'part' )
            {
                list( $host, $port ) = self::splitHost( $item[0] );
                $candidates[] = array( strtolower( $host ), $port, $item[1], true );
            }
        }

        $chosen = null;
        foreach ( $candidates as $candidate )
        {
            $port = $candidate[1] !== null ? $candidate[1] : ( $candidate[3] ? $currentPort : null );
            // DefaultAccess for no match at all is only certain on this host: any host
            // nobody maps answers so, a placeholder SiteURL (example.com) too
            $fallback = strcasecmp( $candidate[0], $currentHost ) === 0 ? $default : null;
            if ( self::leadsTo( $ini, $order, $available, $fallback, $candidate[0] . ( $port !== null ? ':' . $port : '' ), $candidate[2] ) === $siteaccess )
            {
                $chosen = $candidate;
                break;
            }
        }
        if ( $chosen === null )
        {
            // Nothing the rules can be shown to lead there: as before, its URI
            // on this host, else its SiteURL as it is written, else this host.
            if ( $uriCandidate !== null )
                $chosen = $uriCandidate;
            else if ( $siteURLReal )
                $chosen = array( $siteURLHostName, $siteURLPort, $siteURLPath, strcasecmp( $siteURLHostName, $currentHost ) === 0 );
            else
                $chosen = array( $currentHost, null, '', true );
        }

        list( $host, $port, $path, $local ) = $chosen;
        if ( $host === '' )
        {
            $host = $currentHost;
            $local = true;
        }
        if ( $port === null && $local && $currentPort !== null )
            $port = $currentPort;
        $path = trim( (string)$path, '/' );

        $index = trim( (string)eZSys::indexFile( false ), '/' );
        $defaultPort = $scheme === 'https' ? '443' : '80';
        return $scheme . '://' . $host . ( $port !== null && $port !== $defaultPort ? ':' . $port : '' )
             . '/' . ( $index !== '' ? $index . '/' : '' ) . ( $path !== '' ? $path . '/' : '' );
    }

    /**
     * The siteaccess the matching rules give $host with the path $path, as
     * eZSiteAccess::match() would, when nothing of the path is left over:
     * host_uri (HostUriMatchMapItems), host (HostMatchType=map), uri
     * (URIMatchType=element on the first element, or map), in MatchOrder;
     * $default when none of them matches and the path is empty. null
     * when the address leads nowhere certain: the path would be read as a
     * module and view, or a probe this check does not follow (port,
     * servervar, index, element/text/regexp host or URI matching beyond the
     * first element) comes before the probe that would match.
     *
     * @param eZINI $ini site.ini without siteaccess overrides
     * @param array $order MatchOrder
     * @param array $available AvailableSiteAccessList
     * @param string|null $default DefaultAccess, or null when no match at all
     *                             leads nowhere certain
     * @param string $host
     * @param string $path without leading or trailing slash
     * @return string|null
     */
    private static function leadsTo( eZINI $ini, array $order, array $available, $default, $host, $path )
    {
        $path = trim( (string)$path, '/' );
        $elements = $path === '' ? array() : explode( '/', $path );
        foreach ( $order as $probe )
        {
            $name = null;
            $used = 0;
            switch ( $probe )
            {
                case 'host_uri':
                    foreach ( self::hostUriItems( $ini ) as $item )
                    {
                        if ( $item[1] !== '' && $path !== $item[1] && strpos( $path, $item[1] . '/' ) !== 0 )
                            continue;
                        if ( eZSiteAccess::hostMatches( $host, $item[0], $item[3] ) )
                        {
                            $name = $item[2];
                            $used = $item[1] === '' ? 0 : count( explode( '/', $item[1] ) );
                            break;
                        }
                    }
                    break;
                case 'host':
                    if ( (string)$ini->variable( 'SiteAccessSettings', 'HostMatchType' ) !== 'map' )
                        return null;
                    $method = $ini->hasVariable( 'SiteAccessSettings', 'HostMatchMethod' )
                            ? (string)$ini->variable( 'SiteAccessSettings', 'HostMatchMethod' ) : 'strict';
                    if ( $ini->hasVariable( 'SiteAccessSettings', 'HostMatchMapItems' ) )
                    {
                        foreach ( (array)$ini->variable( 'SiteAccessSettings', 'HostMatchMapItems' ) as $item )
                        {
                            $parts = explode( ';', (string)$item );
                            if ( count( $parts ) < 2 || $parts[0] === '' )
                                continue;
                            $itemMethod = isset( $parts[2] ) && trim( $parts[2] ) !== '' ? trim( $parts[2] ) : $method;
                            if ( eZSiteAccess::hostMatches( $host, $parts[0], $itemMethod ) )
                            {
                                $name = $parts[1];
                                break;
                            }
                        }
                    }
                    break;
                case 'uri':
                    $type = (string)$ini->variable( 'SiteAccessSettings', 'URIMatchType' );
                    if ( $type === 'element' )
                    {
                        $count = $ini->hasVariable( 'SiteAccessSettings', 'URIMatchElement' )
                               ? (int)$ini->variable( 'SiteAccessSettings', 'URIMatchElement' ) : 1;
                        if ( $count !== 1 )
                            return null;
                        if ( $elements && in_array( $elements[0], $available, true ) )
                        {
                            $name = $elements[0];
                            $used = 1;
                        }
                    }
                    else if ( $type === 'map' )
                    {
                        foreach ( self::mapItems( $ini, 'URIMatchMapItems' ) as $item )
                        {
                            if ( $elements && $item[0] === $elements[0] && in_array( $item[1], $available, true ) )
                            {
                                $name = $item[1];
                                $used = 1;
                                break;
                            }
                        }
                    }
                    else
                        return null;
                    break;
                case 'none':
                    break;
                default:
                    return null;
            }
            if ( $name !== null )
                return $used === count( $elements ) ? $name : null;
        }
        return $elements ? null : $default;
    }

    /**
     * site.ini [SiteAccessSettings] HostUriMatchMapItems as
     * array( host, uri without slashes, siteaccess, method ), the method
     * filled in from HostUriMatchMethodDefault (strict).
     */
    private static function hostUriItems( eZINI $ini )
    {
        if ( !$ini->hasVariable( 'SiteAccessSettings', 'HostUriMatchMapItems' ) )
            return array();
        $defaultMethod = $ini->hasVariable( 'SiteAccessSettings', 'HostUriMatchMethodDefault' )
                       ? trim( (string)$ini->variable( 'SiteAccessSettings', 'HostUriMatchMethodDefault' ) ) : 'strict';
        if ( $defaultMethod === '' )
            $defaultMethod = 'strict';
        $items = array();
        foreach ( (array)$ini->variable( 'SiteAccessSettings', 'HostUriMatchMapItems' ) as $item )
        {
            $parts = explode( ';', (string)$item );
            if ( count( $parts ) < 3 || trim( $parts[2] ) === '' )
                continue;
            $method = isset( $parts[3] ) && trim( $parts[3] ) !== '' && trim( $parts[3] ) !== 'default' ? trim( $parts[3] ) : $defaultMethod;
            $items[] = array( trim( $parts[0] ), trim( $parts[1], " /" ), trim( $parts[2] ), $method );
        }
        return $items;
    }

    /**
     * A path without a leading index file (index.php/eng: eng).
     */
    private static function trimIndex( $path )
    {
        $path = trim( (string)$path, '/' );
        if ( preg_match( '#^[^/]+\.php(/|$)#', $path ) )
            $path = (string)substr( $path, strpos( $path . '/', '/' ) + 1 );
        return trim( $path, '/' );
    }

    /**
     * The first URI element that matches $siteaccess: its name for
     * URIMatchType=element (one element), its URIMatchMapItems key for map;
     * null when it cannot be reached by URI.
     */
    private static function uriSegment( eZINI $ini, $siteaccess )
    {
        $type = (string)$ini->variable( 'SiteAccessSettings', 'URIMatchType' );
        if ( $type === 'element' )
        {
            $elements = $ini->hasVariable( 'SiteAccessSettings', 'URIMatchElement' )
                ? (int)$ini->variable( 'SiteAccessSettings', 'URIMatchElement' ) : 1;
            return $elements === 1 ? $siteaccess : null;
        }
        if ( $type === 'map' )
        {
            foreach ( self::mapItems( $ini, 'URIMatchMapItems' ) as $item )
            {
                if ( $item[1] === $siteaccess )
                    return $item[0];
            }
        }
        return null;
    }

    /**
     * "a;b" items of a site.ini [SiteAccessSettings] list, as array( a, b ).
     */
    private static function mapItems( eZINI $ini, $name )
    {
        if ( !$ini->hasVariable( 'SiteAccessSettings', $name ) )
            return array();
        $items = array();
        foreach ( (array)$ini->variable( 'SiteAccessSettings', $name ) as $item )
        {
            $parts = explode( ';', (string)$item );
            if ( count( $parts ) >= 2 && $parts[0] !== '' && $parts[1] !== '' )
                $items[] = array( $parts[0], $parts[1] );
        }
        return $items;
    }

    /**
     * "host:port" as array( host, port or null ); [v6]:port too.
     */
    private static function splitHost( $host )
    {
        $host = trim( (string)$host );
        if ( preg_match( '/^(\[[^\]]+\]|[^:]+):(\d+)$/', $host, $m ) )
            return array( $m[1], $m[2] );
        return array( $host, null );
    }
}
