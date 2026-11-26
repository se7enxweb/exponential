<?php
/**
 * File containing the expSiteAccessForNode class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The siteaccess a node belongs to, and where that siteaccess is served: what the preview of a version
 * (content/versionview) and the "View on site" link of a node choose in an installation that serves several sites
 * from one content tree, each site with its own siteaccesses and often its own host.
 * Guide: doc/eng-us/features/6.0/preview-in-owning-siteaccess.md
 *
 *   $sa  = expSiteAccessForNode::forNode( $node, 'ger-DE' );   // array( siteaccess, reason, depth )
 *   $url = expSiteAccessForNode::baseURL( $sa['siteaccess'] ); // https://wedding.example.com/ or https://example.com/eng/
 *
 * - The candidates are the siteaccesses of site.ini [SiteAccessSettings] RelatedSiteAccessList[] of the current
 *   siteaccess (the list the preview offers), AvailableSiteAccessList[] when that is empty; administration and
 *   editor siteaccesses (expSiteAccessOverview::kindOf()) are left out.
 * - The content root of a siteaccess is the deepest of content.ini [NodeSettings] RootNode, the node of site.ini
 *   [SiteSettings] IndexPage (/content/view/full/<id> or a URL alias) and the node of [SiteAccessSettings]
 *   PathPrefix. A siteaccess contains a node when its root is the node or one of its ancestors.
 * - Of the siteaccesses that contain the node, the one with the deepest root wins (the most specific site); between
 *   equally deep roots the language decides: [RegionalSettings] ContentObjectLocale equal to the language first,
 *   then a siteaccess whose SiteLanguageList[] has it (earlier in the list first), then the others; the order of the
 *   list last. No siteaccess contains the node: site.ini [SiteSettings] DefaultAccess.
 * - The address is ezpSiteAccessURL::root(): the siteaccess's host when it is matched by host (host, host_uri,
 *   HostMatchMapItems, its SiteURL), else its URI on this host.
 *
 * resolve(), pathOf() and isOtherHost() are pure (tested without a database); the candidates are read once per
 * request.
 *
 * @since 6.0.15
 * @package kernel
 */
class expSiteAccessForNode
{
    /** Why a siteaccess was chosen */
    const REASON_TREE = 'tree';
    const REASON_DEFAULT = 'default';

    /** @var array|null the candidates of the request; @var string|null the request they belong to */
    protected static $candidates = null;
    protected static $candidatesRequest = null;

    /** @var array memo of forNode() for the request */
    protected static $memo = array();

    // ------------------------------------------------------------------------------------------------ pure

    /**
     * The node ids of a path string (/1/2/60/123/), top first; empty elements (the path of a node not yet
     * published ends in //) are left out.
     *
     * @param string $pathString
     * @return int[]
     */
    public static function pathOf( $pathString )
    {
        $ids = array();
        foreach ( explode( '/', (string)$pathString ) as $part )
        {
            if ( $part !== '' && ctype_digit( $part ) && (int)$part > 0 )
                $ids[] = (int)$part;
        }
        return $ids;
    }

    /**
     * The siteaccess of a node. Pure: the same arrays always give the same answer.
     *
     * @param int[] $nodePath the node ids from the top of the tree down to the node (or to the parent of a node
     *                        that is not published yet)
     * @param string $language the language of the version (ger-DE)
     * @param array $candidates name => array( root_path => int[] (the ids from the top down to the content root),
     *                          locale => ContentObjectLocale, languages => SiteLanguageList ), in the order of the list
     * @param string $default DefaultAccess
     * @return array siteaccess, reason (tree or default), depth (of the winning root, 0 for the default)
     */
    public static function resolve( array $nodePath, $language, array $candidates, $default )
    {
        $nodePath = array_values( array_map( 'intval', $nodePath ) );
        $language = (string)$language;
        $best = null;
        $position = 0;
        foreach ( $candidates as $name => $candidate )
        {
            $position++;
            $root = isset( $candidate['root_path'] ) ? array_values( array_map( 'intval', (array)$candidate['root_path'] ) ) : array();
            $depth = count( $root );
            if ( $depth === 0 || $depth > count( $nodePath ) || array_slice( $nodePath, 0, $depth ) !== $root )
                continue;
            $score = array( $depth, -self::languageRank( $language, $candidate ), -$position );
            if ( $best === null || $score > $best['score'] )
                $best = array( 'score' => $score, 'name' => (string)$name, 'depth' => $depth );
        }
        if ( $best === null )
            return array( 'siteaccess' => (string)$default, 'reason' => self::REASON_DEFAULT, 'depth' => 0 );
        return array( 'siteaccess' => $best['name'], 'reason' => self::REASON_TREE, 'depth' => $best['depth'] );
    }

    /**
     * How well a siteaccess shows a language: 0 its ContentObjectLocale, 1 + the position in SiteLanguageList[],
     * 1000 not at all.
     *
     * @param string $language
     * @param array $candidate
     * @return int
     */
    public static function languageRank( $language, array $candidate )
    {
        if ( $language === '' )
            return 1000;
        if ( isset( $candidate['locale'] ) && (string)$candidate['locale'] === $language )
            return 0;
        $position = array_search( $language, array_values( array_map( 'strval', isset( $candidate['languages'] ) ? (array)$candidate['languages'] : array() ) ), true );
        return $position === false ? 1000 : 1 + (int)$position;
    }

    /**
     * Whether an address is on another host than $currentHost (the port is not compared: one host on two ports is
     * the same site).
     *
     * @param string $url
     * @param string $currentHost
     * @return bool
     */
    public static function isOtherHost( $url, $currentHost )
    {
        $host = strtolower( rtrim( (string)parse_url( (string)$url, PHP_URL_HOST ), '.' ) );
        $current = strtolower( rtrim( preg_replace( '/:\d+$/', '', trim( (string)$currentHost ) ), '.' ) );
        return $host !== '' && $current !== '' && $host !== $current;
    }

    /**
     * The value of a switch of site.ini [SiteAccessSettings] (enabled unless set to disabled, false or 0).
     *
     * @param string|null $value
     * @return bool
     */
    public static function switchOn( $value )
    {
        return $value === null || !in_array( strtolower( trim( (string)$value ) ), array( 'disabled', 'false', '0', 'no' ), true );
    }

    // ------------------------------------------------------------------------------------------------ settings

    /**
     * site.ini [SiteAccessSettings] PreviewInOwningSiteAccess of the current siteaccess: the preview preselects the
     * siteaccess the node belongs to (default enabled).
     *
     * @return bool
     */
    public static function previewInOwningSiteAccess()
    {
        return self::switchOn( self::setting( 'PreviewInOwningSiteAccess' ) );
    }

    /**
     * site.ini [SiteAccessSettings] PreviewOnOwningHost of the current siteaccess: the preview of a siteaccess that
     * is served on another host is shown on that host (default enabled).
     *
     * @return bool
     */
    public static function previewOnOwningHost()
    {
        return self::switchOn( self::setting( 'PreviewOnOwningHost' ) );
    }

    /**
     * @param string $name
     * @return string|null
     */
    protected static function setting( $name )
    {
        $ini = eZINI::instance();
        return $ini->hasVariable( 'SiteAccessSettings', $name ) ? (string)$ini->variable( 'SiteAccessSettings', $name ) : null;
    }

    // ------------------------------------------------------------------------------------------------ the installation

    /**
     * The siteaccess of a node in a language, for the current request.
     *
     * @param eZContentObjectTreeNode|int[]|string $node a node, its path ids or its path string
     * @param string $language
     * @return array see resolve()
     */
    public static function forNode( $node, $language )
    {
        if ( $node instanceof eZContentObjectTreeNode )
            $path = self::pathOf( (string)$node->attribute( 'path_string' ) );
        else if ( is_array( $node ) )
            $path = array_values( array_map( 'intval', $node ) );
        else
            $path = self::pathOf( (string)$node );

        self::resetForRequest();
        $key = implode( '/', $path ) . '|' . (string)$language;
        if ( isset( self::$memo[$key] ) )
            return self::$memo[$key];
        $default = (string)eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' );
        return self::$memo[$key] = self::resolve( $path, $language, self::candidates(), $default );
    }

    /**
     * The address a siteaccess is served at (ending in /), ezpSiteAccessURL::root(); false for one that is not
     * available.
     *
     * @param string $siteaccess
     * @return string|false
     */
    public static function baseURL( $siteaccess )
    {
        try
        {
            return ezpSiteAccessURL::root( (string)$siteaccess );
        }
        catch ( \Throwable $e )
        {
            eZDebug::writeWarning( $e->getMessage(), __METHOD__ );
            return false;
        }
    }

    /**
     * The candidates of the request (see resolve()), read once per request.
     *
     * @return array
     */
    public static function candidates()
    {
        self::resetForRequest();
        if ( self::$candidates !== null )
            return self::$candidates;
        $ini = eZINI::instance();
        $names = (array)$ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' );
        if ( !array_filter( $names ) )
            $names = (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        $candidates = array();
        foreach ( $names as $name )
        {
            $name = trim( (string)$name );
            if ( $name === '' || isset( $candidates[$name] ) || !expSiteAccessOverview::isPlainName( $name ) )
                continue;
            try
            {
                $candidate = self::readCandidate( $name );
            }
            catch ( \Throwable $e )
            {
                eZDebug::writeWarning( "Siteaccess $name: " . $e->getMessage(), __METHOD__ );
                continue;
            }
            if ( $candidate !== null )
                $candidates[$name] = $candidate;
        }
        return self::$candidates = $candidates;
    }

    /**
     * The settings of one siteaccess that resolve() reads; null for an administration or editor siteaccess.
     *
     * @param string $name
     * @return array|null
     */
    protected static function readCandidate( $name )
    {
        $site = eZSiteAccess::getIni( $name, 'site.ini' );
        $get = function ( eZINI $ini, $group, $setting, $default )
        {
            return $ini->hasVariable( $group, $setting ) ? $ini->variable( $group, $setting ) : $default;
        };
        $kind = expSiteAccessOverview::kindOf( array(
            'site_design' => (string)$get( $site, 'DesignSettings', 'SiteDesign', '' ),
            'additional_designs' => (array)$get( $site, 'DesignSettings', 'AdditionalSiteDesignList', array() ),
            'require_user_login' => (string)$get( $site, 'SiteAccessSettings', 'RequireUserLogin', '' ),
            'explicit_type' => (string)$get( $site, 'SiteAccessSettings', 'SiteAccessType', '' ),
        ) );
        if ( $kind === 'admin' || $kind === 'editor' )
            return null;

        $content = eZSiteAccess::getIni( $name, 'content.ini' );
        $roots = array();
        $rootNode = (int)$get( $content, 'NodeSettings', 'RootNode', 2 );
        if ( $rootNode > 0 )
            $roots[] = $rootNode;
        $indexNode = self::nodeOfAddress( (string)$get( $site, 'SiteSettings', 'IndexPage', '' ) );
        if ( $indexNode > 0 )
            $roots[] = $indexNode;
        $prefixNode = self::nodeOfAddress( (string)$get( $site, 'SiteAccessSettings', 'PathPrefix', '' ) );
        if ( $prefixNode > 0 )
            $roots[] = $prefixNode;

        $rootPath = array();
        foreach ( array_unique( $roots ) as $nodeID )
        {
            $rootNodeObject = eZContentObjectTreeNode::fetch( $nodeID, false, false );
            $path = is_array( $rootNodeObject ) && isset( $rootNodeObject['path_string'] ) ? self::pathOf( $rootNodeObject['path_string'] ) : array();
            if ( count( $path ) > count( $rootPath ) )
                $rootPath = $path;
        }
        return array(
            'root_path' => $rootPath,
            'locale' => (string)$get( $site, 'RegionalSettings', 'ContentObjectLocale', '' ),
            'languages' => array_values( array_filter( array_map( 'strval', (array)$get( $site, 'RegionalSettings', 'SiteLanguageList', array() ) ) ) ),
        );
    }

    /**
     * The node of an IndexPage or PathPrefix: /content/view/full/<id>, else a URL alias; 0 for the top and for
     * nothing found.
     *
     * @param string $address
     * @return int
     */
    protected static function nodeOfAddress( $address )
    {
        $address = trim( (string)$address, " \t/" );
        if ( $address === '' )
            return 0;
        if ( preg_match( '#^content/view/[a-z_]+/(\d+)#i', $address, $m ) )
            return (int)$m[1];
        $nodeID = eZURLAliasML::fetchNodeIDByPath( $address );
        return is_numeric( $nodeID ) ? (int)$nodeID : 0;
    }

    /**
     * Forgets what an earlier request read (a persistent worker serves many).
     *
     * @return void
     */
    protected static function resetForRequest()
    {
        $request = class_exists( 'eZExecution' ) && method_exists( 'eZExecution', 'requestKey' ) ? eZExecution::requestKey() : null;
        if ( $request !== self::$candidatesRequest )
        {
            self::$candidates = null;
            self::$memo = array();
            self::$candidatesRequest = $request;
        }
    }

    /**
     * For tests: candidates instead of the settings, until the next request.
     *
     * @param array|null $candidates
     * @return void
     */
    public static function setCandidates( $candidates )
    {
        self::resetForRequest();
        self::$candidates = $candidates;
        self::$memo = array();
    }
}

?>
