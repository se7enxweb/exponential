<?php
/**
 * File containing the expCacheManager class.
 *
 * Everything Setup > Cache (kernel/setup/cache.php) can do, as functions that
 * return what happened instead of setting template variables, so the
 * administration page and the command line (bin/php/cache.php, which the
 * console runs as exp:cache) cannot drift apart: both call these.
 *
 * Every action returns a result array:
 *
 *   ok       bool    whether it did what it was asked
 *   message  string  one sentence for a person
 *   items    array   what was, or with $dryRun would be, cleared: one entry
 *                    per cache, directory, file or tag
 *   dry_run  bool    true when nothing was changed
 *   data     array   figures for a caller that is not a person
 *
 * A dry run never writes, deletes, fetches or touches anything; it only
 * describes. The caches themselves are cleared by the functions the rest of
 * the kernel uses (eZCache, eZDBQueryCache, ezpHttpCacheListener,
 * expStaticCacheRunner, expVelocity), never by a second implementation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

if ( !class_exists( 'expCacheManager', false ) ) {
class expCacheManager
{
    /** Counting stops after this many files, so a dry run stays quick. */
    const COUNT_LIMIT = 200000;

    /** @var array|null eZCache::fetchList(), read once */
    private $cacheList = null;

    // ── Results ──────────────────────────────────────────────────────────

    public static function result( $ok, $message, array $items = array(), $dryRun = false, array $data = array() )
    {
        return array( 'ok' => (bool)$ok, 'message' => (string)$message, 'items' => $items,
                      'dry_run' => (bool)$dryRun, 'data' => $data );
    }

    // ── The cache list: ids and tags ─────────────────────────────────────

    /**
     * eZCache::fetchList(), kernel and extension caches alike.
     *
     * @return array
     */
    public function cacheList()
    {
        if ( $this->cacheList === null )
            $this->cacheList = eZCache::fetchList();
        return $this->cacheList;
    }

    /** @return array every tag, sorted */
    public function tagList()
    {
        $tags = eZCache::fetchTagList( $this->cacheList() );
        sort( $tags );
        return $tags;
    }

    /** @return array tag => list of cache ids it clears */
    public function tagMap()
    {
        $map = array();
        foreach ( $this->tagList() as $tag )
        {
            $map[$tag] = array();
            foreach ( eZCache::fetchByTag( $tag, $this->cacheList() ) as $item )
                $map[$tag][] = $item['id'];
        }
        return $map;
    }

    /**
     * Splits "a,b, c" (or an array of such) into a clean list.
     *
     * @param string|array $value
     * @return array
     */
    public static function splitList( $value )
    {
        $out = array();
        foreach ( (array)$value as $part )
        {
            foreach ( explode( ',', (string)$part ) as $one )
            {
                $one = trim( $one );
                if ( $one !== '' && !in_array( $one, $out, true ) )
                    $out[] = $one;
            }
        }
        return $out;
    }

    /**
     * Where a cache item lives and how it is cleared, for list and dry runs.
     *
     * @param array $item an entry of eZCache::fetchList()
     * @param bool $count count its files and bytes
     * @return array id, name, tags, enabled, how, path, exists, files, bytes, complete
     */
    public function describeItem( array $item, $count = false )
    {
        $function = isset( $item['function'] ) && is_array( $item['function'] ) ? $item['function'] : null;
        $method = $function ? $function[0] . '::' . $function[1] : '';
        $path = null;
        $how = '';

        switch ( $method )
        {
            case 'eZCache::clearTemplateCompileCache':
                $path = eZTemplateCompiler::compilationDirectory();
                $how = 'directory removed';
                break;
            case 'eZCache::clearGlobalINICache':
            case 'eZCache::clearTextToImageCache':
                $path = $item['path'];
                $how = 'directory removed';
                break;
            case 'eZCache::clearContentCache':
                $path = eZSys::cacheDirectory() . '/' . $item['path'];
                $how = 'expiry timestamp content-view-cache set, directory moved aside';
                break;
            case 'eZCache::clearTemplateBlockCache':
                $path = eZSys::cacheDirectory() . '/' . $item['path'];
                $how = 'expiry timestamp global-template-block-cache set, directory moved aside';
                break;
            case 'eZCache::clearUserInfoCache':
                $path = eZSys::cacheDirectory() . '/' . $item['path'];
                $how = 'expiry timestamp user-info-cache set, directory moved aside';
                break;
            case 'eZCache::clearTSTranslationCache':
                $path = eZSys::cacheDirectory() . '/' . $item['path'];
                $how = 'expiry timestamp ts-translation-cache set, directory moved aside';
                break;
            case 'eZCache::clearTemplateOverrideCache':
                $path = eZSys::cacheDirectory() . '/' . $item['path'];
                $how = 'directory removed, in-memory override list emptied';
                break;
            case 'eZCache::clearImageAlias':
                $how = 'expiry timestamp image-manager-alias set: every alias is created again when next viewed'
                     . ' (--purge deletes the alias files instead)';
                break;
            case 'eZCache::clearHttpCache':
                $how = 'new HTTP cache generation: every stored page becomes stale';
                break;
            case 'eZCache::clearQueryCache':
                $how = 'new query cache generation: every stored SQL result becomes stale';
                break;
            case 'eZCache::clearClassID':
                $path = eZSys::cacheDirectory();
                $how = 'files classidentifiers_* and classattributeidentifiers_* removed';
                break;
            case 'eZCache::clearSortKey':
                $path = eZSys::cacheDirectory();
                $how = 'files sortkey_* removed';
                break;
            case 'eZCache::clearStateLimitations':
                $path = eZSys::cacheDirectory();
                $how = 'files statelimitations_* removed';
                break;
            case 'eZCache::clearDesignBaseCache':
                $path = eZSys::cacheDirectory();
                $how = 'files ' . eZTemplateDesignResource::DESIGN_BASE_CACHE_NAME . '* removed';
                break;
            case 'eZCache::clearContentTreeMenu':
                $how = 'expiry timestamp content-tree-menu set (browsers load the menu again)';
                break;
            case 'eZCache::clearActiveExtensions':
                $how = 'active extensions list cache expired';
                break;
            case '':
                if ( isset( $item['path'] ) && $item['path'] !== false && strlen( $item['path'] ) > 0 )
                {
                    $path = eZSys::cacheDirectory() . '/' . $item['path'];
                    $how = is_file( $path ) ? 'file removed' : 'directory removed';
                }
                else
                    $how = 'no path and no handler: nothing can be cleared';
                break;
            default:
                $how = 'handler ' . $method . '()';
        }

        // "files a_* and b_* removed": the files are counted by those prefixes
        $prefix = null;
        if ( preg_match( '/^files (.+) removed$/', $how, $m ) )
            $prefix = str_replace( '*', '', $m[1] );

        $desc = array(
            'id'      => $item['id'],
            'name'    => $item['name'],
            'tags'    => array_values( (array)$item['tag'] ),
            'enabled' => (bool)$item['enabled'],
            'how'     => $how,
            'path'    => $path,
            'exists'  => $path !== null && file_exists( $path ),
        );
        if ( $count && $path !== null )
        {
            $stats = $prefix !== null ? self::prefixStats( $path, explode( ' and ', $prefix ) ) : self::pathStats( $path );
            $desc += $stats;
        }
        return $desc;
    }

    /**
     * Files and bytes below $path (a file or a directory).
     *
     * @param string $path
     * @return array files, bytes, complete (false when counting stopped early)
     */
    public static function pathStats( $path, $limit = self::COUNT_LIMIT )
    {
        $stats = array( 'files' => 0, 'bytes' => 0, 'complete' => true );
        if ( is_file( $path ) )
            return array( 'files' => 1, 'bytes' => (int)@filesize( $path ), 'complete' => true );
        if ( !is_dir( $path ) )
            return $stats;
        try
        {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD );
            foreach ( $it as $file )
            {
                if ( !$file->isFile() )
                    continue;
                $stats['files']++;
                $stats['bytes'] += (int)$file->getSize();
                if ( $stats['files'] >= $limit )
                {
                    $stats['complete'] = false;
                    break;
                }
            }
        }
        catch ( Exception $e )
        {
            $stats['complete'] = false;
        }
        return $stats;
    }

    /** Files directly in $dir whose name starts with one of $prefixes. */
    private static function prefixStats( $dir, array $prefixes )
    {
        $stats = array( 'files' => 0, 'bytes' => 0, 'complete' => true );
        foreach ( $prefixes as $prefix )
        {
            foreach ( glob( rtrim( $dir, '/' ) . '/' . $prefix . '*' ) ?: array() as $file )
            {
                if ( is_file( $file ) )
                {
                    $stats['files']++;
                    $stats['bytes'] += (int)@filesize( $file );
                }
            }
        }
        return $stats;
    }

    /**
     * The cache items for --all, --tag or --id.
     *
     * @param string $by 'all', 'tag' or 'id'
     * @param array $names tags or ids
     * @return array array( items, unknown names )
     */
    public function selectItems( $by, array $names = array() )
    {
        $list = $this->cacheList();
        if ( $by === 'all' )
            return array( $list, array() );

        $items = array();
        $unknown = array();
        foreach ( $names as $name )
        {
            $found = $by === 'tag' ? eZCache::fetchByTag( $name, $list )
                                   : array_filter( array( eZCache::fetchByID( $name, $list ) ) );
            if ( !$found )
                $unknown[] = $name;
            foreach ( $found as $item )
                $items[$item['id']] = $item;
        }
        return array( array_values( $items ), $unknown );
    }

    /**
     * Clears caches of the cache list: all of them (Clear all caches), by tag
     * (Clear content/template/INI caches) or by id (Clear selected).
     *
     * @param string $by 'all', 'tag' or 'id'
     * @param array $names tags or ids, unused for 'all'
     * @param bool $dryRun
     * @param array $purge purge instead of clear: array( 'expiry' => ts|false,
     *                     'sleep' => microseconds|false, 'max' => n|false ), or null
     * @return array result
     */
    public function clear( $by, array $names = array(), $dryRun = false, ?array $purge = null )
    {
        if ( !in_array( $by, array( 'all', 'tag', 'id' ), true ) )
            return self::result( false, 'unknown selection "' . $by . '": use all, tag or id' );
        if ( $by !== 'all' && !$names )
            return self::result( false, 'no ' . $by . ' given' );

        list( $items, $unknown ) = $this->selectItems( $by, $names );
        if ( $unknown )
            return self::result( false, 'no such cache ' . $by . ': ' . implode( ', ', $unknown )
                                        . ' (list them with: exp:cache ' . ( $by === 'tag' ? 'tags' : 'list' ) . ')' );

        $described = array();
        foreach ( $items as $item )
            $described[] = $this->describeItem( $item, $dryRun );

        $what = $by === 'all' ? 'all caches' : ( $by === 'tag' ? 'tag ' : 'id ' ) . implode( ', ', $names );
        if ( $dryRun )
            return self::result( true, 'would ' . ( $purge !== null ? 'purge ' : 'clear ' ) . $what . ': '
                                       . count( $items ) . ' cache' . ( count( $items ) === 1 ? '' : 's' ), $described, true );

        if ( $purge !== null )
        {
            // Some purge functions call the reporter unconditionally
            // (purgeImageAlias()), so there always is one.
            $reporter = isset( $purge['reporter'] ) && is_callable( $purge['reporter'] )
                      ? $purge['reporter'] : function ( $file, $count ) {};
            eZCacheTrash::begin();
            try
            {
                foreach ( $items as $item )
                    eZCache::clearItem( $item, true, $reporter,
                                        $purge['sleep'] ?? false, $purge['max'] ?? false, $purge['expiry'] ?? time() );
            }
            finally
            {
                eZCacheTrash::end();
            }
        }
        else if ( $by === 'all' )
            eZCache::clearAll( $this->cacheList() );
        else if ( $by === 'tag' )
        {
            foreach ( $names as $tag )
                eZCache::clearByTag( $tag, $this->cacheList() );
        }
        else
            eZCache::clearByID( $names, $this->cacheList() );

        return self::result( true, ( $purge !== null ? 'purged ' : 'cleared ' ) . $what . ': '
                                   . count( $items ) . ' cache' . ( count( $items ) === 1 ? '' : 's' ), $described );
    }

    // ── PHP caches of the server process ─────────────────────────────────

    /**
     * What the OPcache and APCu rows of Setup > Cache say, for the process
     * that runs this code.
     *
     * @return array opcache and apcu, each available and text
     */
    public static function phpCacheState()
    {
        $state = array(
            'opcache' => array( 'available' => false, 'text' => 'not loaded' ),
            'apcu'    => array( 'available' => false, 'text' => 'not loaded' ),
        );
        if ( function_exists( 'opcache_get_status' ) )
        {
            $status = @opcache_get_status( false );
            $on = is_array( $status ) && !empty( $status['opcache_enabled'] );
            $state['opcache'] = array( 'available' => $on && function_exists( 'opcache_reset' ),
                'text' => $on ? number_format( $status['opcache_statistics']['num_cached_scripts'] ) . ' scripts cached'
                              : 'not enabled for this server' );
        }
        if ( function_exists( 'apcu_cache_info' ) )
        {
            $on = function_exists( 'apcu_enabled' ) && apcu_enabled();
            $info = $on ? @apcu_cache_info( true ) : false;
            $state['apcu'] = array( 'available' => $on && function_exists( 'apcu_clear_cache' ),
                'text' => $on ? number_format( is_array( $info ) ? (int)$info['num_entries'] : 0 ) . ' entries'
                              : 'not enabled for this server' );
        }
        return $state;
    }

    /**
     * Empties OPcache of the process that runs this code: a php-fpm pool, a
     * Velocity server and its workers, php -S. A command-line script has an
     * OPcache of its own (if opcache.enable_cli is on), so run from the
     * command line this reaches no web server.
     *
     * @param bool $dryRun
     * @return array result
     */
    public static function resetOPcache( $dryRun = false )
    {
        $restrict = (string)ini_get( 'opcache.restrict_api' );
        $status = function_exists( 'opcache_get_status' ) ? @opcache_get_status( true ) : false;
        if ( !is_array( $status ) || empty( $status['opcache_enabled'] ) )
            return self::result( false, 'OPcache could not be reset (it is not '
                . ( function_exists( 'opcache_get_status' ) ? 'enabled for this server' : 'loaded' ) . ')', array(), $dryRun );

        $scripts = array_keys( isset( $status['scripts'] ) ? $status['scripts'] : array() );
        if ( $dryRun )
            return self::result( true, 'OPcache: would reset ' . count( $scripts ) . ' cached scripts', array(), true,
                                 array( 'scripts' => count( $scripts ) ) );

        if ( PHP_SAPI === 'cli' )
        {
            // A command-line server -- the Qbix server -- is one long script, so
            // OPcache never sees it idle and a reset stays pending until the
            // server restarts. Invalidating every cached script works at once:
            // each is compiled again on its next include. Only a restart gives
            // the memory back.
            $count = 0;
            foreach ( $scripts as $script )
                if ( @opcache_invalidate( $script, true ) )
                    $count++;
            return $count > 0 || !$scripts
                ? self::result( true, 'OPcache: ' . $count . ' cached scripts invalidated, each is compiled again when it is next'
                                . ' included (this server runs as one long process, so a full reset -- which also frees the memory --'
                                . ' only happens when it restarts)', array(), false, array( 'scripts' => $count ) )
                : self::result( false, 'OPcache: no script could be invalidated'
                                . ( $restrict !== '' ? ' (opcache.restrict_api allows it only for scripts under ' . $restrict . ')' : '' ) );
        }
        return @opcache_reset()
            ? self::result( true, 'OPcache was reset: every PHP file is compiled again when it is next included' )
            : self::result( false, 'OPcache could not be reset'
                            . ( $restrict !== '' ? ' (opcache.restrict_api allows it only for scripts under ' . $restrict . ')'
                                                 : ( !empty( $status['restart_pending'] ) ? ' (a reset is already pending)' : '' ) ) );
    }

    /**
     * Empties APCu of the process that runs this code (see resetOPcache()).
     *
     * @param bool $dryRun
     * @return array result
     */
    public static function clearAPCu( $dryRun = false )
    {
        $usable = function_exists( 'apcu_clear_cache' ) && function_exists( 'apcu_enabled' ) && apcu_enabled();
        if ( !$usable )
            return self::result( false, 'APCu could not be emptied (it is not loaded or not enabled for this server)', array(), $dryRun );
        if ( $dryRun )
        {
            $info = @apcu_cache_info( true );
            $n = is_array( $info ) ? (int)$info['num_entries'] : 0;
            return self::result( true, 'APCu: would empty ' . $n . ' entries', array(), true, array( 'entries' => $n ) );
        }
        return @apcu_clear_cache()
            ? self::result( true, 'APCu was emptied'
                            . ( defined( 'QBIX_SERVER_VERSION' ) ? ', including the memory tier of the Qbix response cache' : '' ) )
            : self::result( false, 'APCu could not be emptied (it is not loaded or not enabled for this server)' );
    }

    // ── SQL query cache ──────────────────────────────────────────────────

    /** A server whose workers started before the class existed must still work. */
    public static function queryCacheAvailable()
    {
        return class_exists( 'eZDBQueryCache' );
    }

    /** @return array result, with enabled and mode in data */
    public static function queryCacheStatus()
    {
        if ( !self::queryCacheAvailable() )
            return self::result( true, 'the SQL query cache is not part of this installation', array(), false,
                                 array( 'available' => false, 'enabled' => false, 'mode' => 'off' ) );
        $enabled = eZDBQueryCache::enabled();
        $mode = eZDBQueryCache::settings()['mode'] ?? 'off';
        return self::result( true, 'the SQL query cache is ' . ( $enabled ? 'on (mode ' . $mode . ')' : 'switched off in querycache.ini' ),
                             array(), false, array( 'available' => true, 'enabled' => $enabled, 'mode' => $mode ) );
    }

    /**
     * One generation bump: every stored result is stale at once, on every
     * server that shares var/, without walking APCu.
     *
     * @param bool $dryRun
     * @return array result
     */
    public static function clearQueryCache( $dryRun = false )
    {
        if ( !self::queryCacheAvailable() )
            return self::result( false, 'the SQL query cache is not part of this installation', array(), $dryRun );
        $enabled = eZDBQueryCache::enabled();
        if ( $dryRun )
            return self::result( true, 'would start a new SQL query cache generation'
                                       . ( $enabled ? '' : ' (it is switched off in querycache.ini)' ), array(), true );
        eZDBQueryCache::clearAll();
        return $enabled
            ? self::result( true, 'The SQL query cache was cleared' )
            : self::result( true, 'The SQL query cache was cleared (it is switched off in querycache.ini, so nothing was being stored)' );
    }

    // ── Role-aware HTTP cache (exphttpcache) ─────────────────────────────

    public static function httpCacheEnabled()
    {
        return eZINI::instance( 'httpcache.ini' )->variable( 'HttpCacheSettings', 'Enabled' ) === 'enabled';
    }

    public static function httpCacheDirectory()
    {
        return eZSys::cacheDirectory() . '/exphttpcache';
    }

    /** The contract the web server wrote, or null when nothing was stored yet. */
    private static function httpCacheContract()
    {
        return class_exists( 'ezpHttpCacheContract' ) ? ( ezpHttpCacheContract::fromDir( self::httpCacheDirectory() ) ?: null ) : null;
    }

    /**
     * Enabled, where, what is on disk and the counters, as System information
     * shows them.
     *
     * @return array result
     */
    public static function httpCacheStatus()
    {
        $enabled = self::httpCacheEnabled();
        $contract = self::httpCacheContract();
        $data = array( 'enabled' => $enabled, 'started' => (bool)$contract, 'dir' => self::httpCacheDirectory(),
                       'inventory' => null, 'statistics' => null,
                       'purges' => eZINI::instance( 'httpcache.ini' )->variable( 'HttpCacheSettings', 'ContentChangePurges' ) );
        if ( $contract )
        {
            $data['inventory'] = $contract->inventory();
            $data['statistics'] = $contract->statistics();
        }
        $message = !$enabled ? 'the HTTP cache is switched off in httpcache.ini'
                 : ( !$contract ? 'the HTTP cache is on, and no page has been stored yet'
                                : 'the HTTP cache is on: ' . $data['inventory']['entries'] . ' entries, generation '
                                  . $data['inventory']['generation'] );
        return self::result( true, $message, array(), false, $data );
    }

    /**
     * Every cached page for every permission context (Clear HTTP cache).
     *
     * @param bool $dryRun
     * @return array result
     */
    public static function clearHttpCache( $dryRun = false )
    {
        if ( !self::httpCacheEnabled() )
            return self::result( false, 'The HTTP cache is switched off in httpcache.ini, so there was nothing to clear', array(), $dryRun );
        $items = array( array( 'tag' => '*', 'what' => 'a new generation: every page stored before now, in every context' ) );
        if ( $dryRun )
        {
            $contract = self::httpCacheContract();
            $inv = $contract ? $contract->inventory() : null;
            return self::result( true, 'would clear the HTTP cache' . ( $inv ? ' (' . $inv['entries'] . ' entries on disk)' : '' ),
                                 $items, true, array( 'inventory' => $inv ) );
        }
        ezpHttpCacheListener::purgeAll();
        return self::result( true, 'The HTTP cache was cleared: every page is rendered again on its next request', $items );
    }

    /**
     * Purges the pages carrying $tags (l<node>, c<object>, pl<parent>, ez-all...).
     *
     * @param array $tags
     * @param bool $dryRun
     * @param array $why tag => what it stands for, for the listing
     * @return array result
     */
    public static function purgeHttpCacheTags( array $tags, $dryRun = false, array $why = array() )
    {
        $bad = array();
        foreach ( $tags as $tag )
            if ( !preg_match( '/^[a-z-]+\d*$/', $tag ) )
                $bad[] = $tag;
        if ( $bad )
            return self::result( false, 'not an HTTP cache tag: ' . implode( ', ', $bad )
                                        . ' (tags look like l42, c17, pl2, ct3, s1, p42, dq, ez-all)', array(), $dryRun );
        if ( !$tags )
            return self::result( false, 'no tag to purge', array(), $dryRun );
        if ( !self::httpCacheEnabled() )
            return self::result( false, 'The HTTP cache is switched off in httpcache.ini, so there was nothing to purge', array(), $dryRun );

        $items = array();
        foreach ( $tags as $tag )
            $items[] = array( 'tag' => $tag, 'what' => $why[$tag] ?? '' );
        if ( $dryRun )
            return self::result( true, 'would purge the HTTP cache pages tagged ' . implode( ', ', $tags ), $items, true );
        ezpHttpCacheListener::purgeTags( $tags );
        return self::result( true, 'purged the HTTP cache pages tagged ' . implode( ', ', $tags )
                                   . ': each is rendered again on its next request', $items );
    }

    /**
     * Purges the pages of nodes: each is tagged l<node> (see ezpHttpCacheListener::tags()).
     *
     * @param array $nodeIDs
     * @param bool $dryRun
     * @return array result
     */
    public static function purgeHttpCacheNodes( array $nodeIDs, $dryRun = false )
    {
        $tags = array();
        $why = array();
        foreach ( $nodeIDs as $nodeID )
        {
            if ( !ctype_digit( (string)$nodeID ) )
                return self::result( false, 'not a node id: ' . $nodeID, array(), $dryRun );
            $tags[] = 'l' . (int)$nodeID;
            $why['l' . (int)$nodeID] = 'the page of node ' . (int)$nodeID;
        }
        return self::purgeHttpCacheTags( $tags, $dryRun, $why );
    }

    /**
     * Purges the page a url shows: the url is resolved to its node, whose
     * page is purged in every context (an entry's key includes the visitor's
     * context, so one url is not one file).
     *
     * @param array $urls
     * @param bool $dryRun
     * @return array result
     */
    public static function purgeHttpCacheURLs( array $urls, $dryRun = false )
    {
        $nodeIDs = array();
        foreach ( $urls as $url )
        {
            $nodeID = self::nodeIDFromURL( $url );
            if ( !$nodeID )
                return self::result( false, 'no node is found at ' . $url, array(), $dryRun );
            $nodeIDs[$nodeID] = true;
        }
        $result = self::purgeHttpCacheNodes( array_keys( $nodeIDs ), $dryRun );
        $result['data']['nodes'] = array_keys( $nodeIDs );
        return $result;
    }

    /** Removes what can no longer be served (setup/info "Clean up"). */
    public static function httpCacheGC( $dryRun = false )
    {
        $contract = self::httpCacheContract();
        if ( !$contract )
            return self::result( false, 'no HTTP cache is stored here (' . self::httpCacheDirectory() . ')', array(), $dryRun );
        if ( $dryRun )
            return self::result( true, 'would remove expired and purged entries, orphaned bodies and old user records from '
                                       . self::httpCacheDirectory(), array(), true, array( 'inventory' => $contract->inventory() ) );
        $c = $contract->gc();
        return self::result( true, 'Removed ' . $c['entries'] . ' dead entries, ' . $c['bodies'] . ' orphaned bodies and '
                                   . $c['records'] . ' old user records (' . $c['kept'] . ' entries kept)', array(), false, $c );
    }

    /** Starts the hit/miss counters again (setup/info "Reset counters"). */
    public static function httpCacheResetStatistics( $dryRun = false )
    {
        $contract = self::httpCacheContract();
        if ( !$contract )
            return self::result( false, 'no HTTP cache is stored here (' . self::httpCacheDirectory() . ')', array(), $dryRun );
        if ( $dryRun )
            return self::result( true, 'would reset the HTTP cache counters', array(), true );
        $contract->resetStatistics();
        return self::result( true, 'The counters were reset.' );
    }

    /**
     * The node a url of this installation shows, or false.
     *
     * Takes a full url, a path, or content/view/<mode>/<node>. A first path
     * element that names a siteaccess is dropped, and that siteaccess's
     * PathPrefix is put back, so /bold/about-us finds "bold-agency/about-us".
     *
     * @param string $url
     * @return int|false
     */
    public static function nodeIDFromURL( $url )
    {
        $path = (string)parse_url( (string)$url, PHP_URL_PATH );
        $path = trim( rawurldecode( $path ), '/' );
        if ( preg_match( '#(?:^|/)content/view/[a-z_]+/(\d+)#i', $path, $m ) )
            return (int)$m[1];

        $siteAccess = $GLOBALS['eZCurrentAccess']['name'] ?? '';
        $parts = $path === '' ? array() : explode( '/', $path );
        $ini = eZINI::instance();
        $available = (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        if ( $parts && in_array( $parts[0], $available, true ) )
        {
            $siteAccess = array_shift( $parts );
            $path = implode( '/', $parts );
        }

        $saINI = $siteAccess !== '' ? eZSiteAccess::getIni( $siteAccess, 'site.ini' ) : $ini;
        $prefix = $saINI->hasVariable( 'SiteAccessSettings', 'PathPrefix' )
                ? trim( (string)$saINI->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' ) : '';
        $exclude = $saINI->hasVariable( 'SiteAccessSettings', 'PathPrefixExclude' )
                 ? (array)$saINI->variable( 'SiteAccessSettings', 'PathPrefixExclude' ) : array();
        $first = $parts ? strtolower( $parts[0] ) : '';
        $excluded = false;
        foreach ( $exclude as $e )
            if ( strtolower( trim( (string)$e, '/' ) ) === $first )
                $excluded = true;
        if ( $prefix !== '' && !$excluded )
            $path = $path === '' ? $prefix : $prefix . '/' . $path;

        if ( $path === '' )
        {
            $contentINI = $siteAccess !== '' ? eZSiteAccess::getIni( $siteAccess, 'content.ini' ) : eZINI::instance( 'content.ini' );
            return (int)$contentINI->variable( 'NodeSettings', 'RootNode' );
        }
        $nodeID = eZURLAliasML::fetchNodeIDByPath( $path );
        return $nodeID ? (int)$nodeID : false;
    }

    // ── Static cache ─────────────────────────────────────────────────────

    public static function staticCacheEnabled()
    {
        return eZINI::instance()->variable( 'ContentSettings', 'StaticCache' ) == 'enabled';
    }

    /**
     * The sites that can be cached, chosen by $siteAccess ('' for all).
     *
     * @return array|false the names, false when $siteAccess is not one of them
     */
    public static function staticCacheTargets( $siteAccess = '' )
    {
        require_once 'kernel/setup/expstaticcacherunner.php';
        $all = eZStaticCache::cacheableSiteAccessList();
        if ( $siteAccess === '' || $siteAccess === null )
            return $all;
        return in_array( $siteAccess, $all, true ) ? array( $siteAccess ) : false;
    }

    /** The configured static cache handler. */
    private static function staticCacheHandler()
    {
        $options = new ezpExtensionOptions( array( 'iniFile'     => 'site.ini',
                                                   'iniSection'  => 'ContentSettings',
                                                   'iniVariable' => 'StaticCacheHandler' ) );
        return eZExtension::getHandlerClass( $options );
    }

    /**
     * Where pages are written, which sites can be generated and what is stored
     * for each.
     *
     * @return array result
     */
    public static function staticCacheStatus()
    {
        require_once 'kernel/setup/expstaticcacherunner.php';
        $handler = self::staticCacheHandler();
        $sites = array();
        foreach ( expStaticCacheRunner::availableSiteAccesses() as $site )
        {
            $dirs = array();
            if ( $handler && method_exists( $handler, 'cacheDirectoriesForSiteAccess' ) )
                foreach ( $handler->cacheDirectoriesForSiteAccess( $site['name'] ) as $dir )
                    $dirs[] = array( 'path' => $dir ) + self::pathStats( $dir );
            $sites[] = $site + array( 'directories' => $dirs );
        }
        $enabled = self::staticCacheEnabled();
        return self::result( true, 'the static cache is ' . ( $enabled ? 'enabled' : 'not enabled' )
                                   . ' in site.ini [ContentSettings] StaticCache; ' . count( $sites ) . ' site'
                                   . ( count( $sites ) === 1 ? '' : 's' ) . ' can be generated', array(), false,
                             array( 'enabled' => $enabled, 'storage_dir' => expStaticCacheRunner::storageDirectory(),
                                    'sites' => $sites ) );
    }

    /**
     * Regenerates the static cache: every page of the site (Create new), or
     * only the given paths or nodes.
     *
     * @param callable $emit progress sink, as expStaticCacheRunner takes it
     * @param array $options siteaccess ('' for all), paths, nodes, max_pages,
     *                       max_depth, purge
     * @param bool $dryRun
     * @return array result, with the number of pages stored in data['stored']
     */
    public static function regenerateStaticCache( $emit, array $options = array(), $dryRun = false )
    {
        require_once 'kernel/setup/expstaticcacherunner.php';
        $siteAccess = (string)( $options['siteaccess'] ?? '' );
        $targets = self::staticCacheTargets( $siteAccess );
        if ( $targets === false )
            return self::result( false, 'not a site that can be cached: ' . $siteAccess . ' (cacheable: '
                                        . implode( ', ', eZStaticCache::cacheableSiteAccessList() ) . ')', array(), $dryRun );
        if ( !$targets )
            return self::result( false, 'No site can be cached: every siteaccess either requires a login or has no SiteURL.', array(), $dryRun );

        $paths = array();
        foreach ( (array)( $options['paths'] ?? array() ) as $p )
            $paths[] = '/' . trim( (string)$p, '/' );
        $nodes = (array)( $options['nodes'] ?? array() );
        foreach ( $nodes as $nodeID )
            if ( !ctype_digit( (string)$nodeID ) )
                return self::result( false, 'not a node id: ' . $nodeID, array(), $dryRun );

        $items = array();
        $perSite = array();
        if ( $paths || $nodes )
        {
            $cache = self::staticCacheHandler();
            if ( !$cache || !method_exists( $cache, 'cacheFilePathsForURL' ) || ( $nodes && !method_exists( $cache, 'siteAccessPath' ) ) )
                return self::result( false, 'the static cache handler cannot store single pages', array(), $dryRun );
            foreach ( $targets as $sa )
            {
                $list = $paths;
                foreach ( $nodes as $nodeID )
                    foreach ( self::nodeAliases( (int)$nodeID ) as $alias )
                    {
                        $p = $cache->siteAccessPath( $sa, $alias );
                        if ( $p !== false )
                            $list[] = $p;
                    }
                $list = array_values( array_unique( $list ) );
                if ( !$list )
                    continue;
                $perSite[$sa] = $list;
                foreach ( $list as $p )
                    $items[] = array( 'siteaccess' => $sa, 'path' => $p, 'files' => $cache->cacheFilePathsForURL( $sa, $p ) );
            }
            if ( !$perSite )
                return self::result( false, 'no page of ' . implode( ', ', $targets ) . ' matches the given nodes', array(), $dryRun );
        }
        else
        {
            $handler = self::staticCacheHandler();
            foreach ( $targets as $sa )
                $items[] = array( 'siteaccess' => $sa, 'path' => '(every page the site links to)',
                                  'removed_first' => ( !isset( $options['purge'] ) || $options['purge'] ) && $handler
                                                     && method_exists( $handler, 'cacheDirectoriesForSiteAccess' )
                                                     ? $handler->cacheDirectoriesForSiteAccess( $sa ) : array() );
        }

        if ( $dryRun )
            return self::result( true, 'would regenerate the static cache of ' . implode( ', ', $perSite ? array_keys( $perSite ) : $targets )
                                       . ( $perSite ? ' for ' . count( $items ) . ' page' . ( count( $items ) === 1 ? '' : 's' ) : '' )
                                       . ' under ' . expStaticCacheRunner::storageDirectory(), $items, true );

        $stored = 0;
        $done = array();
        $sink = function ( $type, $message, array $data = array() ) use ( $emit, &$stored, &$done )
        {
            if ( $type === 'done' && isset( $data['stored'] ) )
            {
                $stored += (int)$data['stored'];
                $done = $data;
            }
            if ( $emit )
                call_user_func( $emit, $type, $message, $data );
        };
        $runnerOptions = array( 'max_pages' => $options['max_pages'] ?? 2500,
                                'max_depth' => $options['max_depth'] ?? 12,
                                'purge'     => $options['purge'] ?? true );
        if ( $perSite )
        {
            foreach ( $perSite as $sa => $list )
            {
                $runner = new expStaticCacheRunner( $sink, array( 'siteaccess' => $sa, 'paths' => $list ) + $runnerOptions );
                $runner->run();
            }
        }
        else
        {
            $runner = new expStaticCacheRunner( $sink, array( 'siteaccess' => $siteAccess === '' ? array() : $siteAccess ) + $runnerOptions );
            $runner->run();
        }
        $wanted = $perSite ? count( $items ) : 0;
        $ok = $stored > 0 && ( !$wanted || $stored >= $wanted );
        return self::result( $ok, $stored . ' page' . ( $stored === 1 ? '' : 's' ) . ' stored under '
                                  . expStaticCacheRunner::storageDirectory()
                                  . ( $wanted && $stored < $wanted ? ' (' . ( $wanted - $stored ) . ' of the requested pages were not)' : '' ),
                             $items, false, array( 'stored' => $stored ) + $done );
    }

    /** Every url alias path of a node, in every language. */
    private static function nodeAliases( $nodeID )
    {
        $paths = array();
        foreach ( eZURLAliasML::fetchByAction( 'eznode', $nodeID, true, true, true ) as $element )
            $paths[] = '/' . $element->getPath();
        if ( !$paths && ( $node = eZContentObjectTreeNode::fetch( $nodeID ) ) )
            $paths[] = '/' . $node->attribute( 'url_alias' );
        return array_values( array_unique( $paths ) );
    }

    /**
     * Removes stored static pages: every page of the chosen sites, or only the
     * given paths or nodes. The web server then asks the CMS for them again.
     *
     * @param array $options siteaccess, paths, nodes
     * @param bool $dryRun
     * @return array result
     */
    public static function clearStaticCache( array $options = array(), $dryRun = false )
    {
        require_once 'kernel/setup/expstaticcacherunner.php';
        $siteAccess = (string)( $options['siteaccess'] ?? '' );
        $targets = self::staticCacheTargets( $siteAccess );
        if ( $targets === false )
            return self::result( false, 'not a site that can be cached: ' . $siteAccess, array(), $dryRun );
        $handler = self::staticCacheHandler();
        if ( !$handler || !method_exists( $handler, 'cacheDirectoriesForSiteAccess' ) )
            return self::result( false, 'the static cache handler cannot say where its pages are', array(), $dryRun );

        $paths = array();
        foreach ( (array)( $options['paths'] ?? array() ) as $p )
            $paths[] = '/' . trim( (string)$p, '/' );
        $nodes = (array)( $options['nodes'] ?? array() );
        foreach ( $nodes as $nodeID )
            if ( !ctype_digit( (string)$nodeID ) )
                return self::result( false, 'not a node id: ' . $nodeID, array(), $dryRun );

        $items = array();
        foreach ( $targets as $sa )
        {
            if ( $paths || $nodes )
            {
                $list = $paths;
                foreach ( $nodes as $nodeID )
                    foreach ( self::nodeAliases( (int)$nodeID ) as $alias )
                        if ( method_exists( $handler, 'siteAccessPath' ) && ( $p = $handler->siteAccessPath( $sa, $alias ) ) !== false )
                            $list[] = $p;
                foreach ( array_unique( $list ) as $p )
                    foreach ( $handler->cacheFilePathsForURL( $sa, $p ) as $file )
                        if ( is_file( $file ) )
                            $items[] = array( 'siteaccess' => $sa, 'path' => $file, 'files' => 1, 'bytes' => (int)filesize( $file ) );
            }
            else
            {
                foreach ( $handler->cacheDirectoriesForSiteAccess( $sa ) as $dir )
                    if ( is_dir( $dir ) )
                        $items[] = array( 'siteaccess' => $sa, 'path' => $dir ) + self::pathStats( $dir );
            }
        }

        $files = 0;
        foreach ( $items as $i )
            $files += $i['files'];
        if ( $dryRun )
            return self::result( true, 'would remove ' . $files . ' stored page file' . ( $files === 1 ? '' : 's' )
                                       . ' of ' . implode( ', ', $targets ), $items, true );
        foreach ( $items as $i )
        {
            if ( is_dir( $i['path'] ) )
                eZDir::recursiveDelete( $i['path'] );
            else
            {
                @unlink( $i['path'] );
                @rmdir( dirname( $i['path'] ) );
            }
        }
        return self::result( true, 'removed ' . $files . ' stored page file' . ( $files === 1 ? '' : 's' )
                                   . ' of ' . implode( ', ', $targets ), $items );
    }

    // ── Velocity: response cache and precompressed static files ──────────

    /** The configured engine, or null when Velocity is not part of this installation. */
    private static function velocity( $engine = null )
    {
        if ( !class_exists( 'expVelocity' ) )
            return null;
        try
        {
            return expVelocity::create( 'velocity.ini', $engine );
        }
        catch ( Exception $e )
        {
            return null;
        }
    }

    /**
     * Where Velocity's response cache is, how much it holds and when it was
     * last cleared.
     *
     * @return array result
     */
    public static function velocityCacheStatus()
    {
        $velocity = self::velocity();
        $qbix = self::velocity( 'qbix' );
        if ( !$qbix )
            return self::result( false, 'Velocity is not part of this installation' );
        $dir = $qbix->cacheDirectory();
        $marker = $dir . '/.generation';
        $stats = self::pathStats( $dir );
        if ( is_file( $marker ) )
            $stats['files']--;
        $data = array( 'engine' => $velocity ? $velocity->engineName() : 'qbix', 'dir' => $dir,
                       'marker' => $marker, 'cleared' => is_file( $marker ) ? filemtime( $marker ) : null ) + $stats;
        $message = 'response cache ' . $dir . ': ' . $stats['files'] . ' files, ' . self::bytes( $stats['bytes'] )
                 . ( $data['cleared'] ? ', last cleared ' . date( 'Y-m-d H:i:s', $data['cleared'] ) : ', never cleared' );
        if ( $velocity && $velocity->engineName() !== 'qbix' )
            $message .= ' (the configured engine, ' . $velocity->engineName() . ', has no response cache)';
        return self::result( true, $message, array(), false, $data );
    }

    /**
     * Re-renders every page Velocity cached, on its next request
     * (expVelocity::clearCache(), the same as exp:velocity cache clear).
     *
     * @param bool $dryRun
     * @return array result
     */
    public static function clearVelocityCache( $dryRun = false )
    {
        $velocity = self::velocity();
        if ( !$velocity )
            return self::result( false, 'Velocity is not part of this installation', array(), $dryRun );
        if ( $dryRun && $velocity->engineName() !== 'qbix' )
            return self::result( true, 'would do nothing: the ' . $velocity->engineName() . ' engine runs without a response cache', array(), true );
        if ( $dryRun )
        {
            $status = self::velocityCacheStatus();
            $items = array( array( 'path' => $status['data']['marker'] ?? '', 'what' => 'generation marker touched: every entry stored before now is a miss' ) );
            if ( self::httpCacheEnabled() )
                $items[] = array( 'tag' => '*', 'what' => 'a new HTTP cache generation (the server reads it as its marker)' );
            return self::result( true, 'would clear the ' . $velocity->engineName() . ' response cache', $items, true );
        }
        $r = $velocity->clearCache();
        return self::result( $r['ok'], $r['message'], array(), false, (array)$r['data'] );
    }

    /** The directory Velocity keeps precompressed static files in. */
    public static function precompressDirectory()
    {
        $velocity = self::velocity( 'qbix' );
        if ( $velocity )
        {
            try
            {
                $assets = $velocity->assets();
                if ( !empty( $assets['precompress'] ) )
                    return $assets['precompress'];
            }
            catch ( Exception $e )
            {
            }
        }
        return eZSys::rootDir() . '/var/tmp/precompress';
    }

    /** The .gz files the engine wrote (md5-of-path, dash, hex mtime). */
    private static function precompressFiles( $dir )
    {
        $files = array();
        foreach ( glob( rtrim( $dir, '/' ) . '/*' ) ?: array() as $file )
        {
            $name = basename( $file );
            if ( is_file( $file ) && ( preg_match( '/^[0-9a-f]{32}-[0-9a-f]+\.gz$/', $name )
                                       || preg_match( '/^[0-9a-f]{32}-[0-9a-f]+\.gz\.\d+\.tmp$/', $name ) ) )
                $files[] = $file;
        }
        return $files;
    }

    /**
     * Precompressed static files: where, how many, how big, and the settings.
     *
     * @return array result
     */
    public static function precompressStatus()
    {
        $dir = self::precompressDirectory();
        $files = self::precompressFiles( $dir );
        $bytes = 0;
        foreach ( $files as $f )
            $bytes += (int)@filesize( $f );
        $velocity = self::velocity( 'qbix' );
        $setting = function ( $name, $default ) use ( $velocity )
        {
            $ini = eZINI::instance( 'velocity.ini' );
            return $ini->hasVariable( 'ServerSettings', $name ) ? $ini->variable( 'ServerSettings', $name ) : $default;
        };
        $data = array( 'dir' => $dir, 'files' => count( $files ), 'bytes' => $bytes,
                       'enabled' => !in_array( $setting( 'PrecompressStatic', 'enabled' ), array( 'disabled', 'false' ), true ),
                       'max_files' => (int)$setting( 'PrecompressMaxFiles', 0 ), 'min_size' => (int)$setting( 'PrecompressMinSize', 0 ),
                       'velocity' => (bool)$velocity );
        return self::result( true, 'precompressed static files ' . $dir . ': ' . count( $files ) . ' files, '
                                   . self::bytes( $bytes ) . ' (velocity.ini PrecompressStatic '
                                   . ( $data['enabled'] ? 'enabled' : 'disabled' ) . ')', array(), false, $data );
    }

    /**
     * Removes the precompressed static files. The server compresses a file
     * again the next time it is requested; an edited file already gets a new
     * entry (the name carries its mtime), so this is for freeing the space or
     * after changing the compression level. The engine offers no rebuild of
     * its own: files are compressed on their first request.
     *
     * @param bool $dryRun
     * @return array result
     */
    public static function clearPrecompress( $dryRun = false )
    {
        $dir = self::precompressDirectory();
        $files = self::precompressFiles( $dir );
        $bytes = 0;
        foreach ( $files as $f )
            $bytes += (int)@filesize( $f );
        $items = array( array( 'path' => $dir, 'files' => count( $files ), 'bytes' => $bytes ) );
        if ( $dryRun )
            return self::result( true, 'would remove ' . count( $files ) . ' precompressed files (' . self::bytes( $bytes ) . ') from ' . $dir, $items, true );
        $removed = 0;
        foreach ( $files as $f )
            if ( @unlink( $f ) )
                $removed++;
        return self::result( $removed === count( $files ), 'removed ' . $removed . ' of ' . count( $files )
                                                           . ' precompressed files from ' . $dir, $items );
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public static function bytes( $bytes )
    {
        if ( $bytes < 1024 )
            return $bytes . ' B';
        if ( $bytes < 1048576 )
            return round( $bytes / 1024, 1 ) . ' KB';
        if ( $bytes < 1073741824 )
            return round( $bytes / 1048576, 1 ) . ' MB';
        return round( $bytes / 1073741824, 2 ) . ' GB';
    }
}
}
