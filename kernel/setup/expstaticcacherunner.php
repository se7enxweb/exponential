<?php
/**
 * File containing the expStaticCacheRunner class.
 *
 * Generates the static cache for one site at a time and reports every page as
 * it is written, so the administration interface shows a run instead of a
 * button that claims success.
 *
 * It does not use eZStaticCache::generateCache(). That expands
 * staticcache.ini.[CacheSettings].CachedURLArray against the url alias table of
 * the whole installation, which on anything but a single site install produces
 * the wrong set of urls: every alias in the database is offered to every
 * siteaccess, prefixed with that siteaccess's name. Generating Bold Agency
 * fetched /bold/fit-healthy/recipes, /bold/media/..., /bold/users/... and the
 * rest of a second site's tree, none of which are pages of Bold Agency, while
 * missing its real urls entirely - because PathPrefix means the alias
 * "bold-agency/about-us" is served at /bold/about-us, and the alias table does
 * not know that.
 *
 * Instead it crawls, using expPreloadRunner - the same component behind
 * setup/preload. The crawl starts at the site's own root, follows only links
 * the site itself emits, and therefore visits exactly the urls a visitor can
 * request, in the form the visitor requests them. Each page it fetches is
 * stored, so no page is requested twice.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'expStaticCacheRunner', false ) ) {
class expStaticCacheRunner
{
    /**
     * Called for every step, as ( $type, $message, $data ), where type is one
     * of phase, phase-item, ok, warn, error, info, done.
     *
     * @var callable
     */
    private $emit;

    /**
     * Siteaccesses to generate, empty for every cacheable one.
     *
     * @var array
     */
    private $siteAccesses;

    private $maxPages;
    private $maxDepth;

    /**
     * Remove what is already stored for a site before generating it.
     *
     * @var bool
     */
    private $purge;

    private $stored = 0;
    private $files = 0;
    private $skipped = 0;

    /**
     * @param callable $emit Progress sink.
     * @param array $options siteaccess (string or array), max_pages, max_depth, purge.
     */
    public function __construct( $emit, array $options = array() )
    {
        $this->emit = $emit;

        $siteAccesses = isset( $options['siteaccess'] ) ? $options['siteaccess'] : array();
        if ( !is_array( $siteAccesses ) )
            $siteAccesses = $siteAccesses === '' ? array() : array( $siteAccesses );
        $this->siteAccesses = array_values( array_filter( $siteAccesses, 'strlen' ) );

        $this->maxPages = isset( $options['max_pages'] ) ? max( 1, min( 20000, (int)$options['max_pages'] ) ) : 2500;
        $this->maxDepth = isset( $options['max_depth'] ) ? max( 0, min( 30, (int)$options['max_depth'] ) ) : 12;
        $this->purge    = isset( $options['purge'] ) ? (bool)$options['purge'] : true;
    }

    private function say( $type, $message, array $data = array() )
    {
        call_user_func( $this->emit, $type, $message, $data );
    }

    /**
     * The siteaccesses this installation can generate a static cache for.
     *
     * @return array Each entry has name, url and path, the last being the url
     *               prefix that selects the siteaccess on its host.
     */
    public static function availableSiteAccesses()
    {
        $list = array();
        foreach ( eZStaticCache::cacheableSiteAccessList() as $name )
        {
            $siteINI = eZSiteAccess::getIni( $name, 'site.ini' );
            $probe = new expPreloadRunner( function () {}, array( 'siteaccess' => $name ) );
            $list[] = array(
                'name' => $name,
                'url'  => $siteINI->hasVariable( 'SiteSettings', 'SiteURL' )
                        ? $siteINI->variable( 'SiteSettings', 'SiteURL' ) : '',
                'path' => $probe->basePath() === '' ? '/' : $probe->basePath() . '/' );
        }

        return $list;
    }

    /**
     * The directory generated pages are written to, as configured.
     *
     * @return string
     */
    public static function storageDirectory()
    {
        $ini = eZINI::instance( 'staticcache.ini' );
        return eZStaticCache::resolveStorageDirectory( $ini->variable( 'CacheSettings', 'StaticStorageDir' ) );
    }

    /**
     * Instantiates the configured static cache handler.
     *
     * @return ezpStaticCache
     */
    private function handler()
    {
        $options = new ezpExtensionOptions( array( 'iniFile'     => 'site.ini',
                                                   'iniSection'  => 'ContentSettings',
                                                   'iniVariable' => 'StaticCacheHandler' ) );
        $handler = eZExtension::getHandlerClass( $options );
        if ( !$handler )
            throw new Exception( 'site.ini [ContentSettings] StaticCacheHandler does not name a usable class.' );

        return $handler;
    }

    /**
     * Generates the cache and reports every page.
     */
    public function run()
    {
        $staticCacheINI = eZINI::instance( 'staticcache.ini' );
        $siteINI = eZINI::instance( 'site.ini' );

        $targets = $this->siteAccesses ? $this->siteAccesses : eZStaticCache::cacheableSiteAccessList();
        if ( !$targets )
        {
            $this->say( 'error', 'No site can be cached: every siteaccess either requires a login or has no SiteURL.' );
            $this->say( 'done', 'Nothing to do.' );
            return;
        }

        $storageDir = self::storageDirectory();
        $started = microtime( true );

        $this->say( 'phase', 'Configuration' );
        $this->say( 'phase-item', 'Writing to:  ' . $storageDir );
        $this->say( 'phase-item', 'Sites:       ' . implode( ', ', $targets ) );
        $this->say( 'phase-item', 'Limits:      ' . $this->maxPages . ' pages, link depth ' . $this->maxDepth );

        // Generating pages is only half of a static cache. Without this the
        // pages are never refreshed when an editor publishes, so the site would
        // keep serving whatever was true when the button was pressed.
        if ( $siteINI->variable( 'ContentSettings', 'StaticCache' ) !== 'enabled' )
            $this->say( 'warn', 'site.ini [ContentSettings] StaticCache is not enabled, so publishing will not refresh these pages.' );

        if ( $staticCacheINI->variable( 'CacheSettings', 'HostName' ) )
            $this->say( 'warn', 'staticcache.ini [CacheSettings] HostName is set to "'
                        . $staticCacheINI->variable( 'CacheSettings', 'HostName' )
                        . '"; it is deprecated and should be empty so each site is fetched from its own SiteURL.' );

        foreach ( $targets as $siteAccess )
            $this->runSiteAccess( $siteAccess );

        $elapsed = round( microtime( true ) - $started, 1 );
        $summary = $this->stored . ' page' . ( $this->stored === 1 ? '' : 's' )
                 . ' stored as ' . $this->files . ' file' . ( $this->files === 1 ? '' : 's' )
                 . ' under ' . $storageDir . ' in ' . $elapsed . 's';
        if ( $this->skipped )
            $summary .= ', ' . $this->skipped . ' skipped';

        $this->say( 'done', $summary, array( 'stored' => $this->stored, 'files' => $this->files,
                                             'skipped' => $this->skipped, 'seconds' => $elapsed ) );
    }

    /**
     * Generates one site.
     */
    private function runSiteAccess( $siteAccess )
    {
        $siteINI = eZSiteAccess::getIni( $siteAccess, 'site.ini' );
        $siteURL = $siteINI->hasVariable( 'SiteSettings', 'SiteURL' ) ? $siteINI->variable( 'SiteSettings', 'SiteURL' ) : '';

        $handler = $this->handler();
        if ( !method_exists( $handler, 'cacheFilePathsForURL' ) )
        {
            $this->say( 'error', get_class( $handler ) . ' cannot be driven from a crawl; nothing generated for ' . $siteAccess . '.' );
            return;
        }

        // The crawl reports every page it fetches; the store callback below
        // reports every page actually written, which is the useful line. Only
        // the crawl's complaints are relayed, so nothing is said twice.
        $runner = $this;
        $crawler = new expPreloadRunner(
            function ( $type, $message, array $data = array() ) use ( $runner )
            {
                if ( $type === 'warn' || $type === 'error' )
                    $runner->report( $type, $message );
            },
            array( 'siteaccess' => $siteAccess,
                   'max_pages'  => $this->maxPages,
                   'max_depth'  => $this->maxDepth ) );

        $base = $crawler->baseUrl();
        if ( $base === false )
        {
            $this->say( 'error', $siteAccess . ' has no SiteSettings/SiteURL, so there is nothing to fetch.' );
            return;
        }

        $basePath = $crawler->basePath();
        $this->say( 'phase', 'Generating ' . $siteAccess . '  ' . $base . ( $basePath === '' ? '/' : $basePath . '/' ) );

        if ( $this->purge )
            $this->purgeSiteAccess( $handler, $siteAccess );

        $maxCacheDepth = (int)eZINI::instance( 'staticcache.ini' )->variable( 'CacheSettings', 'MaxCacheDepth' );

        $stored = 0;
        $files = 0;
        $skipped = 0;

        $crawler->setStoreCallback(
            function ( $url, $path, $body ) use ( $runner, $handler, $siteAccess, $basePath, $maxCacheDepth, &$stored, &$files, &$skipped )
            {
                // The url as the siteaccess sees it: the prefix that selected
                // the siteaccess is not part of its own urls, and is added back
                // by the storage layout.
                $relative = $path;
                if ( $basePath !== '' && strpos( $relative, $basePath ) === 0 )
                    $relative = (string)substr( $relative, strlen( $basePath ) );
                $relative = '/' . ltrim( $relative, '/' );
                $relative = rtrim( $relative, '/' );

                // The same bound the publish time invalidation uses, so a page
                // is never stored that a publish would not refresh.
                if ( $maxCacheDepth > 0 && substr_count( $relative . '/', '/' ) > $maxCacheDepth )
                {
                    $skipped++;
                    $runner->report( 'info', 'too deep for MaxCacheDepth, not stored: ' . $path );
                    return;
                }

                $written = 0;
                foreach ( $handler->cacheFilePathsForURL( $siteAccess, $relative ) as $file )
                {
                    eZStaticCache::storeCachedFile( $file, $body );
                    $written++;
                }

                $stored++;
                $files += $written;
                $runner->report( 'ok', sprintf( '%-52s %s', $path, $runner->shortenBytes( strlen( $body ) ) ) );
            } );

        $crawler->run();

        $counts = $crawler->counts();
        $this->stored += $stored;
        $this->files += $files;
        $this->skipped += $skipped;

        if ( $stored === 0 )
            $this->say( 'warn', 'Nothing was stored for ' . $siteAccess . '. The site root did not respond, or served no html.' );
        else
            $this->say( 'info', sprintf( '%s: %d pages stored as %d files; %d links broken, %d access denied.',
                                         $siteAccess, $stored, $files, $counts['broken'], $counts['denied'] ) );
    }

    /**
     * Removes what is already stored for a site.
     *
     * A regeneration that only overwrites leaves the pages of everything that
     * has since been unpublished or renamed in place, and the web server would
     * go on serving them.
     */
    private function purgeSiteAccess( $handler, $siteAccess )
    {
        if ( !method_exists( $handler, 'cacheDirectoriesForSiteAccess' ) )
            return;

        foreach ( $handler->cacheDirectoriesForSiteAccess( $siteAccess ) as $dir )
        {
            if ( !is_dir( $dir ) )
                continue;
            eZDir::recursiveDelete( $dir );
            $this->say( 'phase-item', 'Removed ' . $dir );
        }
    }

    /**
     * Public so the store closure can reach it on php 5.3, where a closure has
     * no $this.
     */
    public function report( $type, $message )
    {
        $this->say( $type, $message );
    }

    /** Public for the same reason. */
    public function shortenBytes( $bytes )
    {
        if ( $bytes < 1024 )
            return $bytes . 'B';
        if ( $bytes < 1048576 )
            return round( $bytes / 1024, 1 ) . 'K';
        return round( $bytes / 1048576, 1 ) . 'M';
    }
}
}

require_once 'kernel/setup/exppreloadrunner.php';


?>
