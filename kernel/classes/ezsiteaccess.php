<?php
/**
 * File containing (site)access functionality
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Provides functions for siteaccess handling
 *
 * @package kernel
 */
class eZSiteAccess
{
    /**
     * Integer constants that identify the siteaccess matching used
     *
     * @since 4.4
     */
    const TYPE_DEFAULT = 1;
    const TYPE_URI = 2;
    const TYPE_PORT = 3;
    const TYPE_HTTP_HOST = 4;
    const TYPE_INDEX_FILE = 5;
    const TYPE_STATIC = 6;
    const TYPE_SERVER_VAR = 7;
    const TYPE_URL = 8;
    const TYPE_HTTP_HOST_URI = 9;
    const TYPE_CUSTOM = 10;

    const SUBTYPE_PRE = 1;
    const SUBTYPE_POST = 2;

    /**
     * How many languages of an Accept-Language header acceptedLanguages() reads.
     */
    const MAX_ACCEPTED_LANGUAGES = 32;

    static function siteAccessList()
    {
        $siteAccessList = array();
        $ini = eZINI::instance();
        $availableSiteAccessList = $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        if ( !is_array( $availableSiteAccessList ) )
            $availableSiteAccessList = array();

        $serverSiteAccess = eZSys::serverVariable( $ini->variable( 'SiteAccessSettings', 'ServerVariableName' ), true );
        if ( $serverSiteAccess )
            $availableSiteAccessList[] = $serverSiteAccess;

        $availableSiteAccessList = array_unique( $availableSiteAccessList );
        foreach ( $availableSiteAccessList as $siteAccessName )
        {
            $siteAccessItem = array();
            $siteAccessItem['name'] = $siteAccessName;
            $siteAccessItem['id'] = eZSys::ezcrc32( $siteAccessName );
            $siteAccessList[] = $siteAccessItem;
        }
        return $siteAccessList;
    }

    /**
     * Returns path to site access
     *
     * @param string $siteAccess
     * @return string|false Return path to siteacces or false if invalid
     */
    static function findPathToSiteAccess( $siteAccess )
    {
        $ini = eZINI::instance();
        $siteAccessList = $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        if ( !in_array( $siteAccess, $siteAccessList )  )
            return false;

        $currentPath = 'settings/siteaccess/' . $siteAccess;
        if ( file_exists( $currentPath ) )
            return $currentPath;

        $activeExtensions = eZExtension::activeExtensions();
        foreach ( $activeExtensions as $extension )
        {
            $extensionPath = eZExtension::extensionPath( $extension );
            if ( $extensionPath === false )
                continue;

            $currentPath = $extensionPath . '/settings/siteaccess/' . $siteAccess;
            if ( file_exists( $currentPath ) )
                return $currentPath;
        }

        return 'settings/siteaccess/' . $siteAccess;
    }

    /**
     * Goes trough the access matching rules and returns the access match.
     * The returned match is an associative array with:
     *  name     => string Name of the siteaccess (same as folder name)
     *  type     => int The constant that represent the matching used
     *  uri_part => array(string) List of path elements that was used in start of url for the match
     *
     * @since 4.4
     * @param eZURI $uri
     * @param string $host
     * @param string(numeric) $port
     * @param string $file Example '/index.php'
     * @param bool $languageDefault true only for the web page request (ezpKernelWeb): where no probe matched, the
     *                              siteaccess may then come from DefaultHostUriMatchMapItems by the browser's language,
     *                              with the keys redirect and vary. Every other caller (REST, the tree menu, scripts)
     *                              leaves it false and gets DefaultAccess, as before.
     * @return array
     */
    public static function match( eZURI $uri, $host, $port = 80, $file = '/index.php', $languageDefault = false )
    {
        eZDebugSetting::writeDebug( 'kernel-siteaccess', array( 'uri' => $uri,
                                                                'host' => $host,
                                                                'port' => $port,
                                                                'file' => $file ), __METHOD__ );
        $ini = eZINI::instance();
        if ( $ini->hasVariable( 'SiteAccessSettings', 'StaticMatch' ) )
        {
            $match = $ini->variable( 'SiteAccessSettings', 'StaticMatch' );
            if ( $match != '' )
            {
                $access = array( 'name' => $match,
                                 'type' => eZSiteAccess::TYPE_STATIC,
                                 'uri_part' => array() );
                return $access;
            }
        }

        list( $siteAccessList, $order ) =
            $ini->variableMulti( 'SiteAccessSettings', array( 'AvailableSiteAccessList', 'MatchOrder' ) );
        $access = array( 'name' => $ini->variable( 'SiteSettings', 'DefaultAccess' ),
                         'type' => eZSiteAccess::TYPE_DEFAULT,
                         'uri_part' => array() );

        if ( $order == 'none' )
            return $access;

        $order = $ini->variableArray( 'SiteAccessSettings', 'MatchOrder' );

        // Change the default type to eZSiteAccess::TYPE_URI if we're using URI MatchOrder.
        // This is to keep backward compatiblity with the ezurl operator. ezurl has since
        // rev 4949 added default siteaccess to generated URLs, even when there is
        // no siteaccess in the current URL.
        if ( in_array( 'uri', $order ) )
        {
            $access['type'] = eZSiteAccess::TYPE_URI;
        }

        foreach ( $order as $matchprobe )
        {
            $name = '';
            $type = '';
            $match_type = '';
            $uri_part = array();

            switch( $matchprobe )
            {
                case 'servervar':
                {
                    if ( $serversiteaccess = eZSys::serverVariable( $ini->variable( 'SiteAccessSettings', 'ServerVariableName' ), true ) )
                    {
                        $access['name'] = $serversiteaccess;
                        $access['type'] = eZSiteAccess::TYPE_SERVER_VAR;
                        return $access;
                    }
                    else
                        continue 2;
                } break;
                case 'port':
                {
                    if ( $ini->hasVariable( 'PortAccessSettings', $port ) )
                    {
                        $access['name'] = $ini->variable( 'PortAccessSettings', $port );
                        $access['type'] = eZSiteAccess::TYPE_PORT;
                        return $access;
                    }
                    else
                        continue 2;
                } break;
                case 'uri':
                {
                    $type = eZSiteAccess::TYPE_URI;
                    $match_type = $ini->variable( 'SiteAccessSettings', 'URIMatchType' );

                    if ( $match_type == 'map' )
                    {
                        if ( $ini->hasVariable( 'SiteAccessSettings', 'URIMatchMapItems' ) )
                        {
                            $match_item = $uri->element( 0 );
                            $matchMapItems = $ini->variableArray( 'SiteAccessSettings', 'URIMatchMapItems' );
                            foreach ( $matchMapItems as $matchMapItem )
                            {
                                $matchMapURI = $matchMapItem[0];
                                $matchMapAccess = $matchMapItem[1];
                                if ( $access['name']  == $matchMapAccess and in_array( $matchMapAccess, $siteAccessList ) )
                                {
                                    $uri_part = array( $matchMapURI );
                                }
                                if ( $matchMapURI == $match_item and in_array( $matchMapAccess, $siteAccessList ) )
                                {
                                    $uri->increase( 1 );
                                    $uri->dropBase();
                                    $access['name'] = $matchMapAccess;
                                    $access['type'] = $type;
                                    $access['uri_part'] = array( $matchMapURI );
                                    return $access;
                                }
                            }
                        }
                    }
                    else if ( $match_type == 'element' )
                    {
                        $match_index = $ini->variable( 'SiteAccessSettings', 'URIMatchElement' );
                        $elements = $uri->elements( false );
                        $elements = array_slice( $elements, 0, $match_index );
                        $name = implode( '_', $elements );
                        $uri_part = $elements;
                    }
                    else if ( $match_type == 'text' )
                    {
                        $match_item = $uri->elements();
                        $matcher_pre = $ini->variable( 'SiteAccessSettings', 'URIMatchSubtextPre' );
                        $matcher_post = $ini->variable( 'SiteAccessSettings', 'URIMatchSubtextPost' );
                    }
                    else if ( $match_type == 'regexp' )
                    {
                        $match_item = $uri->elements();
                        $matcher = $ini->variable( 'SiteAccessSettings', 'URIMatchRegexp' );
                        $match_num = $ini->variable( 'SiteAccessSettings', 'URIMatchRegexpItem' );
                    }
                    else
                        continue 2;
                } break;
                case 'host':
                {
                    $type = eZSiteAccess::TYPE_HTTP_HOST;
                    $match_type = $ini->variable( 'SiteAccessSettings', 'HostMatchType' );
                    $match_item = $host;
                    if ( $match_type == 'map' )
                    {
                        if ( $ini->hasVariable( 'SiteAccessSettings', 'HostMatchMapItems' ) )
                        {
                            $matchMapItems = $ini->variableArray( 'SiteAccessSettings', 'HostMatchMapItems' );
                            // strict (the host as it is listed) unless HostMatchMethod or the third field of an
                            // item says start, end or part, as for host_uri
                            $defaultHostMatchMethod = $ini->hasVariable( 'SiteAccessSettings', 'HostMatchMethod' )
                                ? $ini->variable( 'SiteAccessSettings', 'HostMatchMethod' ) : 'strict';
                            foreach ( $matchMapItems as $matchMapItem )
                            {
                                if ( !isset( $matchMapItem[1] ) || $matchMapItem[0] === '' )
                                    continue;
                                $matchMapHost = $matchMapItem[0];
                                $matchMapAccess = $matchMapItem[1];
                                $matchHostMethod = isset( $matchMapItem[2] ) && trim( $matchMapItem[2] ) !== '' ? trim( $matchMapItem[2] ) : $defaultHostMatchMethod;
                                if ( self::hostMatches( $host, $matchMapHost, $matchHostMethod ) )
                                {
                                    $access['name'] = $matchMapAccess;
                                    $access['type'] = $type;
                                    return $access;
                                }
                            }
                        }
                    }
                    else if ( $match_type == 'element' )
                    {
                        $match_index = $ini->variable( 'SiteAccessSettings', 'HostMatchElement' );
                        $match_arr = explode( '.', $match_item );
                        $name = $match_arr[$match_index] ?? '';
                    }
                    else if ( $match_type == 'text' )
                    {
                        $matcher_pre = $ini->variable( 'SiteAccessSettings', 'HostMatchSubtextPre' );
                        $matcher_post = $ini->variable( 'SiteAccessSettings', 'HostMatchSubtextPost' );
                    }
                    else if ( $match_type == 'regexp' )
                    {
                        $matcher = $ini->variable( 'SiteAccessSettings', 'HostMatchRegexp' );
                        $match_num = $ini->variable( 'SiteAccessSettings', 'HostMatchRegexpItem' );
                    }
                    else
                        continue 2;
                } break;
                case 'host_uri':
                {
                    $type = eZSiteAccess::TYPE_HTTP_HOST_URI;
                    if ( $ini->hasVariable( 'SiteAccessSettings', 'HostUriMatchMapItems' ) )
                    {
                        $uriString = $uri->elements();
                        $matchMapItems = $ini->variableArray( 'SiteAccessSettings', 'HostUriMatchMapItems' );
                        $defaultHostMatchMethod = $ini->variable( 'SiteAccessSettings', 'HostUriMatchMethodDefault' );

                        foreach ( $matchMapItems as $matchMapItem )
                        {
                            $matchHost       = $matchMapItem[0];
                            $matchURI        = $matchMapItem[1];
                            $matchAccess     = $matchMapItem[2];
                            $matchHostMethod = isset( $matchMapItem[3] ) ? $matchMapItem[3] : $defaultHostMatchMethod;

                            if ( $matchURI !== '' && !preg_match( "@^$matchURI\b@u", $uriString ) )
                                continue;

                            if ( self::hostMatches( $host, $matchHost, $matchHostMethod ) )
                            {
                                if ( $matchURI !== '' )
                                {
                                    $matchURIFolders = explode( '/', $matchURI );
                                    $uri->increase( count( $matchURIFolders ) );
                                    $uri->dropBase();
                                    $access['uri_part'] = $matchURIFolders;
                                }
                                $access['name'] = $matchAccess;
                                $access['type'] = $type;
                                return $access;
                            }
                        }
                    }
                } break;
                case 'index':
                {
                    $type = eZSiteAccess::TYPE_INDEX_FILE;
                    $match_type = $ini->variable( 'SiteAccessSettings', 'IndexMatchType' );
                    $match_item = $file;
                    if ( $match_type == 'element' )
                    {
                        $match_index = $ini->variable( 'SiteAccessSettings', 'IndexMatchElement' );
                        $match_pos = strpos( $match_item, '.php' );
                        if ( $match_pos !== false )
                        {
                            $match_item = substr( $match_item, 0, $match_pos );
                            $match_arr = explode( '_', $match_item );
                            $name = $match_arr[$match_index] ?? '';
                        }
                    }
                    else if ( $match_type == 'text' )
                    {
                        $matcher_pre = $ini->variable( 'SiteAccessSettings', 'IndexMatchSubtextPre' );
                        $matcher_post = $ini->variable( 'SiteAccessSettings', 'IndexMatchSubtextPost' );
                    }
                    else if ( $match_type == 'regexp' )
                    {
                        $matcher = $ini->variable( 'SiteAccessSettings', 'IndexMatchRegexp' );
                        $match_num = $ini->variable( 'SiteAccessSettings', 'IndexMatchRegexpItem' );
                    }
                    else
                        continue 2;
                } break;
                default:
                {
                    eZDebug::writeError( "Unknown access match: $matchprobe", "access" );
                } break;
            }

            if ( $match_type == 'regexp' )
                $name = self::matchRegexp( $match_item, $matcher, $match_num );
            else if ( $match_type == 'text' )
                $name = self::matchText( $match_item, $matcher_pre, $matcher_post );

            if ( isset( $name ) && $name != '' )
            {
                $nameClean = self::washName( $name );

                if ( in_array( $nameClean, $siteAccessList ) )
                {
                    if ( $nameClean !== $name )
                    {
                        if ( !$ini->hasVariable( 'SiteAccessSettings', 'NormalizeSANames' ) || $ini->variable( 'SiteAccessSettings', 'NormalizeSANames' ) == 'enabled' )
                        {
                            $name = $nameClean;
                            // Not while reachesSiteAccess() only asks where an address would lead
                            if ( !self::$matchingTarget && $ini->hasVariable( 'SiteAccessSettings', 'RedirectOnNormalize' ) && $ini->variable( 'SiteAccessSettings', 'RedirectOnNormalize' ) == 'enabled' )
                            {
                                header( $_SERVER['SERVER_PROTOCOL'] .  " 301 Moved Permanently" );
                                header( "Status: 301 Moved Permanently" );
                                $uriSlice = $uri->URIArray;
                                array_shift( $uriSlice );
                                $newUri = $name . '/' . implode( '/' , $uriSlice );
                                $location = eZSys::indexDir() . "/" . eZURI::encodeIRI( $newUri );
                                header( "Location: " . $location );
                                eZExecution::cleanExit();
                            }
                        }
                    }
                    if ( $type == eZSiteAccess::TYPE_URI )
                    {
                        if ( $match_type == 'element' )
                        {
                            $uri->increase( $match_index );
                            $uri->dropBase();
                        }
                        else if ( $match_type == 'regexp' )
                        {
                            $uri->setURIString( $match_item );
                        }
                        else if ( $match_type == 'text' )
                        {
                            $uri->setURIString( $match_item );
                        }
                    }
                    $access['type']     = $type;
                    $access['name']     = $name;
                    $access['uri_part'] = $uri_part;
                    return $access;
                }
            }
        }

        // No probe matched, so the default siteaccess applies. For the web page request alone (ezpKernelWeb asks
        // with $languageDefault), DefaultHostUriMatchMapItems can choose it by host and by the browser's language,
        // with the uri part its links carry (/ger); every other caller gets DefaultAccess, as before
        if ( self::$matchingTarget )
        {
            $access['unmatched'] = true;
        }
        else if ( $languageDefault === true && $ini->hasVariable( 'SiteAccessSettings', 'DefaultHostUriMatchMapItems' ) )
        {
            $default = self::matchDefaultHostUri( $ini->variableArray( 'SiteAccessSettings', 'DefaultHostUriMatchMapItems' ),
                                                  $host,
                                                  $ini->hasVariable( 'SiteAccessSettings', 'HostUriMatchMethodDefault' )
                                                      ? $ini->variable( 'SiteAccessSettings', 'HostUriMatchMethodDefault' ) : 'strict',
                                                  isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? (string)$_SERVER['HTTP_ACCEPT_LANGUAGE'] : '' );
            if ( $default !== null && isset( $default['name'] ) && !in_array( $default['name'], $siteAccessList ) )
            {
                // A siteaccess that does not exist here would be loaded without settings: keep the default
                eZDebug::writeError( "DefaultHostUriMatchMapItems names the siteaccess '{$default['name']}', which is not in AvailableSiteAccessList", 'access' );
                $default = isset( $default['vary'] ) ? array( 'vary' => $default['vary'] ) : null;
            }
            if ( $default !== null )
            {
                $access = array_merge( $access, $default );
                // The web kernel sends the browser on to the address with the segment, once (/ to /ger), so the
                // pages a cache keeps have one address each: only when that address reaches the siteaccess through
                // a probe of MatchOrder, else the redirect would come back here
                if ( !empty( $default['uri_part'] )
                     && ( !$ini->hasVariable( 'SiteAccessSettings', 'DefaultHostUriRedirect' )
                          || $ini->variable( 'SiteAccessSettings', 'DefaultHostUriRedirect' ) !== 'disabled' ) )
                {
                    $access['redirect'] = self::reachesSiteAccess( $default['name'], implode( '/', $default['uri_part'] ) . '/' . $uri->elements(),
                                                                   $host, $port, $file );
                }
            }
        }
        return $access;
    }

    /**
     * Set while reachesSiteAccess() matches an address: no default and no redirect then, only whether a probe matched.
     *
     * @var bool
     */
    private static $matchingTarget = false;

    /**
     * Whether the address $uriString (with its segment, for example "ger/news/an-article") reaches the siteaccess
     * $name through a probe of MatchOrder, so that a redirect there is not answered with another one.
     *
     * @param string $name
     * @param string $uriString
     * @param string $host
     * @param int $port
     * @param string $file
     * @return bool
     */
    static function reachesSiteAccess( $name, $uriString, $host, $port = 80, $file = '/index.php' )
    {
        self::$matchingTarget = true;
        try
        {
            $access = self::match( new eZURI( trim( $uriString, '/' ) ), $host, $port, $file );
        }
        finally
        {
            self::$matchingTarget = false;
        }
        return empty( $access['unmatched'] ) && $access['name'] === $name;
    }

    /**
     * The default siteaccess for an address no probe of MatchOrder matched: the first entry of
     * $items (DefaultHostUriMatchMapItems[]=host;uri;siteaccess[;method[;language]]) whose host matches and whose
     * language the browser accepts, trying the languages of $acceptLanguage from the most wanted; else the first
     * entry for the host without a language. method is strict, start, end or part; empty or "default" is
     * $defaultMethod (HostUriMatchMethodDefault). A language matches itself and its regional forms (de: de, de-CH).
     *
     * @param array $items the entries, each split at ";"
     * @param string $host
     * @param string $defaultMethod
     * @param string $acceptLanguage the Accept-Language header of the request
     * @return array|null array( name, uri_part[, vary] ) with vary = Accept-Language when the answer for the host
     *                    depends on the browser: an entry names a language, and the entries for the host lead to more
     *                    than one siteaccess or segment, or none of them applies without a language. When the answer
     *                    depends on the browser but this browser got no entry, only array( vary ) (the default
     *                    siteaccess answers, but not for every browser). null when no entry is for the host.
     */
    static function matchDefaultHostUri( array $items, $host, $defaultMethod, $acceptLanguage )
    {
        $forHost = array();
        $outcomes = array();
        $hasLanguage = false;
        $hasFallback = false;
        foreach ( $items as $item )
        {
            $item = (array)$item;
            // An empty host matches any host with part or start, as in HostUriMatchMapItems
            if ( !isset( $item[2] ) || trim( (string)$item[2] ) === '' )
                continue;
            $method = isset( $item[3] ) && trim( (string)$item[3] ) !== '' && trim( (string)$item[3] ) !== 'default'
                ? trim( (string)$item[3] ) : (string)$defaultMethod;
            if ( !self::hostMatches( $host, (string)$item[0], $method ) )
                continue;
            // de_DE as a locale is written, de-DE as a browser sends it
            $language = isset( $item[4] ) ? strtolower( str_replace( '_', '-', trim( (string)$item[4] ) ) ) : '';
            $uri = trim( trim( (string)$item[1] ), '/' );
            $name = trim( (string)$item[2] );
            $outcomes[$name . "\0" . $uri] = true;
            if ( $language === '' )
                $hasFallback = true;
            else
                $hasLanguage = true;
            $forHost[] = array( 'uri' => $uri, 'name' => $name, 'language' => $language );
        }
        if ( !$forHost )
            return null;

        // The browser makes a difference only where a language entry can lead elsewhere than the others: to another
        // siteaccess or segment, or, without an entry for every browser, to the default siteaccess
        $vary = $hasLanguage && ( count( $outcomes ) > 1 || !$hasFallback );

        $chosen = null;
        foreach ( self::acceptedLanguages( $acceptLanguage ) as $wanted )
        {
            foreach ( $forHost as $entry )
            {
                if ( $entry['language'] !== '' && ( $wanted === $entry['language'] || strpos( $wanted, $entry['language'] . '-' ) === 0 ) )
                {
                    $chosen = $entry;
                    break 2;
                }
            }
        }
        if ( $chosen === null )
        {
            foreach ( $forHost as $entry )
            {
                if ( $entry['language'] === '' )
                {
                    $chosen = $entry;
                    break;
                }
            }
        }
        if ( $chosen === null )
            return $vary ? array( 'vary' => 'Accept-Language' ) : null;

        $access = array( 'name' => $chosen['name'],
                         'uri_part' => $chosen['uri'] !== '' ? array_values( array_filter( explode( '/', $chosen['uri'] ), 'strlen' ) ) : array() );
        if ( $vary )
            $access['vary'] = 'Accept-Language';
        return $access;
    }

    /**
     * The languages of an Accept-Language header, lower case, the most wanted first (by q, then in the order given);
     * a language with q=0 and the wildcard are left out.
     *
     * @param string $header for example "de-DE,de;q=0.9,en;q=0.8"
     * @return string[]
     */
    static function acceptedLanguages( $header )
    {
        $languages = array();
        // A browser names a handful; the rest of an overlong header is not read
        foreach ( array_slice( explode( ',', (string)$header, self::MAX_ACCEPTED_LANGUAGES + 1 ), 0, self::MAX_ACCEPTED_LANGUAGES ) as $position => $part )
        {
            $fields = explode( ';', $part );
            $tag = strtolower( trim( $fields[0] ) );
            if ( $tag === '' || $tag === '*' || strlen( $tag ) > 35 || !preg_match( '/^[a-z0-9]+(-[a-z0-9]+)*$/', $tag ) )
                continue;
            $quality = 1.0;
            foreach ( array_slice( $fields, 1 ) as $parameter )
            {
                if ( preg_match( '/^\s*q\s*=\s*([0-9]*\.?[0-9]+)\s*$/i', $parameter, $match ) )
                    $quality = min( 1.0, (float)$match[1] );
            }
            if ( $quality <= 0 )
                continue;
            $languages[] = array( $quality, $position, $tag );
        }
        usort( $languages, function ( $a, $b ) { return $b[0] <=> $a[0] ?: $a[1] <=> $b[1]; } );
        return array_column( $languages, 2 );
    }

    /**
     * Whether $host matches the host $matchHost of a map item (HostMatchMapItems, HostUriMatchMapItems) by $method:
     * strict (the same host), start (begins with it, so www.example.com.test.local matches www.example.com), end
     * (ends with it) or part (contains it), as host_uri always matched. An unknown method matches nothing and is
     * logged.
     *
     * Host names do not depend on case, so both are compared in lower case, without the dot a fully qualified name
     * may end with, and a host written in Unicode (münchen.example) is compared in its ASCII form
     * (xn--mnchen-3ya.example), which is what a browser sends. A port on $host (example.com:8080) is ignored unless
     * $matchHost names one itself. An empty $matchHost matches every host with start and part, and none with strict
     * and end.
     *
     * @param string $host
     * @param string $matchHost
     * @param string $method strict, start, end or part
     * @return bool
     */
    static function hostMatches( $host, $matchHost, $method )
    {
        $host = self::normalizeHost( $host );
        $matchHost = self::normalizeHost( $matchHost );
        if ( !preg_match( '/^(\[[^\]]*\]|[^:]*):\d+$/', $matchHost ) && preg_match( '/^(\[[^\]]*\]|[^:]*):\d+$/', $host, $withoutPort ) )
            $host = $withoutPort[1];
        switch ( $method )
        {
            case 'strict':
                return $matchHost === $host;
            case 'start':
                return strpos( $host, $matchHost ) === 0;
            case 'end':
                return $matchHost !== '' && str_ends_with( $host, $matchHost );
            case 'part':
                return strpos( $host, $matchHost ) !== false;
        }
        eZDebug::writeError( "Unknown host match: $method", 'access' );
        return false;
    }

    /**
     * A host name as hostMatches() compares it: trimmed, lower case, without a trailing dot, a Unicode name in its
     * ASCII (punycode) form when the intl extension is there.
     *
     * @param string $host
     * @return string
     */
    private static function normalizeHost( $host )
    {
        $host = strtolower( rtrim( trim( (string)$host ), '.' ) );
        if ( $host !== '' && preg_match( '/[\x80-\xff]/', $host ) && function_exists( 'idn_to_ascii' ) )
        {
            $port = '';
            if ( preg_match( '/^(.*)(:\d+)$/', $host, $parts ) )
                list( , $host, $port ) = $parts;
            $ascii = idn_to_ascii( $host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46 );
            if ( is_string( $ascii ) && $ascii !== '' )
                $host = strtolower( $ascii );
            $host .= $port;
        }
        return $host;
    }

    /**
     * Match a regex expression
     *
     * @since 4.4
     * @param string $text
     * @param string $reg
     * @param int $num
     * @return string|null
     */
    static function matchRegexp( &$text, $reg, $num )
    {
        $reg = str_replace( '/', "\\/", $reg );
        if ( preg_match( "/$reg/", $text, $regs, PREG_OFFSET_CAPTURE ) && $num < count( $regs ) )
        {
            // remove the matched text where it was matched, not every other place the same text occurs
            list( $matched, $offset ) = $regs[$num];
            if ( $offset >= 0 )
                $text = substr_replace( $text, '', $offset, strlen( $matched ) );
            return $matched;
        }
        return null;
    }

    /**
     * Match a text string with pre or/or post text strings
     *
     * @since 4.4
     * @param string $text
     * @param string $match_pre
     * @param string $match_post
     * @return string|null
     */
    static function matchText( &$text, $match_pre, $match_post )
    {
        // Without either marker there is nothing to match
        if ( $match_pre === '' && $match_post === '' )
            return null;

        // The name is what lies between the two markers; the text keeps what is before the first marker and after
        // the second one. Without a match the text is left as it was.
        $ret = $text;
        $rest = '';
        if ( $match_pre !== '' )
        {
            $pos = strpos( $text, $match_pre );
            if ( $pos === false )
                return null;

            $ret = substr( $text, $pos + strlen( $match_pre ) );
            $rest = substr( $text, 0, $pos );
        }
        if ( $match_post !== '' )
        {
            $pos = strpos( $ret, $match_post );
            if ( $pos === false )
                return null;

            $rest .= substr( $ret, $pos + strlen( $match_post ) );
            $ret = substr( $ret, 0, $pos );
        }
        $text = $rest;
        return $ret;
    }

    /**
     * Re-initialises the current site access
     * If a siteaccess is set, then executes {@link eZSiteAccess::load()}
     *
     * @return bool True if re-initialisation was successful
     */
    static function reInitialise()
    {
        if ( isset( $GLOBALS['eZCurrentAccess'] ) )
        {
            self::load( $GLOBALS['eZCurrentAccess'] );
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Changes the site access to what's defined in $access. It will change the
     * access path in eZSys and prepend an override dir to eZINI
     * Note: does not load extensions, use {@link eZSiteAccess::load()} if you want that
     *
     * @since 4.4
     * @param array $access An associative array with 'name' (string), 'type' (int) and 'uri_part' (array).
     *                      See {@link eZSiteAccess::match()} for array structure definition
     * @param eZINI|null $siteINI Optional parameter to be able to only do change on specific instance of site.ini
     *                   hence skip changing eZSys access paths (but not siteaccess, see {@link eZSiteAccess::load()})
     * @return array The $access parameter
     */
    static function change( array $access, ?eZINI $siteINI = null )
    {
        $name = $access['name'];
        $GLOBALS['eZCurrentAccess'] =& $access;
        if ( $siteINI !== null )
        {
            $ini = $siteINI;
        }
        else
        {
            $ini = eZINI::instance();
        }

        $ini->prependOverrideDir( "siteaccess/$name", false, 'siteaccess', 'siteaccess' );

        /* Make sure extension siteaccesses are prepended */
        eZExtension::prependExtensionSiteAccesses( $name, $ini );

        if ( $siteINI === null )
        {
            // site.ini of this siteaccess is read from the INI cache directory every site shares, never from that of
            // the site before (a persistent worker or a script that changes siteaccess): the site before would hold a
            // copy of it that clearing the INI cache of this site does not refresh
            self::restoreINICacheDirectory();
        }
        $ini->loadCache();

        // change some global settings if $siteINI is null
        if ( $siteINI === null )
        {
            eZSys::clearAccessPath();
            if ( empty( $access['uri_part'] ) || $access['uri_part'] === null )
            {
                if ( $ini->hasVariable('SiteSettings', 'SiteUriParts') )
                    $access['uri_part'] = $ini->variable('SiteSettings', 'SiteUriParts');
                else if ( isset( $access['type'] ) && $access['type'] === eZSiteAccess::TYPE_URI )
                    $access['uri_part'] = array( $access['name'] );
                else
                    $access['uri_part'] = array();
            }

            eZSys::setAccessPath( $access['uri_part'], $name );

            eZContentLanguage::expireCache( false );

            // A siteaccess with a VarDir of its own reads its own expiry.php: clearing a cache of one site
            // must not expire the caches of every site (multi-site hosting)
            eZExpiryHandler::resetForCurrentCacheDirectory();

            // Its INI files are cached in its own cache directory when site.ini says so, and its logs go to its
            // own log directory. Both are derived again on every change, so a process that serves several sites
            // one after another (a persistent worker, a script that changes siteaccess) never keeps those of the
            // previous one.
            if ( self::updateINICacheDirectory( $ini ) !== false )
            {
                // site.ini itself was read above from the shared directory. Read it again from the directory of this
                // site, so that it is cached there too and clearing the INI cache of this site refreshes it.
                $ini->loadCache();
            }
            self::updateLogDirectory( $ini );

            eZUpdateDebugSettings();
            eZDebugSetting::writeDebug( 'kernel-siteaccess', "Updated settings to use siteaccess '$name'", __METHOD__ );
        }

        return $access;
    }

    /**
     * Sets where the INI files read from now on are cached: in the cache directory of the site
     * (<CacheDir>/ini/) when site.ini [FileSettings] INICacheDir is "site", in var/cache/ini/ (or the directory the
     * installation set) otherwise.
     *
     * With several sites on one installation (multi-site hosting), each with a VarDir of its own, clearing the INI
     * cache of one site no longer removes that of all the others. The INI files read before the siteaccess is known
     * stay in var/cache/ini/. A directory set by the installation or a multi-site wrapper before is kept for a site
     * without the setting, and comes back after a site with it.
     *
     * @param eZINI $ini site.ini of the siteaccess
     * @return string|false The directory set, false for var/cache/ini/
     */
    static function updateINICacheDirectory( eZINI $ini )
    {
        if ( $ini->hasVariable( 'FileSettings', 'INICacheDir' ) && $ini->variable( 'FileSettings', 'INICacheDir' ) === 'site' )
        {
            $cacheDirectory = eZSys::cacheDirectory();
            if ( $cacheDirectory === '' || $cacheDirectory[0] !== '/' )
            {
                // The installation root, as eZINI::loadCache() puts it before var/cache/ini/
                $cacheDirectory = ( defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : dirname( __DIR__, 2 ) ) . '/' . $cacheDirectory;
            }
            if ( !isset( $GLOBALS['eZSiteAccessINICacheDir'] ) )
            {
                // What was there before, set by the installation or a multi-site wrapper, comes back for a site
                // without the setting
                $GLOBALS['eZSiteAccessINICacheDirBefore'] = isset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ) ? $GLOBALS['eZINI_CONFIG_CACHE_DIR'] : null;
            }
            $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = rtrim( $cacheDirectory, '/' ) . '/ini/';
            $GLOBALS['eZSiteAccessINICacheDir'] = $GLOBALS['eZINI_CONFIG_CACHE_DIR'];
            return $GLOBALS['eZINI_CONFIG_CACHE_DIR'];
        }

        self::restoreINICacheDirectory();
        return false;
    }

    /**
     * Undoes what updateINICacheDirectory() set for a site with INICacheDir=site: the directory there was before
     * comes back, unless something else has set another one since. Does nothing when no site directory is set.
     *
     * @return void
     */
    protected static function restoreINICacheDirectory()
    {
        if ( isset( $GLOBALS['eZSiteAccessINICacheDir'] ) )
        {
            if ( isset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ) && $GLOBALS['eZINI_CONFIG_CACHE_DIR'] === $GLOBALS['eZSiteAccessINICacheDir'] )
            {
                if ( isset( $GLOBALS['eZSiteAccessINICacheDirBefore'] ) )
                {
                    $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $GLOBALS['eZSiteAccessINICacheDirBefore'];
                }
                else
                {
                    unset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
                }
            }
            unset( $GLOBALS['eZSiteAccessINICacheDir'], $GLOBALS['eZSiteAccessINICacheDirBefore'] );
        }
    }

    /**
     * Points the log files of eZDebug and the default logs of eZLog at the log directory of the site
     * (eZSys::logDirectory()) when site.ini [FileSettings] UseGlobalLogDir is disabled, and back at var/log otherwise.
     *
     * change() calls it for every front controller and every script, whether or not that has loaded the function
     * file of eZUpdateDebugLogDirectory() (soap.php and webdav.php declare their own eZUpdateDebugSettings()).
     *
     * @param eZINI $ini site.ini of the siteaccess
     * @return string|false The log directory used, false for var/log
     */
    static function updateLogDirectory( eZINI $ini )
    {
        $logDir = false;
        if ( $ini->hasVariable( 'FileSettings', 'UseGlobalLogDir' ) &&
             $ini->variable( 'FileSettings', 'UseGlobalLogDir' ) === 'disabled' )
        {
            $logDir = eZSys::logDirectory();
        }
        eZDebug::setLogDirectory( $logDir );
        return $logDir;
    }

    /**
     * Washes site access name
     *
     * Allowed characters are [a-z], [A-Z] and [0-9], and the "_" (underscore). The washing rules are:
     * - Characters not in the previous list (alphanumerical and underscore) are replaced by an "_" (underscore);
     * - Multiple consecutive "_" (underscores) are replaced by a single underscore;
     * - Leading and trailing "_" are removed.
     *
     * @since 5.3
     * @param string $name The site access name, as received from the browser
     * @return string The washed name
     */
    private static function washName( $name )
    {
        return preg_replace(
            array( '/[^a-zA-Z0-9]+/', '/_+/', '/^_/', '/_$/' ),
            array( '_', '_', '', '' ),
            $name
        );
    }

    /**
     * Reloads extensions and changes siteaccess globally
     * If you only want changes on a instance of ini, use {@link eZSiteAccess::getIni()}
     *
     * - clears all in-memory caches used by the INI system
     * - re-builds the list of paths where INI files are searched for
     * - runs {@link eZSiteAccess::change()}
     * - re-searches module paths {@link eZModule::setGlobalPathList()}
     *
     * @since 4.4
     * @param array $access An associative array with 'name' (string), 'type' (int) and 'uri_part' (array).
     *                      See {@link eZSiteAccess::match()} for array structure definition
     * @param eZINI|null $siteINI Optional parameter to be able to only do change on specific instance of site.ini
     *                            If set, then global siteacceess will not be changed as well.
     * @return array The $access parameter
     */
    static function load( array $access, ?eZINI $siteINI = null )
    {
        if ( isset( $GLOBALS['eZCurrentAccess'] ) )
        {
            $currentSiteAccess = $GLOBALS['eZCurrentAccess'];
            unset( $GLOBALS['eZCurrentAccess'] );
        }
        else
        {
            $currentSiteAccess = null;
        }

        // Clear all ini override dirs
        if ( $siteINI instanceof eZINI )
        {
            $siteINI->resetOverrideDirs();
        }
        else
        {
            eZINI::resetAllInstances();
            eZExtension::clearActiveExtensionsMemoryCache();
            eZTemplateDesignResource::clearInMemoryCache();
        }

        // Reload extensions, siteaccess and access extensions
        eZExtension::activateExtensions( 'default', $siteINI );
        $access = self::change( $access, $siteINI );
        eZExtension::activateExtensions( 'access', $siteINI );

        // Restore current (old) siteacces if changes where only to be applied to locale instance of site.ini
        if ( $siteINI instanceof eZINI )
        {
            $GLOBALS['eZCurrentAccess'] = $currentSiteAccess;
        }
        else
        {
            $moduleRepositories = eZModule::activeModuleRepositories();
            eZModule::setGlobalPathList( $moduleRepositories );
        }

        return $access;
    }

    /**
     * Loads ini environment for a specific siteaccess
     *
     * eg: $ini = eZSiteAccess::getIni( 'eng', 'site.ini' );
     *
     * @since 4.4
     * @param string $siteAccess
     * @param string $settingFile
     * @return eZINI
     */
    static function getIni( $siteAccess, $settingFile = 'site.ini' )
    {
        // return global if siteaccess is same as requested or false
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] )
          && $GLOBALS['eZCurrentAccess']['name'] === $siteAccess )
        {
            return eZINI::instance( $settingFile );
        }
        else if ( !$siteAccess )
        {
            return eZINI::instance( $settingFile );
        }

        // create a site ini instance using $useLocalOverrides = true
        $siteIni = new eZINI( 'site.ini', 'settings', null, null, true );

        // create a dummy access definition (not used as long as $siteIni is sent to self::load() )
        $access = array( 'name' => $siteAccess,
                         'type' => eZSiteAccess::TYPE_STATIC,
                         'uri_part' => array() );

        // Load siteaccess but on our locale instance of site.ini only
        $access = self::load( $access, $siteIni );

        // if site.ini, return with no further work needed
        if ( $settingFile === 'site.ini' )
        {
            return $siteIni;
        }

        // load settings file with $useLocalOverrides = true
        $ini = new eZINI( $settingFile,'settings', null, null, true );
        $ini->setOverrideDirs( $siteIni->overrideDirs( false ) );
        $ini->load();

        return $ini;
    }

    /**
     * Get current siteaccess data if set, see {@link eZSiteAccess::match()} for array structure
     *
     * @since 4.4
     * return array|null
     */
    static function current()
    {
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            return $GLOBALS['eZCurrentAccess'];
        return null;
    }

    /**
     * Gets siteaccess name by language based on site.ini\[RegionalSettings]\LanguageSA[]
     * if defined otherwise by convention ( eng-GB -> eng ), in both cases sa needs to
     * be in site.ini\[SiteAccessSettings]\RelatedSiteAccessList[] as well to be valid.
     *
     * @since 4.5
     * @param string $language eg: eng-GB
     * @return string|null
     */
    public static function saNameByLanguage( $language )
    {
        $ini = eZINI::instance();
        if ( $ini->hasVariable( 'RegionalSettings', 'LanguageSA' ) )
        {
            $langMap = $ini->variable( 'RegionalSettings', 'LanguageSA' );
            if ( !isset( $langMap[$language] ) )
            {
                return null;
            }
            $sa = $langMap[$language];
        }
        else
        {
            $sa = explode( '-', $language );
            $sa = $sa[0];
        }

        if ( in_array( $sa, $ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) ) )
        {
            return $sa;
        }
        eZDebug::writeWarning("Tried to find siteaccess based on '$language' but '$sa' is not a valid RelatedSiteAccessList[]", __METHOD__ );
        return null;
    }

    /**
     * Checks if $siteAccessName matches a configured siteaccess
     * @param $siteAccessName
     * @return bool
     */
    public static function exists( $siteAccessName )
    {
        foreach ( eZSiteAccess::siteAccessList() as $siteaccessListItem )
        {
            if ( $siteaccessListItem['name'] == $siteAccessName )
            {
                return true;
            }
        }
        return false;
    }
}
