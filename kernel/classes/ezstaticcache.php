<?php
/**
 * File containing the eZStaticCache class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * The eZStaticCache class manages the static cache system.
 *
 * This class can be used to generate static cache files usable
 * by the static cache system.
 *
 * Generating static cache is done by instantiating the class and then
 * calling generateCache(). For example:
 *
 * <code>
 * $staticCache = new eZStaticCache();
 * $staticCache->generateCache();
 * </code>
 *
 * To generate the URLs that must always be updated call generateAlwaysUpdatedCache()
 *
 * @package kernel
 */
class eZStaticCache implements ezpStaticCache
{
    public $staticStorageDir;
    /**
     * User-Agent string
     */
    const USER_AGENT = 'eZ Publish static cache generator';

    private static $actionList = array();

    /**
     * The name of the host to fetch HTML data from.
     *
     * @deprecated deprecated since version 4.4, site.ini.[SiteSettings].SiteURL is used instead
     * @var string
     */
    private $hostName;

    /**
     * The base path for the directory where static files are placed.
     *
     * @var string
     */
    private $staticStorage;

    /**
     * The maximum depth of URLs that will be cached.
     *
     * @var int
     */
    private $maxCacheDepth;

    /**
     * Array of URLs to cache.
     *
     * @var array(int=>string)
     */
    private $cachedURLArray = array();

    /**
     * An array with siteaccesses names that will be cached.
     *
     * @var array(int=>string)
     */
    private $cachedSiteAccesses = array();

    /**
     * An array with URLs that is to always be updated.
     *
     * @var array(int=>string)
     */
    private $alwaysUpdate;

    /**
     * The protocol used when fetching a page to store, http or https.
     *
     * @var string
     */
    private $sourceProtocol;

    /**
     * Optional callable notified for every stored page, used to report progress
     * while a generation run is in flight. Receives ( $url, $file, $ok ).
     *
     * @var callable|null
     */
    private $progressCallback = null;

    /**
     *  Initialises the static cache object with settings from staticcache.ini.
     */
    public function __construct()
    {
        $ini = eZINI::instance( 'staticcache.ini');
        $this->hostName = $ini->variable( 'CacheSettings', 'HostName' );
        $this->staticStorageDir = self::resolveStorageDirectory( $ini->variable( 'CacheSettings', 'StaticStorageDir' ) );
        $this->maxCacheDepth = $ini->variable( 'CacheSettings', 'MaxCacheDepth' );
        $this->cachedURLArray = $ini->variable( 'CacheSettings', 'CachedURLArray' );
        $this->cachedSiteAccesses = (array)$ini->variable( 'CacheSettings', 'CachedSiteAccesses' );
        // An empty list used to mean "cache nothing", silently: storeCache()
        // builds its target directories by iterating this array, so with no
        // entries it queued no work, wrote no file and reported no error. The
        // shipped default is empty, which made the feature dead on arrival on
        // every installation that had not been hand configured. Falling back to
        // the siteaccesses this installation actually serves makes the default
        // behaviour the useful one; naming siteaccesses explicitly still wins.
        if ( !$this->cachedSiteAccesses )
            $this->cachedSiteAccesses = self::cacheableSiteAccessList();
        $this->alwaysUpdate = $ini->variable( 'CacheSettings', 'AlwaysUpdateArray' );
        $this->sourceProtocol = $ini->hasVariable( 'CacheSettings', 'SourceProtocol' )
                              ? strtolower( trim( $ini->variable( 'CacheSettings', 'SourceProtocol' ) ) ) : 'http';
        if ( $this->sourceProtocol !== 'https' )
            $this->sourceProtocol = 'http';
    }

    /**
     * Resolves the configured StaticStorageDir to a path below the var
     * directory of the current siteaccess.
     *
     * The setting ships as the bare word "static", which produced ./static in
     * the installation root - outside var, outside everything the installer
     * creates and everything the cache clearing tools know about. Generated
     * pages are cache like any other, so they belong in the var directory next
     * to var/<site>/cache and var/<site>/storage, which is what this returns:
     * "static" becomes "var/<site>/static".
     *
     * A path that is already absolute, or already expressed below var, is used
     * exactly as given so an existing deployment can keep its layout.
     *
     * @param string $dir The configured directory.
     * @return string The directory to store cache files in.
     */
    public static function resolveStorageDirectory( $dir )
    {
        $dir = trim( (string)$dir );
        if ( $dir === '' )
            $dir = 'static';

        // Absolute, posix or windows: the administrator has been explicit.
        if ( $dir[0] === '/' || $dir[0] === '\\' || preg_match( '#^[a-zA-Z]:[\\\\/]#', $dir ) )
            return rtrim( $dir, '/' );

        $dir = trim( $dir, '/' );
        $varDir = trim( eZSys::varDirectory(), '/' );
        if ( $varDir === '' )
            $varDir = 'var';

        if ( $dir === $varDir || strpos( $dir, $varDir . '/' ) === 0 || strpos( $dir, 'var/' ) === 0 )
            return $dir;

        return $varDir . '/' . $dir;
    }

    /**
     * The siteaccesses of this installation whose pages can be served from a
     * static file.
     *
     * Used when staticcache.ini.[CacheSettings].CachedSiteAccesses names none.
     * An interface that requires a login is skipped: its pages are per user, so
     * a shared static copy of them would be both useless and a disclosure. A
     * siteaccess whose SiteURL is still the shipped placeholder is skipped too,
     * because there is no host to fetch the page from.
     *
     * @return array An array of siteaccess names.
     */
    public static function cacheableSiteAccessList()
    {
        $ini = eZINI::instance();
        $list = array();
        foreach ( array( 'RelatedSiteAccessList', 'AvailableSiteAccessList' ) as $variable )
        {
            if ( $ini->hasVariable( 'SiteAccessSettings', $variable ) )
                $list = array_merge( $list, (array)$ini->variable( 'SiteAccessSettings', $variable ) );
            if ( $list )
                break;
        }

        $cacheable = array();
        foreach ( array_unique( $list ) as $name )
        {
            if ( !is_string( $name ) || $name === '' )
                continue;

            $siteINI = eZSiteAccess::getIni( $name, 'site.ini' );

            if ( $siteINI->hasVariable( 'SiteAccessSettings', 'RequireUserLogin' ) &&
                 $siteINI->variable( 'SiteAccessSettings', 'RequireUserLogin' ) === 'true' )
                continue;

            $siteURL = $siteINI->hasVariable( 'SiteSettings', 'SiteURL' )
                     ? trim( $siteINI->variable( 'SiteSettings', 'SiteURL' ) ) : '';
            if ( $siteURL === '' || $siteURL === 'example.com' )
                continue;

            $cacheable[] = $name;
        }

        return $cacheable;
    }

    /**
     * Limits this run to the given siteaccesses.
     *
     * Lets a caller generate one site at a time - the administration interface
     * offers the choice - without writing the restriction into the settings.
     *
     * @param array $siteAccesses
     */
    public function setCachedSiteAccesses( array $siteAccesses )
    {
        $this->cachedSiteAccesses = array_values( array_filter( $siteAccesses, 'strlen' ) );
    }

    /**
     * Registers a callable notified as each page is stored.
     *
     * Only used when generation is not delayed, which is where the fetching and
     * writing actually happens. The callable receives the source url, the
     * destination file and whether the page could be fetched.
     *
     * @param callable|null $callback
     */
    public function setProgressCallback( $callback )
    {
        $this->progressCallback = $callback;
    }

    /**
     * Getter method for {@link eZStaticCache::$hostName}
     *
     * @deprecated deprecated since version 4.4
     * @return string The currently configured host-name.
     */
    public function hostName()
    {
        return $this->hostName;
    }

    /**
     * Getter method for {@link eZStaticCache::$staticStorageDir}
     *
     * @return string The currently configured storage directory for the static cache.
     */
    public function storageDirectory()
    {
        return $this->staticStorageDir;
    }

    /**
     * Getter method for {@link eZStaticCache::$maxCacheDepth}
     *
     * @return int The maximum depth in the url which will be cached.
     */
    public function maxCacheDepth()
    {
        return $this->maxCacheDepth;
    }

    /**
     * Getter method for {@link eZStaticCache::$cachedSiteAccesses}
     *
     * @return array An array with site-access names that should be cached.
     */
    public function cachedSiteAccesses()
    {
        return $this->cachedSiteAccesses;
    }

    /**
     * Getter method for {@link eZStaticCache::$cachedURLArray}
     *
     * @return array An array with URLs that is to be cached statically, the URLs may contain wildcards.
     */
    public function cachedURLArray()
    {
        return $this->cachedURLArray;
    }

    /**
     * Getter method for {@link eZStaticCache::$alwaysUpdate}
     *
     * These URLs are configured with AlwaysUpdateArray in staticcache.ini.
     *
     * @see eZStaticCache::generateAlwaysUpdatedCache()
     * @return array An array with URLs that is to always be updated.
     */
    function alwaysUpdateURLArray()
    {
        return $this->alwaysUpdate;
    }

    /**
     * Generates the caches for all URLs that must always be generated.
     *
     * @param bool $quiet If true then the function will not output anything.
     * @param eZCLI|false $cli The eZCLI object or false if no output can be done.
     * @param bool $delay
     */
    public function generateAlwaysUpdatedCache( $quiet = false, $cli = false, $delay = true )
    {
        foreach ( $this->alwaysUpdate as $uri )
        {
            if ( !$quiet and $cli )
                $cli->output( "caching: $uri ", false );
            $this->storeCache( $uri, $this->staticStorageDir, array(), false, $delay );
            if ( !$quiet and $cli )
                $cli->output( "done" );
        }
    }

    /**
     * Generates caches for all the urls of nodes in $nodeList.
     *
     * The associative array must have on of these entries:
     * - node_id - ID of the node
     * - path_identification_string - The path_identification_string from the node table, is used to fetch the node ID if node_id is missing.
     *
     * @param array $nodeList An array with node entries, each entry is either the node ID or an associative array.
     */
    public function generateNodeListCache( $nodeList )
    {
        $db = eZDB::instance();

        foreach ( $nodeList as $uri )
        {
            if ( is_array( $uri ) )
            {
                if ( !isset( $uri['node_id'] ) )
                {
                    eZDebug::writeError( "node_id is not set for uri entry " . var_export( $uri ) . ", will need to perform extra query to get node_id" );
                    $node = eZContentObjectTreeNode::fetchByURLPath( $uri['path_identification_string'] );
                    $nodeID = (int)$node->attribute( 'node_id' );
                }
                else
                {
                    $nodeID = (int)$uri['node_id'];
                }
            }
            else
            {
                $nodeID = (int)$uri;
            }
            $elements = eZURLAliasML::fetchByAction( 'eznode', $nodeID, true, true, true );
            foreach ( $elements as $element )
            {
                $path = $element->getPath();
                $this->cacheURL( '/' . $path );
            }
        }
    }

    /**
     * Generates the static cache from the configured INI settings.
     *
     * @param bool $force If true then it will create all static caches even if it is not outdated.
     * @param bool $quiet If true then the function will not output anything.
     * @param eZCLI|false $cli The eZCLI object or false if no output can be done.
     * @param bool $delay
     */
    public function generateCache( $force = false, $quiet = false, $cli = false, $delay = true )
    {
        $staticURLArray = $this->cachedURLArray();
        $db = eZDB::instance();
        $configSettingCount = count( $staticURLArray );
        $currentSetting = 0;

        // This contains parent elements which must checked to find new urls and put them in $generateList
        // Each entry contains:
        // - url - Url of parent
        // - glob - A glob string to filter direct children based on name
        // - org_url - The original url which was requested
        // - parent_id - The element ID of the parent (optional)
        // The parent_id will be used to quickly fetch the children, if not it will use the url
        $parentList = array();
        // A list of urls which must generated, each entry is a string with the url
        $generateList = array();
        foreach ( $staticURLArray as $url )
        {
            $currentSetting++;
            if ( strpos( $url, '*') === false )
            {
                $generateList[] = $url;
            }
            else
            {
                $queryURL = ltrim( str_replace( '*', '', $url ), '/' );
                $dir = dirname( $queryURL );
                if ( $dir == '.' )
                    $dir = '';
                $glob = basename( $queryURL );
                $parentList[] = array( 'url' => $dir,
                                       'glob' => $glob,
                                       'org_url' => $url );
            }
        }

        // As long as we have urls to generate or parents to check we loop
        while ( count( $generateList ) > 0 || count( $parentList ) > 0 )
        {
            // First generate single urls
            foreach ( $generateList as $generateURL )
            {
                if ( !$quiet and $cli )
                    $cli->output( "caching: $generateURL ", false );
                $this->cacheURL( $generateURL, false, !$force, $delay );
                if ( !$quiet and $cli )
                    $cli->output( "done" );
            }
            $generateList = array();

            // Then check for more data
            $newParentList = array();
            foreach ( $parentList as $parentURL )
            {
                if ( isset( $parentURL['parent_id'] ) )
                {
                    $elements = eZURLAliasML::fetchByParentID( $parentURL['parent_id'], true, true, false );
                    foreach ( $elements as $element )
                    {
                        $path = '/' . $element->getPath();
                        $generateList[] = $path;
                        $newParentList[] = array( 'parent_id' => $element->attribute( 'id' ) );
                    }
                }
                else
                {
                    if ( !$quiet and $cli and $parentURL['glob'] )
                        $cli->output( "wildcard cache: " . $parentURL['url'] . '/' . $parentURL['glob'] . "*" );
                    $elements = eZURLAliasML::fetchByPath( $parentURL['url'], $parentURL['glob'] );
                    foreach ( $elements as $element )
                    {
                        $path = '/' . $element->getPath();
                        $generateList[] = $path;
                        $newParentList[] = array( 'parent_id' => $element->attribute( 'id' ) );
                    }
                }
            }
            $parentList = $newParentList;
        }
    }

    /**
     * Generates the caches for the url $url using the currently configured storageDirectory().
     *
     * @param string $url The URL to cache, e.g /news
     * @param int|false $nodeID The ID of the node to cache, if supplied it will also cache content/view/full/xxx.
     * @param bool $skipExisting If true it will not unlink existing cache files.
     * @return bool
     */
    public function cacheURL( $url, $nodeID = false, $skipExisting = false, $delay = true )
    {
        // Check if URL should be cached
        if ( substr_count( $url, "/") >= $this->maxCacheDepth )
            return false;

        $doCacheURL = false;
        foreach ( $this->cachedURLArray as $cacheURL )
        {
            if ( $url == $cacheURL )
            {
                $doCacheURL = true;
                break;
            }
            else if ( strpos( $cacheURL, '*') !== false )
            {
                if ( strpos( $url, str_replace( '*', '', $cacheURL ) ) === 0 )
                {
                    $doCacheURL = true;
                    break;
                }
            }
        }

        if ( $doCacheURL == false )
        {
            return false;
        }

        $this->storeCache( $url, $this->staticStorageDir, $nodeID ? array( "/content/view/full/$nodeID" ) : array(), $skipExisting, $delay );

        return true;
    }

    /**
     * Stores the static cache for $url and hostname defined in site.ini.[SiteSettings].SiteURL for cached siteaccess
     * by fetching the web page using {@link eZHTTPTool::getDataByURL()} and storing the fetched HTML data.
     *
     * @param string $url The URL to cache, e.g /news
     * @param string $staticStorageDir The base directory for storing cache files.
     * @param array $alternativeStaticLocations
     * @param bool $skipUnlink If true it will not unlink existing cache files.
     * @param bool $delay
     */
    private function storeCache( $url, $staticStorageDir, $alternativeStaticLocations = array(), $skipUnlink = false, $delay = true )
    {
        // The url as given, before any siteaccess specific correction: the loop
        // below runs once per storage location and each has its own.
        $siteAccessURL = $url;
        $dirs = array();

        foreach ( $this->cachedSiteAccesses as $cachedSiteAccess )
        {
            $dirs[] = $this->buildCacheDirPath( $cachedSiteAccess );
        }

        foreach ( $dirs as $dirParts )
        {
            foreach ( $dirParts as $dirPart )
            {
                $dir = $dirPart['dir'];
                $siteURL = $dirPart['site_url'];
                $urlPrefix = isset( $dirPart['url_prefix'] ) ? $dirPart['url_prefix'] : '';

                // The url this siteaccess actually serves the page at.
                $url = self::stripPathPrefix( $siteAccessURL, $dirPart );

                $cacheFiles = array();

                $cacheFiles[] = $this->buildCacheFilename( $staticStorageDir, $dir . $url );
                foreach ( $alternativeStaticLocations as $location )
                {
                    $cacheFiles[] = $this->buildCacheFilename( $staticStorageDir, $dir . $location );
                }

                // Store new content
                $content = false;
                foreach ( $cacheFiles as $file )
                {
                    if ( !$skipUnlink || !file_exists( $file ) )
                    {
                        // The page is fetched over http from the host that
                        // serves this siteaccess, at the url prefix that
                        // selects it.
                        //
                        // Both halves used to be wrong. The prefix was taken
                        // from $dir, the storage directory, which for a host
                        // matched siteaccess is the host name - producing
                        // http://host/host/url. And when the deprecated
                        // HostName was not set the prefix was dropped
                        // altogether, so every uri matched siteaccess fetched
                        // the default site's page and stored it under its own
                        // name: three siteaccesses, three directories, one
                        // page. buildCacheDirPart() now records the host and
                        // the prefix that belong together, and this uses them.
                        $sourceHost = $this->hostName ? $this->hostName : $dirPart['host'];
                        if ( !$sourceHost )
                            $sourceHost = $siteURL;

                        $fileName = "{$this->sourceProtocol}://{$sourceHost}{$urlPrefix}{$url}";

                        if ( $delay )
                        {
                            $this->addAction( 'store', array( $file, $fileName ) );
                        }
                        else
                        {
                            // Generate content, if required
                            if ( $content === false )
                            {
                                if ( eZHTTPTool::getDataByURL( $fileName, true, eZStaticCache::USER_AGENT ) )
                                    $content = eZHTTPTool::getDataByURL( $fileName, false, eZStaticCache::USER_AGENT );
                            }
                            if ( $content === false )
                            {
                                eZDebug::writeError( "Could not grab content (from $fileName), is the hostname correct and Apache running?", 'Static Cache' );
                            }
                            else
                            {
                                eZStaticCache::storeCachedFile( $file, $content );
                            }

                            if ( $this->progressCallback )
                                call_user_func( $this->progressCallback, $fileName, $file, $content !== false );
                        }
                    }
                }
            }
        }
    }

    /**
     * Removes a siteaccess's PathPrefix from a url derived from the url alias
     * table, which stores paths from the content root.
     *
     * PathPrefixExclude names the first segments that keep their full path,
     * Media and Users by default, and those are left alone.
     *
     * @param string $url
     * @param array $dirPart A dir part from buildCacheDirPart().
     * @return string
     */
    private static function stripPathPrefix( $url, array $dirPart )
    {
        $prefix = isset( $dirPart['path_prefix'] ) ? $dirPart['path_prefix'] : '';
        if ( $prefix === '' || $url === '' )
            return $url;

        $first = strtolower( (string)strtok( ltrim( $url, '/' ), '/' ) );
        foreach ( (array)$dirPart['path_prefix_exclude'] as $exclude )
        {
            if ( strtolower( trim( (string)$exclude, '/' ) ) === $first )
                return $url;
        }

        if ( strcasecmp( ltrim( $url, '/' ), $prefix ) === 0 )
            return '';

        if ( strncasecmp( ltrim( $url, '/' ), $prefix . '/', strlen( $prefix ) + 1 ) === 0 )
            return '/' . substr( ltrim( $url, '/' ), strlen( $prefix ) + 1 );

        return $url;
    }

    /**
     * The files a page is stored as for one siteaccess.
     *
     * Public so a generator that already holds the rendered page can write it
     * to exactly the same places storeCache() would, and so the files a publish
     * invalidates and the files a generation writes cannot drift apart.
     *
     * @param string $siteAccess
     * @param string $url The url relative to the siteaccess, e.g. /about-us
     * @return array An array of file paths.
     */
    public function cacheFilePathsForURL( $siteAccess, $url )
    {
        $files = array();
        foreach ( $this->buildCacheDirPath( $siteAccess ) as $dirPart )
            $files[] = $this->buildCacheFilename( $this->staticStorageDir,
                                                  $dirPart['dir'] . self::stripPathPrefix( $url, $dirPart ) );

        return array_values( array_unique( $files ) );
    }

    /**
     * The directory a siteaccess's pages are stored under, for removing them
     * before a full regeneration.
     *
     * @param string $siteAccess
     * @return array An array of directory paths.
     */
    public function cacheDirectoriesForSiteAccess( $siteAccess )
    {
        $dirs = array();
        foreach ( $this->buildCacheDirPath( $siteAccess ) as $dirPart )
        {
            $dir = preg_replace( '#//+#', '/', $this->staticStorageDir . $dirPart['dir'] );
            if ( $dir !== '' && rtrim( $dir, '/' ) !== rtrim( $this->staticStorageDir, '/' ) )
                $dirs[] = rtrim( $dir, '/' );
        }

        return array_values( array_unique( $dirs ) );
    }

    /**
     * Generates a full path to the cache file (index.html) based on the input parameters.
     *
     * @param string $staticStorageDir The storage for cache files.
     * @param string $url The URL for the current item, e.g /news
     * @return string The full path to the cache file (index.html).
     */
    private function buildCacheFilename( $staticStorageDir, $url )
    {
        $file = "{$staticStorageDir}{$url}/index.html";
        $file = preg_replace( '#//+#', '/', $file );
        return $file;
    }

    /**
     * Generates a cache directory parts including path, siteaccess name, site URL
     * depending on the match order type.
     *
     * @param string $siteAccess
     * @return array
     */
    private function buildCacheDirPath( $siteAccess )
    {
        $dirParts = array();

        $ini = eZINI::instance();

        $matchOderArray = $ini->variableArray( 'SiteAccessSettings', 'MatchOrder' );

        foreach ( $matchOderArray as $matchOrderItem )
        {
            switch ( $matchOrderItem )
            {
                case 'host_uri':
                    foreach ( $ini->variable( 'SiteAccessSettings', 'HostUriMatchMapItems' ) as $hostUriMatchMapItem )
                    {
                        $parts = explode( ';', $hostUriMatchMapItem );

                        if ( $parts[2] === $siteAccess  )
                        {
                            $dirParts[] = $this->buildCacheDirPart( ( $parts[0] ? '/' . $parts[0] : '' ) .
                                                                    ( $parts[1] ? '/' . $parts[1] : '' ), $siteAccess,
                                                                    $parts[0], $parts[1] ? '/' . $parts[1] : '' );
                        }
                    }
                    break;
                case 'host':
                    foreach ( $ini->variable( 'SiteAccessSettings', 'HostMatchMapItems' ) as $hostMatchMapItem )
                    {
                        $parts = explode( ';', $hostMatchMapItem );

                        if ( $parts[1] === $siteAccess  )
                        {
                            // Matched on the host alone, so the page lives at
                            // the root of that host and takes no prefix.
                            $dirParts[] = $this->buildCacheDirPart( ( $parts[0] ? '/' . $parts[0] : '' ), $siteAccess,
                                                                    $parts[0], '' );
                        }
                    }
                    break;
                default:
                    // Matched on the first url segment, which is the siteaccess
                    // name, so that segment is part of every url of this site.
                    $dirParts[] = $this->buildCacheDirPart( '/' . $siteAccess, $siteAccess, false, '/' . $siteAccess );
                    break;
            }
        }

        return $dirParts;
    }

    /**
     * A helper method used to create directory parts array
     *
     * @param string $dir
     * @param string $siteAccess
     * @return array
     */
    private function buildCacheDirPart( $dir, $siteAccess, $host = false, $urlPrefix = '' )
    {
        $siteURL = eZSiteAccess::getIni( $siteAccess, 'site.ini' )->variable( 'SiteSettings', 'SiteURL' );

        $siteAccessINI = eZSiteAccess::getIni( $siteAccess, 'site.ini' );

        return array( 'dir' => $dir,
                      'access_name' => $siteAccess,
                      'site_url' => $siteURL,
                      // A siteaccess rooted below the content root serves
                      // "bold-agency/about-us" at /about-us. The url alias
                      // table does not know that, so anything derived from it
                      // has to be corrected before it is fetched or stored.
                      'path_prefix' => $siteAccessINI->hasVariable( 'SiteAccessSettings', 'PathPrefix' )
                                     ? trim( (string)$siteAccessINI->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' ) : '',
                      'path_prefix_exclude' => $siteAccessINI->hasVariable( 'SiteAccessSettings', 'PathPrefixExclude' )
                                     ? (array)$siteAccessINI->variable( 'SiteAccessSettings', 'PathPrefixExclude' ) : array(),
                      // The host to fetch this site's pages from, and the url
                      // prefix that selects the siteaccess on it. Kept apart
                      // from 'dir' because the storage layout and the public
                      // url are not the same thing: a host matched siteaccess
                      // is stored under the host name but served from the root.
                      'host' => $host ? $host : $siteURL,
                      'url_prefix' => $urlPrefix );
    }

    /**
     * Stores the cache file $file with contents $content.
     * Takes care of setting proper permissions on the new file.
     *
     * @param string $file
     * @param string $content
     */
    static function storeCachedFile( $file, $content )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
        {
            eZDir::mkdir( $dir, false, true );
        }

        $oldumask = umask( 0 );

        $tmpFileName = $file . '.' . md5( $file. uniqid( "ezp". getmypid(), true ) );

        // Remove files, this might be necessary for Windows
        @unlink( $tmpFileName );

        // Write the new cache file with the data attached
        $fp = fopen( $tmpFileName, 'w' );
        if ( $fp )
        {
            $comment = ( eZINI::instance( 'staticcache.ini' )->variable( 'CacheSettings', 'AppendGeneratedTime' ) === 'true' ) ? "<!-- Generated: " . date( 'Y-m-d H:i:s' ). " -->\n\n" : null;

            fwrite( $fp, $content . $comment );
            fclose( $fp );
            eZFile::rename( $tmpFileName, $file, false, eZFile::CLEAN_ON_FAILURE | eZFile::APPEND_DEBUG_ON_FAILURE );

            $perm = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
            chmod( $file, octdec( $perm ) );
        }

        umask( $oldumask );
    }

    /**
     * Removes the static cache file (index.html) and its directory if it exists.
     * The directory path is based upon the URL $url and the configured static storage dir.
     *
     * @param string $url The URL for the current item, e.g /news
     */
    function removeURL( $url )
    {
        $dir = eZDir::path( array( $this->staticStorageDir, $url ) );

        @unlink( $dir . "/index.html" );
        @rmdir( $dir );
    }

    /**
     * This function adds an action to the list that is used at the end of the
     * request to remove and regenerate static cache files.
     *
     * @param string $action
     * @param array $parameters
     */
    private function addAction( $action, $parameters )
    {
        self::$actionList[] = array( $action, $parameters );
    }

    /**
     * This function goes over the list of recorded actions and excecutes them.
     *
     * @return int The number of pages stored, or handed to the cronjob queue.
     *             The administration interface reported success whether or not
     *             anything had happened, because there was nothing to report.
     */
    static function executeActions()
    {
        $storedCount = 0;

        if ( empty( self::$actionList ) )
        {
            return $storedCount;
        }

        $fileContentCache = array();
        $doneDestList = array();

        $ini = eZINI::instance( 'staticcache.ini');
        $clearByCronjob = ( $ini->variable( 'CacheSettings', 'CronjobCacheClear' ) == 'enabled' );

        if ( $clearByCronjob )
        {
            $db = eZDB::instance();
        }

        foreach ( self::$actionList as $action )
        {
            list( $action, $parameters ) = $action;

            switch( $action ) {
                case 'store':
                    list( $destination, $source ) = $parameters;

                    if ( isset( $doneDestList[$destination] ) )
                        continue 2;

                    if ( $clearByCronjob )
                    {
                        $param = $db->escapeString( $destination . ',' . $source );
                        $db->query( 'INSERT INTO ezpending_actions( action, param ) VALUES ( \'static_store\', \''. $param . '\' )' );
                        $doneDestList[$destination] = 1;
                        $storedCount++;
                    }
                    else
                    {
                        if ( !isset( $fileContentCache[$source] ) )
                        {
                            if ( eZHTTPTool::getDataByURL( $source, true, eZStaticCache::USER_AGENT ) )
                                $fileContentCache[$source] = eZHTTPTool::getDataByURL( $source, false, eZStaticCache::USER_AGENT );
                            else
                                $fileContentCache[$source] = false;
                        }
                        if ( $fileContentCache[$source] === false )
                        {
                            eZDebug::writeError( "Could not grab content (from $source), is the hostname correct and Apache running?", 'Static Cache' );
                        }
                        else
                        {
                            eZStaticCache::storeCachedFile( $destination, $fileContentCache[$source] );
                            $doneDestList[$destination] = 1;
                            $storedCount++;
                        }
                    }
                    break;
            }
        }
        self::$actionList = array();

        return $storedCount;
    }
}

?>
