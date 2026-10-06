<?php
/**
 * File containing the expPreloadAddress class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Where the cache preloader finds a siteaccess: the address of its site, the url prefix that selects it on that
 * host, and the pages a run starts from.
 *
 * The preloader asks the web server for pages as a visitor would, so the address has to be one the siteaccess
 * matching of site.ini sends to the siteaccess that was chosen. This class works that out from the settings alone,
 * without a request and without a database: it tries the address without a prefix, then each prefix the uri,
 * host_uri and host matching offer, and keeps the first one that site.ini's MatchOrder sends to the siteaccess.
 *
 * HostMatchMapItems entries are read with their optional third field (host;siteaccess;strict|start|end|part) and
 * HostMatchMethod, as eZSiteAccess::match() reads them, so a host map that matches the start of a host reaches the
 * same siteaccess here as in a request.
 *
 * The settings are handed in (fromINI() reads them from site.ini), so the setup/preload page, the preload command
 * and the tests build the same addresses.
 */
class expPreloadAddress
{
    private $settings;

    /**
     * @param array $settings match_order (list), uri_match_type, uri_match_element, uri_match_map (list of
     *                        'uri;siteaccess'), host_match_type, host_match_element, host_match_map (list of
     *                        'host;siteaccess[;method]'), host_match_method, host_uri_match_map (list of
     *                        'host;uri;siteaccess[;method]'), host_uri_match_method, siteaccesses (list of the
     *                        names the installation serves), default_access
     */
    public function __construct( array $settings = array() )
    {
        $this->settings = $settings + array(
            'match_order' => array( 'uri' ),
            'uri_match_type' => 'element',
            'uri_match_element' => 1,
            'uri_match_map' => array(),
            'host_match_type' => 'map',
            'host_match_element' => 0,
            'host_match_map' => array(),
            'host_match_method' => 'strict',
            'host_uri_match_map' => array(),
            'host_uri_match_method' => 'strict',
            'siteaccesses' => array(),
            'default_access' => '',
        );
    }

    /**
     * The settings of site.ini [SiteAccessSettings] and [SiteSettings] DefaultAccess.
     *
     * @param eZINI|null $ini
     * @return expPreloadAddress
     */
    public static function fromINI( $ini = null )
    {
        $ini = $ini ? $ini : eZINI::instance( 'site.ini' );
        $get = function ( $group, $name, $default ) use ( $ini )
        {
            return $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : $default;
        };
        $names = array();
        foreach ( array( 'AvailableSiteAccessList', 'RelatedSiteAccessList' ) as $list )
            foreach ( (array)$get( 'SiteAccessSettings', $list, array() ) as $name )
                if ( trim( (string)$name ) !== '' )
                    $names[trim( (string)$name )] = true;
        $order = $get( 'SiteAccessSettings', 'MatchOrder', 'uri' );

        return new self( array(
            'match_order' => is_array( $order ) ? $order : explode( ';', (string)$order ),
            'uri_match_type' => (string)$get( 'SiteAccessSettings', 'URIMatchType', 'element' ),
            'uri_match_element' => (int)$get( 'SiteAccessSettings', 'URIMatchElement', 1 ),
            'uri_match_map' => (array)$get( 'SiteAccessSettings', 'URIMatchMapItems', array() ),
            'host_match_type' => (string)$get( 'SiteAccessSettings', 'HostMatchType', 'map' ),
            'host_match_element' => (int)$get( 'SiteAccessSettings', 'HostMatchElement', 0 ),
            'host_match_map' => (array)$get( 'SiteAccessSettings', 'HostMatchMapItems', array() ),
            'host_match_method' => (string)$get( 'SiteAccessSettings', 'HostMatchMethod', 'strict' ),
            'host_uri_match_map' => (array)$get( 'SiteAccessSettings', 'HostUriMatchMapItems', array() ),
            'host_uri_match_method' => (string)$get( 'SiteAccessSettings', 'HostUriMatchMethodDefault', 'strict' ),
            'siteaccesses' => array_keys( $names ),
            'default_access' => (string)$get( 'SiteSettings', 'DefaultAccess', '' ),
        ) );
    }

    /**
     * A SiteURL as an absolute address without a trailing slash: https:// is added when it has no scheme.
     *
     * @param string $siteUrl
     * @return string|false false for an empty one
     */
    public static function baseUrl( $siteUrl )
    {
        $url = rtrim( trim( (string)$siteUrl ), '/' );
        if ( $url === '' )
            return false;
        if ( strpos( $url, '://' ) === false )
            $url = 'https://' . $url;
        return $url;
    }

    /**
     * The url prefix ('/bold', '' for none) that selects $siteaccess at $baseUrl, with whether the matching was
     * shown to reach it there.
     *
     * @param string $siteaccess
     * @param string $baseUrl the site's address (SiteURL, with a scheme)
     * @return array hash prefix (string), reached (bool), how (no_prefix, uri, host_uri, or guess when not reached)
     */
    public function prefix( $siteaccess, $baseUrl )
    {
        $siteaccess = trim( (string)$siteaccess );
        if ( $siteaccess === '' )
            return array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' );

        $host = (string)parse_url( $baseUrl, PHP_URL_HOST );
        $port = parse_url( $baseUrl, PHP_URL_PORT );
        if ( $port )
            $host .= ':' . $port;
        // SiteURL may already end in the prefix (demo.example/site): that path counts as part of the address.
        $basePath = trim( (string)parse_url( $baseUrl, PHP_URL_PATH ), '/' );

        $candidates = array( array( '', 'no_prefix' ) );
        foreach ( $this->settings['match_order'] as $method )
        {
            $method = trim( (string)$method );
            if ( $method === 'uri' )
            {
                if ( $this->settings['uri_match_type'] === 'map' )
                {
                    foreach ( $this->settings['uri_match_map'] as $item )
                    {
                        $fields = self::fields( $item );
                        if ( isset( $fields[1] ) && $fields[1] === $siteaccess && $fields[0] !== '' )
                            $candidates[] = array( '/' . trim( $fields[0], '/' ), 'uri' );
                    }
                }
                else if ( $this->settings['uri_match_type'] === 'element' && (int)$this->settings['uri_match_element'] === 1 )
                {
                    $candidates[] = array( '/' . $siteaccess, 'uri' );
                }
            }
            else if ( $method === 'host_uri' )
            {
                foreach ( $this->settings['host_uri_match_map'] as $item )
                {
                    $fields = self::fields( $item );
                    if ( isset( $fields[2] ) && $fields[2] === $siteaccess )
                        $candidates[] = array( $fields[1] !== '' ? '/' . trim( $fields[1], '/' ) : '', 'host_uri' );
                }
            }
        }

        foreach ( $candidates as $candidate )
        {
            list( $prefix, $how ) = $candidate;
            $path = $basePath;
            // a prefix SiteURL already ends in is not added again
            $own = trim( $prefix, '/' );
            if ( $own !== '' && ( $path === $own || substr( $path, -strlen( $own ) - 1 ) === '/' . $own ) )
                $prefix = '';
            else if ( $own !== '' )
                $path = ltrim( $path . '/' . $own, '/' );
            if ( $this->match( $host, $path ) === $siteaccess )
                return array( 'prefix' => $prefix, 'reached' => true, 'how' => $how );
        }

        // Nothing in the settings sends this address to the siteaccess. The prefix the older preloader used is
        // kept, so a run still goes where it used to, and the page says the address was not confirmed.
        $uri = in_array( 'uri', array_map( 'trim', $this->settings['match_order'] ), true );
        $prefix = $uri && $basePath !== $siteaccess && substr( $basePath, -strlen( $siteaccess ) - 1 ) !== '/' . $siteaccess
                ? '/' . $siteaccess : '';
        return array( 'prefix' => $prefix, 'reached' => false, 'how' => 'guess' );
    }

    /**
     * The siteaccess the settings choose for a request to $host and $path, as eZSiteAccess::match() would: the
     * methods of MatchOrder in turn, then DefaultAccess. The matchings that compare text or regular expressions
     * are not followed; they never decide here.
     *
     * @param string $host  host, with :port when it has one
     * @param string $path  path without the leading slash ('', 'bold/news')
     * @return string
     */
    public function match( $host, $path )
    {
        $path = trim( (string)$path, '/' );
        $elements = $path === '' ? array() : explode( '/', $path );
        $names = array_flip( $this->settings['siteaccesses'] );

        foreach ( $this->settings['match_order'] as $method )
        {
            switch ( trim( (string)$method ) )
            {
                case 'uri':
                    if ( $this->settings['uri_match_type'] === 'map' )
                    {
                        $first = isset( $elements[0] ) ? $elements[0] : '';
                        foreach ( $this->settings['uri_match_map'] as $item )
                        {
                            $fields = self::fields( $item );
                            if ( isset( $fields[1] ) && $fields[0] === $first && isset( $names[$fields[1]] ) )
                                return $fields[1];
                        }
                    }
                    else if ( $this->settings['uri_match_type'] === 'element' )
                    {
                        $count = max( 1, (int)$this->settings['uri_match_element'] );
                        $name = implode( '_', array_slice( $elements, 0, $count ) );
                        if ( $name !== '' && isset( $names[$name] ) )
                            return $name;
                    }
                    break;

                case 'host':
                    if ( $this->settings['host_match_type'] === 'map' )
                    {
                        foreach ( $this->settings['host_match_map'] as $item )
                        {
                            $fields = self::fields( $item );
                            if ( !isset( $fields[1] ) || $fields[0] === '' )
                                continue;
                            $method = isset( $fields[2] ) && $fields[2] !== '' ? $fields[2] : $this->settings['host_match_method'];
                            if ( self::hostMatches( $host, $fields[0], $method ) )
                                return $fields[1];
                        }
                    }
                    else if ( $this->settings['host_match_type'] === 'element' )
                    {
                        $parts = explode( '.', preg_replace( '/:\d+$/', '', $host ) );
                        $index = (int)$this->settings['host_match_element'];
                        if ( isset( $parts[$index] ) && isset( $names[$parts[$index]] ) )
                            return $parts[$index];
                    }
                    break;

                case 'host_uri':
                    foreach ( $this->settings['host_uri_match_map'] as $item )
                    {
                        $fields = self::fields( $item );
                        if ( !isset( $fields[2] ) )
                            continue;
                        $uri = trim( $fields[1], '/' );
                        if ( $uri !== '' && $path !== $uri && strpos( $path, $uri . '/' ) !== 0 )
                            continue;
                        $method = isset( $fields[3] ) && $fields[3] !== '' ? $fields[3] : $this->settings['host_uri_match_method'];
                        if ( self::hostMatches( $host, $fields[0], $method ) )
                            return $fields[2];
                    }
                    break;
            }
        }
        return (string)$this->settings['default_access'];
    }

    /**
     * The pages a run starts from: the site root (with the prefix that selects the siteaccess) and a page for
     * each of URLTranslationKeyword's sections, or the paths asked for.
     *
     * @param string $baseUrl
     * @param string $prefix  from prefix()
     * @param string $keywords URLTranslationKeyword, sections separated by ';'
     * @param array $startPaths paths to start from instead ('/about-us')
     * @return array
     */
    public static function startUrls( $baseUrl, $prefix, $keywords = '', array $startPaths = array() )
    {
        // SiteURL may already end in the siteaccess prefix (latest.demo.exponential.earth/site): the prefix is
        // added once, never repeated (/site/site).
        $prefix = rtrim( (string)$prefix, '/' );
        $basePart = rtrim( (string)parse_url( $baseUrl, PHP_URL_PATH ), '/' );
        if ( $prefix !== '' && substr( $basePart, -strlen( $prefix ) ) === $prefix )
            $prefix = '';
        $base = $baseUrl . $prefix;

        $urls = array();
        if ( $startPaths )
        {
            foreach ( $startPaths as $path )
                $urls[] = self::normalise( $base . '/' . ltrim( (string)$path, '/' ) );
            return array_values( array_unique( $urls ) );
        }

        $urls[] = self::normalise( $base . '/' );
        foreach ( explode( ';', (string)$keywords ) as $keyword )
        {
            $keyword = trim( $keyword, "/ \t\n\r" );
            if ( $keyword !== '' )
                $urls[] = self::normalise( $base . '/' . $keyword . '/' );
        }
        return array_values( array_unique( $urls ) );
    }

    /**
     * One address per page: /index.php dropped, and the trailing slash of the path, not of the query, removed
     * (the host root keeps its slash).
     *
     * @param string $url
     * @return string
     */
    public static function normalise( $url )
    {
        $url = preg_replace( '#/index\\.php(?=/|$|\?)#', '', $url, 1 );

        $parts = parse_url( $url );
        $path = isset( $parts['path'] ) ? $parts['path'] : '';

        if ( $path === '' )
        {
            $query = strpos( $url, '?' );
            return $query === false ? rtrim( $url, '/' ) . '/' : substr( $url, 0, $query ) . '/' . substr( $url, $query );
        }

        if ( $path !== '/' && substr( $path, -1 ) === '/' )
        {
            $query = strpos( $url, '?' );
            $head = $query === false ? $url : substr( $url, 0, $query );
            $tail = $query === false ? '' : substr( $url, $query );
            $url = substr( $head, 0, -1 ) . $tail;
        }

        return $url;
    }

    /**
     * The fields of a map entry: an ini array entry 'a;b;c' or one already split.
     */
    private static function fields( $item )
    {
        return array_map( 'trim', is_array( $item ) ? $item : explode( ';', (string)$item ) );
    }

    /**
     * eZSiteAccess::hostMatches(), the comparison a request uses.
     */
    private static function hostMatches( $host, $matchHost, $method )
    {
        if ( !in_array( $method, array( 'strict', 'start', 'end', 'part' ), true ) )
            return false;
        return eZSiteAccess::hostMatches( $host, $matchHost, $method );
    }
}
