<?php
/**
 * The code of kernel/setup/cache.php, moved into a class (#207 stage 1). The file kernel/setup/cache.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/cache.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

class Cache extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];


        $ini = \eZINI::instance( );
        $tpl = \eZTemplate::factory();

        // Every action below is expCacheManager's, which the command line
        // (exp:cache, bin/php/cache.php) calls too, so the two cannot differ. Loaded
        // by path: a server whose workers kept an autoload array from before the class
        // existed (Velocity) must still render this page.
        require_once 'kernel/classes/expcachemanager.php';
        $cacheManager = new \expCacheManager();

        $cacheList = $cacheManager->cacheList();

        $cacheCleared = array( 'all' => false,
                               'content' => false,
                               'ini' => false,
                               'template' => false,
                               'list' => false,
                               'static' => false,
                               // array( ok, message ) once OPcache or APCu was emptied
                               'opcache' => false,
                               'apcu' => false,
                               // array( ok, message ) once the SQL query cache or the
                               // HTTP cache was cleared
                               'querycache' => false,
                               'httpcache' => false,
                               // a button Setup > System information has too (expCacheManager::$sharedActions)
                               'shared' => false );

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

        // The feedback rows take array( ok, message ).
        $feedback = function ( array $result )
        {
            \eZDebug::writeNotice( $result['message'], 'setup/cache' );
            return array( $result['ok'], $result['message'] );
        };

        // The HTTP cache, query cache and SQL profile buttons Setup > System information has as well: one code
        // path for both pages (expCacheManager::sharedActionFromPost(), which checks setup/managecache itself).
        $sharedCacheAction = \expCacheManager::sharedActionFromPost( $http );
        if ( $sharedCacheAction )
            $cacheCleared['shared'] = $feedback( $sharedCacheAction );

        // PHP's opcode cache and APCu. Both live in the shared memory of the server
        // process that answers this request -- a php-fpm pool, a Qbix server and its
        // workers, a FrankenPHP process and its threads, php -S and its workers -- so
        // emptying one here empties it for that server, and for nothing else: a
        // command-line script, another pool or another engine keeps its own.
        if ( $module->isCurrentAction( 'ResetOPcache' ) )
            $cacheCleared['opcache'] = $feedback( \expCacheManager::resetOPcache() );

        if ( $module->isCurrentAction( 'ClearAPCu' ) )
            $cacheCleared['apcu'] = $feedback( \expCacheManager::clearAPCu() );

        // What the two rows say about each cache before its button.
        $phpCacheState = \expCacheManager::phpCacheState();

        if ( $module->isCurrentAction( 'ClearAllCache' ) )
        {
            $cacheManager->clear( 'all' );
            $cacheCleared['all'] = true;
        }

        if ( $module->isCurrentAction( 'ClearContentCache' ) )
        {
            $cacheManager->clear( 'tag', array( 'content' ) );
            $cacheCleared['content'] = true;
        }

        if ( $module->isCurrentAction( 'ClearINICache' ) )
        {
            $cacheManager->clear( 'tag', array( 'ini' ) );
            $cacheCleared['ini'] = true;
        }

        if ( $module->isCurrentAction( 'ClearTemplateCache' ) )
        {
            $cacheManager->clear( 'tag', array( 'template' ) );
            $cacheCleared['template'] = true;
        }

        if ( $module->isCurrentAction( 'ClearCache' ) && $module->hasActionParameter( 'CacheList' ) && is_array( $module->actionParameter( 'CacheList' ) ) )
        {
            // Only ids of the list: anything else in the form is ignored, as before.
            $cacheIDList = \eZCache::fetchIDList( $cacheList );
            $cacheClearList = array_values( array_intersect( array_map( 'strval', $module->actionParameter( 'CacheList' ) ), $cacheIDList ) );
            $cacheItemList = array();
            if ( $cacheClearList )
            {
                $result = $cacheManager->clear( 'id', $cacheClearList );
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
            }
            $cacheCleared['list'] = $cacheItemList;
        }

        // Which sites can be generated, and where the pages are written. Shown on the
        // page so the operator chooses a site before pressing the button, and can see
        // the target directory without reading two ini files.
        require_once 'kernel/setup/expstaticcacherunner.php';
        $staticCacheSiteAccessList = \expStaticCacheRunner::availableSiteAccesses();
        $staticCacheStorageDir = \expStaticCacheRunner::storageDirectory();
        $staticCacheEnabled = \expCacheManager::staticCacheEnabled();

        if ( $module->isCurrentAction( 'RegenerateStaticCache' ) )
        {
            // The chosen site, empty meaning every cacheable one. Only a name this
            // installation serves is accepted.
            $staticCacheSiteAccess = $module->hasActionParameter( 'StaticCacheSiteAccess' )
                                   ? (string)$module->actionParameter( 'StaticCacheSiteAccess' ) : '';
            if ( $staticCacheSiteAccess !== '' &&
                 !in_array( $staticCacheSiteAccess, \eZStaticCache::cacheableSiteAccessList(), true ) )
                $staticCacheSiteAccess = '';

            // The same runner the streamed console uses, so the two cannot behave
            // differently. This path is the fallback for a browser without
            // EventSource: it produces the same files, it just says nothing until it
            // is finished.
            $result = \expCacheManager::regenerateStaticCache( null, array( 'siteaccess' => $staticCacheSiteAccess ) );

            // Report what happened rather than that the code ran. This was set to true
            // unconditionally, so a run that wrote nothing - and the shipped
            // configuration could write nothing at all - still reported success.
            $cacheCleared['static'] = (int)( $result['data']['stored'] ?? 0 );
        }

        $queryCacheAvailable = \expCacheManager::queryCacheAvailable();

        if ( $queryCacheAvailable && $module->isCurrentAction( 'ClearQueryCache' ) )
            $cacheCleared['querycache'] = $feedback( \expCacheManager::clearQueryCache() );

        if ( $module->isCurrentAction( 'ClearHttpCache' ) )
            $cacheCleared['httpcache'] = $feedback( \expCacheManager::clearHttpCache() );

        $tpl->setVariable( "cache_cleared", $cacheCleared );
        $tpl->setVariable( 'query_cache_enabled', $queryCacheAvailable && \eZDBQueryCache::enabled() );
        $tpl->setVariable( 'query_cache_mode', $queryCacheAvailable ? ( \eZDBQueryCache::settings()['mode'] ?? 'off' ) : 'off' );
        $tpl->setVariable( 'http_cache_enabled', \expCacheManager::httpCacheEnabled() );
        $tpl->setVariable( 'static_cache_siteaccess_list', $staticCacheSiteAccessList );
        $tpl->setVariable( 'static_cache_storage_dir', $staticCacheStorageDir );
        $tpl->setVariable( 'static_cache_enabled', $staticCacheEnabled );
        $tpl->setVariable( 'static_cache_stream_url', 'setup/staticcachestream' );
        $tpl->setVariable( "cache_enabled", $cacheEnabled );
        $tpl->setVariable( 'cache_list', $cacheList );
        $tpl->setVariable( 'php_cache_state', $phpCacheState );
        $tpl->setVariable( 'sql_profile_on', \expCacheManager::sqlProfileEnabled() );


        $Result = array();
        $Result['content'] = $tpl->fetch( "design:setup/cache.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/setup', 'Cache admin' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
