<?php
/**
 * File containing the expViewCountPathResolver class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Finds the node a requested path showed, for the view counter: the way the siteaccess that served it would.
 *
 * - "/" (and "/index.php") is the siteaccess's site.ini [SiteSettings] IndexPage: /content/view/full/<node> or a
 *   URL alias. It is never the content root reached by gluing "/" to a PathPrefix.
 * - /content/view/<view mode>/<node> is that node, for every view mode (or those of ViewModes[]).
 * - Anything else is a URL alias: first with each path prefix in front (the siteaccess's PathPrefix, or
 *   logfile.ini PathPrefix[]), unless the path starts with one of PathPrefixExclude[], then as it is.
 * - View parameters (/(offset)/10) and everything after them are not part of the alias.
 *
 * The alias lookup is a callable (eZURLAliasML::fetchNodeIDByPath() by default), so the rules can be tested without
 * a database. Each path is looked up once per siteaccess language list and process.
 */
class expViewCountPathResolver
{
    /** @var callable string path => int|false node id */
    private $lookup;

    /** @var callable|null string[] locale codes => void, sets the languages the lookup uses */
    private $languages;

    /** @var string[] view modes counted for /content/view/<mode>/<node>; empty: all */
    private $viewModes;

    /** @var array cache: language key => path => node id|false */
    private $cache = array();

    /** @var int how many lookups reached the callable */
    private $lookups = 0;

    /** @var string the language key set last */
    private $currentLanguages = null;

    /**
     * @param callable|null $lookup path => node id or false; null: eZURLAliasML::fetchNodeIDByPath()
     * @param string[] $viewModes view modes counted for /content/view/<mode>/<node>; empty: all
     * @param callable|null $languages locale codes => void, called before the lookups of a site with its
     *                                 SiteLanguageList[]; null: eZContentLanguage::setPrioritizedLanguages()
     */
    public function __construct( $lookup = null, array $viewModes = array(), $languages = null )
    {
        $this->lookup = $lookup !== null ? $lookup : function ( $path )
        {
            return eZURLAliasML::fetchNodeIDByPath( $path );
        };
        $this->languages = $languages !== null ? $languages : function ( array $locales )
        {
            if ( $locales )
                eZContentLanguage::setPrioritizedLanguages( $locales );
            else
                eZContentLanguage::clearPrioritizedLanguages();
        };
        $this->viewModes = array_values( array_filter( array_map( 'strval', $viewModes ), 'strlen' ) );
    }

    /**
     * The elements of a path, URL decoded, without "index.php" in front and without view parameters. A request for
     * "/" has none.
     *
     * @param string $path still URL encoded, without query
     * @return string[]
     */
    public static function elements( $path )
    {
        $elements = array();
        foreach ( explode( '/', (string)$path ) as $element )
        {
            if ( $element === '' )
                continue;
            $element = rawurldecode( $element );
            // view parameters: (offset)/10, (language)/ger-DE ... and what follows them
            if ( $element[0] === '(' && substr( $element, -1 ) === ')' )
                break;
            $elements[] = $element;
        }
        if ( $elements && strtolower( $elements[0] ) === 'index.php' )
            array_shift( $elements );
        return $elements;
    }

    /**
     * Removes the first element when it is one of the prefixes (a whole element, compared as written), as
     * logfile.ini SitePrefix[] asks.
     *
     * @param string[] $elements
     * @param string[] $prefixes
     * @return string[]
     */
    public static function stripPrefix( array $elements, array $prefixes )
    {
        foreach ( $prefixes as $prefix )
        {
            $parts = array_values( array_filter( explode( '/', trim( (string)$prefix, '/' ) ), 'strlen' ) );
            if ( $parts && array_slice( $elements, 0, count( $parts ) ) === $parts )
                return array_slice( $elements, count( $parts ) );
        }
        return $elements;
    }

    /**
     * The node a path shows for a site.
     *
     * @param expViewCountSite $site
     * @param string[] $elements see elements()
     * @return int|false
     */
    public function resolve( expViewCountSite $site, array $elements )
    {
        if ( !$elements )
            return $this->indexNode( $site );
        $node = $this->contentView( $elements );
        if ( $node !== null )
            return $node;
        return $this->alias( $site, implode( '/', $elements ) );
    }

    /**
     * The node of the site's IndexPage, false when it is not a node (user/login, a module view).
     *
     * @param expViewCountSite $site
     * @return int|false
     */
    public function indexNode( expViewCountSite $site )
    {
        if ( $site->indexNodeID !== null )
            return $site->indexNodeID ?: false;
        $elements = self::elements( '/' . ltrim( (string)$site->indexPage, '/' ) );
        if ( !$elements )
            return false;
        $node = $this->contentView( $elements );
        if ( $node !== null )
            return $node;
        return $this->alias( $site, implode( '/', $elements ) );
    }

    /**
     * The node of content/view/<mode>/<node>, false for another view mode, null when the path is not one.
     *
     * @param string[] $elements
     * @return int|false|null
     */
    public function contentView( array $elements )
    {
        if ( count( $elements ) < 4 || strtolower( $elements[0] ) !== 'content' || strtolower( $elements[1] ) !== 'view' )
            return null;
        if ( !ctype_digit( $elements[3] ) || (int)$elements[3] < 1 )
            return false;
        if ( $this->viewModes && !in_array( $elements[2], $this->viewModes, true ) )
            return false;
        return (int)$elements[3];
    }

    /**
     * The node of a URL alias of a site: with each path prefix in front first, then without.
     *
     * @param expViewCountSite $site
     * @param string $path elements joined with "/"
     * @return int|false
     */
    public function alias( expViewCountSite $site, $path )
    {
        $candidates = array();
        $first = strtolower( (string)strtok( $path, '/' ) );
        $excluded = false;
        foreach ( $site->pathPrefixExclude as $exclude )
        {
            if ( strtolower( trim( $exclude, '/' ) ) === $first )
                $excluded = true;
        }
        if ( !$excluded )
        {
            foreach ( $site->pathPrefixes as $prefix )
            {
                $prefix = trim( (string)$prefix, '/' );
                if ( $prefix !== '' )
                    $candidates[] = $prefix . '/' . $path;
            }
        }
        $candidates[] = $path;
        foreach ( $candidates as $candidate )
        {
            $node = $this->lookup( $site, $candidate );
            if ( $node )
                return $node;
        }
        return false;
    }

    /** @return int how many alias lookups reached the database (or the callable) */
    public function lookups()
    {
        return $this->lookups;
    }

    /** Puts the languages the process had back, when a site changed them. */
    public function restoreLanguages()
    {
        if ( $this->currentLanguages !== null && $this->currentLanguages !== '' )
            call_user_func( $this->languages, array() );
        $this->currentLanguages = null;
    }

    /**
     * @param expViewCountSite $site
     * @param string $path
     * @return int|false
     */
    private function lookup( expViewCountSite $site, $path )
    {
        $key = implode( ',', $site->languages );
        if ( isset( $this->cache[$key] ) && array_key_exists( $path, $this->cache[$key] ) )
            return $this->cache[$key][$path];
        if ( $this->currentLanguages !== $key )
        {
            // back to the process's own list first, so the stack of setPrioritizedLanguages() does not grow
            $this->restoreLanguages();
            if ( $key !== '' )
                call_user_func( $this->languages, $site->languages );
            $this->currentLanguages = $key;
        }
        ++$this->lookups;
        $node = call_user_func( $this->lookup, $path );
        $node = $node ? (int)$node : false;
        $this->cache[$key][$path] = $node;
        return $node;
    }
}
