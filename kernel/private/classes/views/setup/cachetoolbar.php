<?php
/**
 * The code of kernel/setup/cachetoolbar.php, moved into a class (#207 stage 1). The file kernel/setup/cachetoolbar.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/cachetoolbar.php:
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

class Cachetoolbar extends \Exponential\Runnable\ModuleView
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

        $cacheType = $module->actionParameter( 'CacheType' );

        \eZPreferences::setValue( 'admin_clearcache_type', $cacheType );

        $nodeID = null;
        $objectID = null;

        if ( $module->hasActionParameter ( 'NodeID' ) )
            $nodeID = $module->actionParameter( 'NodeID' );

        if ( $module->hasActionParameter ( 'ObjectID' ) )
            $objectID = $module->actionParameter( 'ObjectID' );

        if ( $cacheType == 'All' )
        {
            \eZCache::clearAll();
        }
        elseif ( $cacheType == 'Template' )
        {
            \eZCache::clearByTag( 'template' );
        }
        elseif ( $cacheType == 'Content' )
        {
            \eZCache::clearByTag( 'content' );
        }
        elseif ( $cacheType == 'TemplateContent' )
        {
            \eZCache::clearByTag( 'template' );
            \eZCache::clearByTag( 'content' );
        }
        elseif ( $cacheType == 'Ini' )
        {
            \eZCache::clearByTag( 'ini' );
        }
        elseif ( $cacheType == 'Static' )
        {
            // get staticCacheHandler instance
            $optionArray = array( 'iniFile'      => 'site.ini',
                                  'iniSection'   => 'ContentSettings',
                                  'iniVariable'  => 'StaticCacheHandler' );

            $options = new \ezpExtensionOptions( $optionArray );
            $staticCacheHandler = \eZExtension::getHandlerClass( $options );

            $staticCacheHandler->generateCache( true, true );
            $cacheCleared['static'] = true;
        }
        elseif ( $cacheType == 'ContentNode' )
        {
            $contentModule = \eZModule::exists( 'content' );
            if ( $contentModule instanceof \eZModule )
            {
                $contentModule->setCurrentAction( 'ClearViewCache', 'action' );

                $contentModule->setActionParameter( 'NodeID', $nodeID, 'action' );
                $contentModule->setActionParameter( 'ObjectID', $objectID, 'action' );

                $contentModule->run( 'action', array( $nodeID, $objectID) );
            }
        }
        elseif ( $cacheType == 'ContentSubtree' )
        {
            $contentModule = \eZModule::exists( 'content' );
            if ( $contentModule instanceof \eZModule )
            {
                $contentModule->setCurrentAction( 'ClearViewCacheSubtree', 'action' );

                $contentModule->setActionParameter( 'NodeID', $nodeID, 'action' );
                $contentModule->setActionParameter( 'ObjectID', $objectID, 'action' );

                $contentModule->run( 'action', array( $nodeID, $objectID) );
            }
        }

        $uri = $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessedModifyingURI', '/' ) );
        $module->redirectTo( $uri );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
