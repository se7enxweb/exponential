<?php
/**
 * File containing the expSiteAccessOverview class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Every siteaccess of the installation, how a request reaches it and where it is: the siteaccess part of
 * Setup > System information (#552).
 *
 * The page answers three questions an administrator otherwise has to piece together from INI files:
 * - which siteaccesses there are (site.ini [SiteAccessSettings] AvailableSiteAccessList and RelatedSiteAccessList),
 *   with the design, locale, languages, access extensions and login rule of each;
 * - which rules map a request to a siteaccess (MatchOrder, then the URI, host, host-URI, port, server variable and
 *   index file rules in that order, DefaultAccess last), and which entries reach each siteaccess;
 * - which siteaccess this very request got, and by which rule.
 *
 * build() is pure: it takes the settings as arrays and gives the map, so it is tested without a database or INI
 * files (tests/tests/kernel/classes/expSiteAccessOverviewTest.php). gather() reads the settings: the matching rules
 * from site.ini without siteaccess overrides (as eZSiteAccess::match() and ezpSiteAccessURL read them), and the
 * settings of each siteaccess through eZSiteAccess::getIni(), which loads them into a separate eZINI and leaves the
 * current siteaccess as it is. The "Open" link of each siteaccess is ezpSiteAccessURL::root(), the code of the
 * admin's "Open the site" link. Nothing here is a secret: no database setting is read.
 *
 * The kind of a siteaccess (admin, editor, login, site) is site.ini [SiteAccessSettings] SiteAccessType when a
 * siteaccess sets it, else it is worked out from its designs and RequireUserLogin (kindOf()).
 */
class expSiteAccessOverview
{
    /** The site.ini [SiteAccessSettings] settings the matching reads, with their defaults. */
    const MATCH_SETTINGS = array(
        'AvailableSiteAccessList' => array(), 'RelatedSiteAccessList' => array(), 'MatchOrder' => '',
        'StaticMatch' => '', 'URIMatchType' => '', 'URIMatchElement' => '1', 'URIMatchMapItems' => array(),
        'URIMatchSubtextPre' => '', 'URIMatchSubtextPost' => '', 'URIMatchRegexp' => '', 'URIMatchRegexpItem' => '',
        'HostMatchType' => '', 'HostMatchMapItems' => array(), 'HostMatchMethod' => 'strict', 'HostMatchElement' => '',
        'HostMatchSubtextPre' => '', 'HostMatchSubtextPost' => '', 'HostMatchRegexp' => '', 'HostMatchRegexpItem' => '',
        'HostUriMatchMapItems' => array(), 'HostUriMatchMethodDefault' => 'strict', 'ServerVariableName' => '',
        'IndexMatchType' => '', 'RemoveSiteAccessIfDefaultAccess' => 'disabled',
    );

    /** The probes of MatchOrder, by the eZSiteAccess::TYPE_* constant a request matched by them gets. */
    const PROBE_BY_TYPE = array( 1 => 'default', 2 => 'uri', 3 => 'port', 4 => 'host', 5 => 'index', 6 => 'static',
                                 7 => 'servervar', 9 => 'host_uri' );

    /**
     * The map for this installation and this request.
     *
     * @return array see build()
     */
    public static function gather()
    {
        $global = new eZINI( 'site.ini', 'settings', null, null, true );
        $settings = array();
        foreach ( self::MATCH_SETTINGS as $name => $default )
            $settings[$name] = $global->hasVariable( 'SiteAccessSettings', $name ) ? $global->variable( 'SiteAccessSettings', $name ) : $default;
        $settings['DefaultAccess'] = (string)$global->variable( 'SiteSettings', 'DefaultAccess' );
        $settings['PortAccessSettings'] = $global->hasGroup( 'PortAccessSettings' ) ? (array)$global->group( 'PortAccessSettings' ) : array();

        $siteaccesses = array();
        foreach ( self::names( $settings ) as $name )
        {
            $siteaccesses[$name] = self::siteAccessSettings( $name );
            $url = in_array( $name, (array)$settings['AvailableSiteAccessList'], true ) ? ezpSiteAccessURL::root( $name ) : false;
            $siteaccesses[$name]['url'] = $url === false ? '' : $url;
        }

        $current = eZSiteAccess::current();
        $currentInfo = array(
            'name' => isset( $current['name'] ) ? (string)$current['name'] : '',
            'type' => isset( $current['type'] ) ? (int)$current['type'] : 0,
            'uri_part' => isset( $current['uri_part'] ) ? array_values( (array)$current['uri_part'] ) : array(),
            'host' => (string)eZSys::hostname(),
            'port' => (string)eZSys::serverVariable( 'SERVER_PORT', true ),
        );
        return self::build( $settings, $siteaccesses, $currentInfo );
    }

    /**
     * The settings of one siteaccess that the page shows, read without changing the current siteaccess.
     */
    protected static function siteAccessSettings( $name )
    {
        $empty = array( 'site_design' => '', 'additional_designs' => array(), 'locale' => '', 'languages' => array(),
                        'access_extensions' => array(), 'show_hidden_nodes' => '', 'require_user_login' => '',
                        'explicit_type' => '', 'readable' => false );
        if ( !self::isPlainName( $name ) )
            return $empty;
        try
        {
            $ini = eZSiteAccess::getIni( $name, 'site.ini' );
        }
        catch ( Throwable $e )
        {
            return $empty;
        }
        $get = function ( $group, $setting, $default ) use ( $ini )
        {
            return $ini->hasVariable( $group, $setting ) ? $ini->variable( $group, $setting ) : $default;
        };
        return array(
            'site_design' => (string)$get( 'DesignSettings', 'SiteDesign', '' ),
            'additional_designs' => (array)$get( 'DesignSettings', 'AdditionalSiteDesignList', array() ),
            'locale' => (string)$get( 'RegionalSettings', 'Locale', '' ),
            'languages' => (array)$get( 'RegionalSettings', 'SiteLanguageList', array() ),
            'access_extensions' => (array)$get( 'ExtensionSettings', 'ActiveAccessExtensions', array() ),
            'show_hidden_nodes' => (string)$get( 'SiteAccessSettings', 'ShowHiddenNodes', '' ),
            'require_user_login' => (string)$get( 'SiteAccessSettings', 'RequireUserLogin', '' ),
            'explicit_type' => (string)$get( 'SiteAccessSettings', 'SiteAccessType', '' ),
            'readable' => true,
        );
    }

    /**
     * The siteaccess names: AvailableSiteAccessList, then those only in RelatedSiteAccessList, each once.
     */
    public static function names( array $settings )
    {
        $names = array();
        foreach ( array( 'AvailableSiteAccessList', 'RelatedSiteAccessList' ) as $list )
            foreach ( isset( $settings[$list] ) ? (array)$settings[$list] : array() as $name )
                if ( is_string( $name ) && trim( $name ) !== '' && !in_array( trim( $name ), $names, true ) )
                    $names[] = trim( $name );
        return $names;
    }

    /**
     * Whether a name can be a siteaccess directory: no path, no dots, nothing to wash.
     */
    public static function isPlainName( $name )
    {
        return is_string( $name ) && preg_match( '/^[A-Za-z0-9_-]+$/', $name ) === 1;
    }

    /**
     * MatchOrder as a list of probes.
     */
    public static function matchOrder( $order )
    {
        $order = is_array( $order ) ? $order : explode( ';', (string)$order );
        return array_values( array_filter( array_map( 'trim', $order ), function ( $probe ) { return $probe !== ''; } ) );
    }

    /**
     * The kind of a siteaccess: admin, editor, login (a site that needs a login) or site.
     * site.ini [SiteAccessSettings] SiteAccessType wins when a siteaccess sets it.
     */
    public static function kindOf( array $sa )
    {
        $explicit = isset( $sa['explicit_type'] ) ? strtolower( trim( (string)$sa['explicit_type'] ) ) : '';
        if ( $explicit !== '' && preg_match( '/^[a-z0-9_-]+$/', $explicit ) )
            return $explicit;
        $design = isset( $sa['site_design'] ) ? strtolower( (string)$sa['site_design'] ) : '';
        $login = isset( $sa['require_user_login'] ) && strtolower( (string)$sa['require_user_login'] ) === 'true';
        if ( strpos( $design, 'editor' ) !== false )
            return 'editor';
        if ( strpos( $design, 'admin' ) === 0 )
            return 'admin';
        if ( $login )
        {
            foreach ( isset( $sa['additional_designs'] ) ? (array)$sa['additional_designs'] : array() as $additional )
                if ( strpos( strtolower( (string)$additional ), 'admin' ) === 0 )
                    return 'admin';
            return 'login';
        }
        return 'site';
    }

    /**
     * "a;b;c" items (or arrays, as eZINI::variableArray() gives them) as arrays.
     */
    protected static function items( $list, $minimum )
    {
        $items = array();
        foreach ( (array)$list as $item )
        {
            $parts = is_array( $item ) ? array_values( $item ) : explode( ';', (string)$item );
            $parts = array_map( 'trim', array_map( 'strval', $parts ) );
            if ( count( $parts ) >= $minimum && $parts[0] !== '' && $parts[$minimum - 1] !== '' )
                $items[] = $parts;
        }
        return $items;
    }

    /**
     * The map. Pure: the same arrays always give the same map.
     *
     * @param array $settings the site.ini [SiteAccessSettings] values of MATCH_SETTINGS, plus DefaultAccess and
     *                        PortAccessSettings (port => siteaccess), as read without siteaccess overrides
     * @param array $siteaccesses name => the settings of siteAccessSettings() plus 'url' (the Open link, '' for none)
     * @param array $current name, type (eZSiteAccess::TYPE_*), uri_part, host and port of this request
     * @return array count, default, match_order, static, rules (the ordered rules, each with its entries),
     *               siteaccesses (one row each) and current (the siteaccess of this request, the probe and entry
     *               that chose it)
     */
    public static function build( array $settings, array $siteaccesses, array $current )
    {
        $settings += self::MATCH_SETTINGS + array( 'DefaultAccess' => '', 'PortAccessSettings' => array() );
        $available = array_values( array_map( 'strval', (array)$settings['AvailableSiteAccessList'] ) );
        $related = array_values( array_map( 'strval', (array)$settings['RelatedSiteAccessList'] ) );
        $default = (string)$settings['DefaultAccess'];
        $order = self::matchOrder( $settings['MatchOrder'] );
        $orderNone = $order === array( 'none' );
        $static = trim( (string)$settings['StaticMatch'] );

        // The rules, in MatchOrder, each with the entries that name a siteaccess.
        $rules = array();
        $position = 0;
        foreach ( $orderNone ? array() : $order as $probe )
        {
            $rule = array( 'probe' => $probe, 'position' => ++$position, 'method' => '', 'entries' => array(),
                           'pattern' => '', 'known' => true, 'wins' => false );
            switch ( $probe )
            {
                case 'uri':
                    $type = (string)$settings['URIMatchType'];
                    $rule['method'] = $type;
                    if ( $type === 'map' )
                    {
                        foreach ( self::items( $settings['URIMatchMapItems'], 2 ) as $item )
                            $rule['entries'][] = array( 'match' => '/' . $item[0] . '/', 'siteaccess' => $item[1], 'key' => $item[0] );
                    }
                    else if ( $type === 'element' )
                    {
                        $elements = max( 1, (int)$settings['URIMatchElement'] );
                        $rule['pattern'] = (string)$elements;
                        foreach ( $available as $name )
                        {
                            // a name of N parts joined by _ is reached by its N first URI elements
                            $parts = explode( '_', $name );
                            if ( $elements === 1 || count( $parts ) === $elements )
                                $rule['entries'][] = array( 'match' => '/' . ( $elements === 1 ? $name : implode( '/', $parts ) ) . '/',
                                                            'siteaccess' => $name, 'key' => $elements === 1 ? $name : implode( '/', $parts ) );
                        }
                    }
                    else if ( $type === 'text' )
                        $rule['pattern'] = (string)$settings['URIMatchSubtextPre'] . '…' . (string)$settings['URIMatchSubtextPost'];
                    else if ( $type === 'regexp' )
                        $rule['pattern'] = (string)$settings['URIMatchRegexp'];
                    else
                        $rule['known'] = false;
                    break;
                case 'host':
                    $type = (string)$settings['HostMatchType'];
                    $rule['method'] = $type;
                    if ( $type === 'map' )
                    {
                        foreach ( self::items( $settings['HostMatchMapItems'], 2 ) as $item )
                            $rule['entries'][] = array( 'match' => $item[0], 'siteaccess' => $item[1], 'key' => strtolower( $item[0] ),
                                                        'host_method' => isset( $item[2] ) && $item[2] !== '' ? $item[2] : (string)$settings['HostMatchMethod'] );
                    }
                    else if ( $type === 'element' )
                        $rule['pattern'] = (string)$settings['HostMatchElement'];
                    else if ( $type === 'text' )
                        $rule['pattern'] = (string)$settings['HostMatchSubtextPre'] . '…' . (string)$settings['HostMatchSubtextPost'];
                    else if ( $type === 'regexp' )
                        $rule['pattern'] = (string)$settings['HostMatchRegexp'];
                    else
                        $rule['known'] = false;
                    break;
                case 'host_uri':
                    $rule['method'] = 'map';
                    foreach ( self::items( $settings['HostUriMatchMapItems'], 3 ) as $item )
                        $rule['entries'][] = array( 'match' => $item[0] . ( $item[1] !== '' ? '/' . $item[1] . '/' : '' ), 'siteaccess' => $item[2],
                                                    'key' => strtolower( $item[0] ) . '|' . $item[1],
                                                    'host_method' => isset( $item[3] ) && $item[3] !== '' ? $item[3] : (string)$settings['HostUriMatchMethodDefault'] );
                    break;
                case 'port':
                    foreach ( (array)$settings['PortAccessSettings'] as $port => $name )
                        if ( is_scalar( $name ) && (string)$name !== '' )
                            $rule['entries'][] = array( 'match' => ':' . $port, 'siteaccess' => (string)$name, 'key' => (string)$port );
                    break;
                case 'servervar':
                    $rule['pattern'] = (string)$settings['ServerVariableName'];
                    break;
                case 'index':
                    $rule['method'] = (string)$settings['IndexMatchType'];
                    break;
                default:
                    $rule['known'] = false;
            }
            $rules[] = $rule;
        }

        // Which rule and entry chose this request's siteaccess.
        $currentName = isset( $current['name'] ) ? (string)$current['name'] : '';
        $currentType = isset( $current['type'] ) ? (int)$current['type'] : 0;
        $uriPart = isset( $current['uri_part'] ) ? implode( '/', (array)$current['uri_part'] ) : '';
        $probe = isset( self::PROBE_BY_TYPE[$currentType] ) ? self::PROBE_BY_TYPE[$currentType] : '';
        // eZSiteAccess::match() gives the default siteaccess the URI type when MatchOrder has uri (for ezurl): with
        // no URI element taken, the default rule chose it.
        if ( $probe === 'uri' && $uriPart === '' )
            $probe = 'default';
        if ( $probe === '' && $currentName !== '' )
            $probe = 'other';
        $winningEntry = null;
        foreach ( $rules as $r => $rule )
        {
            if ( $rule['probe'] !== $probe )
                continue;
            $rules[$r]['wins'] = true;
            foreach ( $rule['entries'] as $e => $entry )
            {
                if ( $entry['siteaccess'] !== $currentName )
                    continue;
                if ( $probe === 'uri' && strcasecmp( $entry['key'], $uriPart ) !== 0 )
                    continue;
                if ( $probe === 'host' && isset( $current['host'] ) && ( $entry['host_method'] ?? 'strict' ) === 'strict'
                     && strcasecmp( $entry['key'], preg_replace( '/:\d+$/', '', (string)$current['host'] ) ) !== 0 )
                    continue;
                if ( $probe === 'port' && isset( $current['port'] ) && (string)$entry['key'] !== (string)$current['port'] )
                    continue;
                $rules[$r]['entries'][$e]['wins'] = true;
                $winningEntry = $entry['match'];
                break;
            }
            break;
        }

        // One row per siteaccess.
        $rows = array();
        foreach ( self::names( array( 'AvailableSiteAccessList' => $available, 'RelatedSiteAccessList' => $related ) ) as $name )
        {
            $sa = isset( $siteaccesses[$name] ) && is_array( $siteaccesses[$name] ) ? $siteaccesses[$name] : array();
            $sa += array( 'site_design' => '', 'additional_designs' => array(), 'locale' => '', 'languages' => array(),
                          'access_extensions' => array(), 'show_hidden_nodes' => '', 'require_user_login' => '',
                          'explicit_type' => '', 'readable' => false, 'url' => '' );
            $matchedBy = array();
            foreach ( $rules as $rule )
                foreach ( $rule['entries'] as $entry )
                    if ( $entry['siteaccess'] === $name )
                        $matchedBy[] = array( 'probe' => $rule['probe'], 'match' => $entry['match'] );
            $isAvailable = in_array( $name, $available, true );
            $rows[] = array(
                'name' => $name,
                'kind' => self::kindOf( $sa ),
                'available' => $isAvailable,
                'related' => in_array( $name, $related, true ),
                'is_default' => $name === $default,
                'is_current' => $name === $currentName,
                'is_static' => $static !== '' && $name === $static,
                'site_design' => (string)$sa['site_design'],
                'additional_designs' => array_values( array_filter( array_map( 'strval', (array)$sa['additional_designs'] ), 'strlen' ) ),
                'locale' => (string)$sa['locale'],
                'languages' => array_values( array_filter( array_map( 'strval', (array)$sa['languages'] ), 'strlen' ) ),
                'access_extensions' => array_values( array_filter( array_map( 'strval', (array)$sa['access_extensions'] ), 'strlen' ) ),
                'show_hidden_nodes' => strtolower( (string)$sa['show_hidden_nodes'] ) === 'true',
                'require_user_login' => strtolower( (string)$sa['require_user_login'] ) === 'true',
                'readable' => (bool)$sa['readable'],
                'matched_by' => $matchedBy,
                // only the default rule reaches a siteaccess that no entry names
                'reachable' => $isAvailable && ( $matchedBy || $name === $default || $static === $name ),
                'url' => $isAvailable ? (string)$sa['url'] : '',
                'settings_path' => self::isPlainName( $name ) ? 'settings/view/' . $name . '/site.ini' : '',
            );
        }

        return array(
            'count' => count( $rows ),
            'available_count' => count( $available ),
            'default' => $default,
            'match_order' => $orderNone ? array( 'none' ) : $order,
            'static' => $static,
            'rules' => $rules,
            'siteaccesses' => $rows,
            'current' => array( 'name' => $currentName, 'probe' => $probe, 'entry' => $winningEntry,
                                'uri_part' => $uriPart ),
        );
    }

    /**
     * The short line of the overview: "17 (default: site; match: uri, host)".
     */
    public static function summary( array $map )
    {
        return sprintf( '%d (default: %s; match: %s)', (int)$map['count'], $map['default'] !== '' ? $map['default'] : '-',
                        $map['match_order'] ? implode( ', ', $map['match_order'] ) : '-' );
    }
}
