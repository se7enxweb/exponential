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
                       'static' => false );

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


$Result = array();
$Result['content'] = $tpl->fetch( "design:setup/cache.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Cache admin' ) ) );

?>
