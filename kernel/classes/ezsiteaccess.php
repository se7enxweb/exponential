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
     * @return array
     */
    public static function match( eZURI $uri, $host, $port = 80, $file = '/index.php' )
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
                                $matchHostMethod = isset( $matchMapItem[2] ) && $matchMapItem[2] !== '' ? $matchMapItem[2] : $defaultHostMatchMethod;
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

                    // No entry matched, for example the address has no language segment: an entry of
                    // DefaultHostUriMatchMapItems for the host gives the siteaccess and the uri part of its links,
                    // chosen by the languages the browser accepts. The address itself is not shortened.
                    if ( $ini->hasVariable( 'SiteAccessSettings', 'DefaultHostUriMatchMapItems' ) )
                    {
                        $default = self::matchDefaultHostUri( $ini->variableArray( 'SiteAccessSettings', 'DefaultHostUriMatchMapItems' ),
                                                              $host,
                                                              $ini->variable( 'SiteAccessSettings', 'HostUriMatchMethodDefault' ),
                                                              isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? (string)$_SERVER['HTTP_ACCEPT_LANGUAGE'] : '' );
                        if ( $default !== null )
                        {
                            $access = array_merge( $access, $default );
                            $access['type'] = $type;
                            // The web kernel sends the browser on to the address with the segment, once (/ to /ger):
                            // the pages a cache keeps then have one address and one language each. Only when an entry
                            // of HostUriMatchMapItems takes that address, else it would land here again
                            if ( $default['uri_part']
                                 && ( !$ini->hasVariable( 'SiteAccessSettings', 'DefaultHostUriRedirect' )
                                      || $ini->variable( 'SiteAccessSettings', 'DefaultHostUriRedirect' ) !== 'disabled' )
                                 && $ini->hasVariable( 'SiteAccessSettings', 'HostUriMatchMapItems' ) )
                            {
                                $access['redirect'] = self::hostUriEntryExists( $ini->variableArray( 'SiteAccessSettings', 'HostUriMatchMapItems' ),
                                                                                $host, implode( '/', $default['uri_part'] ),
                                                                                $ini->variable( 'SiteAccessSettings', 'HostUriMatchMethodDefault' ) );
                            }
                            return $access;
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
                            if ( $ini->hasVariable( 'SiteAccessSettings', 'RedirectOnNormalize' ) && $ini->variable( 'SiteAccessSettings', 'RedirectOnNormalize' ) == 'enabled' )
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
        return $access;
    }

    /**
     * The default of host_uri matching for an address no HostUriMatchMapItems entry matched: the first entry of
     * $items (DefaultHostUriMatchMapItems[]=host;uri;siteaccess[;method[;language]]) whose host matches and whose
     * language the browser accepts, trying the languages of $acceptLanguage from the most wanted; else the first
     * entry for the host without a language. method is strict, start, end or part; empty or "default" is
     * $defaultMethod (HostUriMatchMethodDefault). A language matches itself and its regional forms (de: de, de-CH).
     *
     * @param array $items the entries, each split at ";"
     * @param string $host
     * @param string $defaultMethod
     * @param string $acceptLanguage the Accept-Language header of the request
     * @return array|null array( name, uri_part[, vary] ) with vary = Accept-Language when the entries for the host have
     *                    more than one uri part (language variants), so the answer depends on the browser; null when no
     *                    entry for the host applies
     */
    static function matchDefaultHostUri( array $items, $host, $defaultMethod, $acceptLanguage )
    {
        $forHost = array();
        $variants = array();
        foreach ( $items as $item )
        {
            $item = (array)$item;
            // An empty host matches any host with part or start, as in HostUriMatchMapItems
            if ( !isset( $item[2] ) || (string)$item[2] === '' )
                continue;
            $method = isset( $item[3] ) && $item[3] !== '' && $item[3] !== 'default' ? (string)$item[3] : (string)$defaultMethod;
            if ( !self::hostMatches( $host, (string)$item[0], $method ) )
                continue;
            $language = isset( $item[4] ) ? strtolower( trim( (string)$item[4] ) ) : '';
            $uri = trim( (string)$item[1], '/' );
            $variants[$uri] = true;
            $forHost[] = array( 'uri' => $uri, 'name' => (string)$item[2], 'language' => $language );
        }

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
            return null;

        $access = array( 'name' => $chosen['name'],
                         'uri_part' => $chosen['uri'] !== '' ? explode( '/', $chosen['uri'] ) : array() );
        // Only where the host has more than one language variant does the browser make a difference
        if ( count( $variants ) > 1 )
            $access['vary'] = 'Accept-Language';
        return $access;
    }

    /**
     * Whether an entry of HostUriMatchMapItems ($items, each split at ";") takes the address with the uri part $uri on
     * $host, so a redirect there does not come back to the default.
     *
     * @param array $items
     * @param string $host
     * @param string $uri for example "ger"
     * @param string $defaultMethod HostUriMatchMethodDefault
     * @return bool
     */
    static function hostUriEntryExists( array $items, $host, $uri, $defaultMethod )
    {
        foreach ( $items as $item )
        {
            $item = (array)$item;
            if ( !isset( $item[2] ) || trim( (string)$item[1], '/' ) !== $uri )
                continue;
            $method = isset( $item[3] ) && $item[3] !== '' ? (string)$item[3] : (string)$defaultMethod;
            if ( self::hostMatches( $host, (string)$item[0], $method ) )
                return true;
        }
        return false;
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
        foreach ( explode( ',', (string)$header ) as $position => $part )
        {
            $fields = explode( ';', $part );
            $tag = strtolower( trim( $fields[0] ) );
            if ( $tag === '' || $tag === '*' || !preg_match( '/^[a-z0-9-]+$/', $tag ) )
                continue;
            $quality = 1.0;
            foreach ( array_slice( $fields, 1 ) as $parameter )
            {
                if ( preg_match( '/^\s*q\s*=\s*([0-9.]+)\s*$/i', $parameter, $match ) )
                    $quality = (float)$match[1];
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
     * @param string $host
     * @param string $matchHost
     * @param string $method strict, start, end or part
     * @return bool
     */
    static function hostMatches( $host, $matchHost, $method )
    {
        $host = (string)$host;
        $matchHost = (string)$matchHost;
        switch ( $method )
        {
            case 'strict':
                return $matchHost === $host;
            case 'start':
                return strpos( $host, $matchHost ) === 0;
            case 'end':
                return strstr( $host, $matchHost ) === $matchHost;
            case 'part':
                return strpos( $host, $matchHost ) !== false;
        }
        eZDebug::writeError( "Unknown host match: $method", 'access' );
        return false;
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

            eZUpdateDebugSettings();
            eZDebugSetting::writeDebug( 'kernel-siteaccess', "Updated settings to use siteaccess '$name'", __METHOD__ );
        }

        return $access;
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
