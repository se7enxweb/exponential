<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];


$ini = eZINI::instance( );
$tpl = eZTemplate::factory();

$cacheList = eZCache::fetchList();

$cacheCleared = array( 'all' => false,
                       'content' => false,
                       'ini' => false,
                       'template' => false,
                       'list' => false,
                       'static' => false,
                       // array( ok, message ) once OPcache or APCu was emptied
                       'opcache' => false,
                       'apcu' => false );

$contentCacheEnabled = $ini->variable( 'ContentSettings', 'ViewCaching' ) == 'enabled';
$iniCacheEnabled = true;
$templateCacheEnabled = $ini->variable( 'TemplateSettings', 'TemplateCache' ) == 'enabled';

$cacheEnabledList = array();
foreach ( $cacheList as $cacheItem )
{
    $cacheEnabledList[$cacheItem['id']] = $cacheItem['enabled'];
}

$cacheEnabled = array( 'all' => true,
                       'content' => $contentCacheEnabled,
                       'ini' => $iniCacheEnabled,
                       'template' => $templateCacheEnabled,
                       'list' => $cacheEnabledList );

// PHP's opcode cache and APCu. Both live in the shared memory of the server
// process that answers this request -- a php-fpm pool, a Qbix server and its
// workers, a FrankenPHP process and its threads, php -S and its workers -- so
// emptying one here empties it for that server, and for nothing else: a
// command-line script, another pool or another engine keeps its own.
if ( $module->isCurrentAction( 'ResetOPcache' ) )
{
    $restrict = (string)ini_get( 'opcache.restrict_api' );
    $status = function_exists( 'opcache_get_status' ) ? @opcache_get_status( true ) : false;
    if ( !is_array( $status ) || empty( $status['opcache_enabled'] ) )
        $cacheCleared['opcache'] = array( false, 'OPcache could not be reset (it is not '
            . ( function_exists( 'opcache_get_status' ) ? 'enabled for this server' : 'loaded' ) . ')' );
    elseif ( PHP_SAPI === 'cli' )
    {
        // A command-line server -- the Qbix server -- is one long script, so
        // OPcache never sees it idle and a reset stays pending until the
        // server restarts. Invalidating every cached script works at once:
        // each is compiled again on its next include. Only a restart gives
        // the memory back.
        $count = 0;
        foreach ( array_keys( isset( $status['scripts'] ) ? $status['scripts'] : array() ) as $script )
            if ( @opcache_invalidate( $script, true ) )
                $count++;
        $cacheCleared['opcache'] = $count > 0 || empty( $status['scripts'] )
            ? array( true, 'OPcache: ' . $count . ' cached scripts invalidated, each is compiled again when it is next'
                           . ' included (this server runs as one long process, so a full reset -- which also frees the memory --'
                           . ' only happens when it restarts)' )
            : array( false, 'OPcache: no script could be invalidated'
                            . ( $restrict !== '' ? ' (opcache.restrict_api allows it only for scripts under ' . $restrict . ')' : '' ) );
    }
    else
        $cacheCleared['opcache'] = @opcache_reset()
            ? array( true, 'OPcache was reset: every PHP file is compiled again when it is next included' )
            : array( false, 'OPcache could not be reset'
                            . ( $restrict !== '' ? ' (opcache.restrict_api allows it only for scripts under ' . $restrict . ')'
                                                 : ( !empty( $status['restart_pending'] ) ? ' (a reset is already pending)' : '' ) ) );
    eZDebug::writeNotice( $cacheCleared['opcache'][1], 'setup/cache' );
}

if ( $module->isCurrentAction( 'ClearAPCu' ) )
{
    $cacheCleared['apcu'] = function_exists( 'apcu_clear_cache' ) && function_exists( 'apcu_enabled' ) && apcu_enabled()
                            && @apcu_clear_cache()
        ? array( true, 'APCu was emptied'
                       . ( defined( 'QBIX_SERVER_VERSION' ) ? ', including the memory tier of the Qbix response cache' : '' ) )
        : array( false, 'APCu could not be emptied (it is not loaded or not enabled for this server)' );
    eZDebug::writeNotice( $cacheCleared['apcu'][1], 'setup/cache' );
}

// What the two rows say about each cache before its button.
$phpCacheState = array(
    'opcache' => array( 'available' => false, 'text' => 'not loaded' ),
    'apcu'    => array( 'available' => false, 'text' => 'not loaded' ),
);
if ( function_exists( 'opcache_get_status' ) )
{
    $status = @opcache_get_status( false );
    $on = is_array( $status ) && !empty( $status['opcache_enabled'] );
    $phpCacheState['opcache'] = array( 'available' => $on && function_exists( 'opcache_reset' ),
        'text' => $on ? number_format( $status['opcache_statistics']['num_cached_scripts'] ) . ' scripts cached'
                      : 'not enabled for this server' );
}
if ( function_exists( 'apcu_cache_info' ) )
{
    $on = function_exists( 'apcu_enabled' ) && apcu_enabled();
    $info = $on ? @apcu_cache_info( true ) : false;
    $phpCacheState['apcu'] = array( 'available' => $on && function_exists( 'apcu_clear_cache' ),
        'text' => $on ? number_format( is_array( $info ) ? (int)$info['num_entries'] : 0 ) . ' entries'
                      : 'not enabled for this server' );
}

if ( $module->isCurrentAction( 'ClearAllCache' ) )
{
    eZCache::clearAll();
    $cacheCleared['all'] = true;
}

if ( $module->isCurrentAction( 'ClearContentCache' ) )
{
    eZCache::clearByTag( 'content' );
    $cacheCleared['content'] = true;
}

if ( $module->isCurrentAction( 'ClearINICache' ) )
{
    eZCache::clearByTag( 'ini' );
    $cacheCleared['ini'] = true;
}

if ( $module->isCurrentAction( 'ClearTemplateCache' ) )
{
    eZCache::clearByTag( 'template' );
    $cacheCleared['template'] = true;
}

if ( $module->isCurrentAction( 'ClearCache' ) && $module->hasActionParameter( 'CacheList' ) && is_array( $module->actionParameter( 'CacheList' ) ) )
{
    $cacheClearList = $module->actionParameter( 'CacheList' );
    eZCache::clearByID( $cacheClearList );
    $cacheItemList = array();
    foreach ( $cacheClearList as $cacheClearItem )
    {
        foreach ( $cacheList as $cacheItem )
        {
            if ( $cacheItem['id'] == $cacheClearItem )
            {
                $cacheItemList[] = $cacheItem;
                break;
            }
        }
    }
    $cacheCleared['list'] = $cacheItemList;
}

// Which sites can be generated, and where the pages are written. Shown on the
// page so the operator chooses a site before pressing the button, and can see
// the target directory without reading two ini files.
require_once 'kernel/setup/expstaticcacherunner.php';
$staticCacheSiteAccessList = expStaticCacheRunner::availableSiteAccesses();
$staticCacheStorageDir = expStaticCacheRunner::storageDirectory();
$staticCacheEnabled = $ini->variable( 'ContentSettings', 'StaticCache' ) == 'enabled';

if ( $module->isCurrentAction( 'RegenerateStaticCache' ) )
{
    // The chosen site, empty meaning every cacheable one. Only a name this
    // installation serves is accepted.
    $staticCacheSiteAccess = $module->hasActionParameter( 'StaticCacheSiteAccess' )
                           ? (string)$module->actionParameter( 'StaticCacheSiteAccess' ) : '';
    if ( $staticCacheSiteAccess !== '' &&
         !in_array( $staticCacheSiteAccess, eZStaticCache::cacheableSiteAccessList(), true ) )
        $staticCacheSiteAccess = '';

    // The same runner the streamed console uses, so the two cannot behave
    // differently. This path is the fallback for a browser without
    // EventSource: it produces the same files, it just says nothing until it
    // is finished.
    $staticCacheStored = 0;
    $runner = new expStaticCacheRunner(
        function ( $type, $message, array $data = array() ) use ( &$staticCacheStored )
        {
            if ( $type === 'done' && isset( $data['stored'] ) )
                $staticCacheStored = (int)$data['stored'];
        },
        array( 'siteaccess' => $staticCacheSiteAccess ) );
    $runner->run();

    // Report what happened rather than that the code ran. This was set to true
    // unconditionally, so a run that wrote nothing - and the shipped
    // configuration could write nothing at all - still reported success.
    $cacheCleared['static'] = $staticCacheStored;
}

$tpl->setVariable( "cache_cleared", $cacheCleared );
$tpl->setVariable( 'static_cache_siteaccess_list', $staticCacheSiteAccessList );
$tpl->setVariable( 'static_cache_storage_dir', $staticCacheStorageDir );
$tpl->setVariable( 'static_cache_enabled', $staticCacheEnabled );
$tpl->setVariable( 'static_cache_stream_url', 'setup/staticcachestream' );
$tpl->setVariable( "cache_enabled", $cacheEnabled );
$tpl->setVariable( 'cache_list', $cacheList );
$tpl->setVariable( 'php_cache_state', $phpCacheState );


$Result = array();
$Result['content'] = $tpl->fetch( "design:setup/cache.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Cache admin' ) ) );

?>
