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
 * - Host: this request's host when the siteaccess can be reached on it by URI
 *   (MatchOrder has uri) and this request is not on a host of another
 *   siteaccess; else a host that [SiteAccessSettings] HostMatchMapItems maps
 *   to the siteaccess; else its [SiteSettings] SiteURL; else this host.
 * - Port: the visitor's port, when the host has none of its own and is served
 *   by this installation (this host, or one of HostMatchMapItems) -- an
 *   installation reached both on 443 and on 8080 keeps the visitor on the one
 *   they are on.
 * - Path: the siteaccess name (or its URIMatchMapItems key) when it is matched
 *   by URI, unless RemoveSiteAccessIfDefaultAccess leaves the default out;
 *   the index file when URLs carry one.
 *
 * The rules are read from the settings without siteaccess overrides, as
 * eZSiteAccess::match() reads them, so the admin's own settings do not change
 * where the site is.
 */
class ezpSiteAccessURL
{
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

        // Hosts the host map gives this siteaccess, and every host it maps.
        $mappedHosts = array();
        $allMapped = array();
        foreach ( self::mapItems( $ini, 'HostMatchMapItems' ) as $item )
        {
            $allMapped[] = strtolower( $item[0] );
            if ( $item[1] === $siteaccess )
                $mappedHosts[] = $item[0];
        }

        $order = $ini->variable( 'SiteAccessSettings', 'MatchOrder' );
        $order = is_array( $order ) ? $order : explode( ';', (string)$order );
        $uriSegment = in_array( 'uri', $order, true ) ? self::uriSegment( $ini, $siteaccess ) : null;

        // On this host by URI, unless this host belongs to another siteaccess
        // (then a URI would be matched there, under that host's rules).
        $currentMapsElsewhere = in_array( strtolower( $currentHost ), $allMapped, true )
                                && !in_array( strtolower( $currentHost ), array_map( 'strtolower', $mappedHosts ), true );
        if ( $uriSegment !== null && !$currentMapsElsewhere )
        {
            $host = $currentHost;
            $port = null;
            $local = true;
        }
        else if ( $mappedHosts )
        {
            list( $host, $port ) = self::splitHost( $mappedHosts[0] );
            $local = true;
        }
        else
        {
            $siteURL = trim( (string)eZSiteAccess::getIni( $siteaccess, 'site.ini' )->variable( 'SiteSettings', 'SiteURL' ) );
            $siteURL = preg_replace( '#^[a-z]+://#i', '', $siteURL );
            list( $host, $port ) = self::splitHost( $siteURL !== '' ? strtok( $siteURL, '/' ) : $currentHost );
            $local = strcasecmp( $host, $currentHost ) === 0;
        }
        if ( $host === '' || in_array( strtolower( $host ), array( 'localhost', '127.0.0.1' ), true ) && $currentHost !== '' )
        {
            // A SiteURL left at localhost names no real address.
            $host = $currentHost;
            $local = true;
        }
        if ( $port === null && $local && $currentPort !== null )
            $port = $currentPort;

        $path = '';
        if ( $uriSegment !== null
             && !( $ini->variable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess' ) === 'enabled' && $siteaccess === $default ) )
            $path = $uriSegment . '/';

        $index = trim( (string)eZSys::indexFile( false ), '/' );
        $defaultPort = $scheme === 'https' ? '443' : '80';
        return $scheme . '://' . $host . ( $port !== null && $port !== $defaultPort ? ':' . $port : '' )
             . '/' . ( $index !== '' ? $index . '/' : '' ) . $path;
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
