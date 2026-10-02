<?php
/**
 * The code of kernel/content/view.php, moved into a class (#207 stage 1). The file kernel/content/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/view.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class View extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();

        $ViewMode = $Params['ViewMode'];
        $NodeID = $Params['NodeID'];
        $Module = $Params['Module'];
        $LanguageCode = $Params['Language'];
        $Offset = $Params['Offset'];
        $Year = $Params['Year'];
        $Month = $Params['Month'];
        $Day = $Params['Day'];

        // Check if we should switch access mode (http/https) for this node.
        \eZSSLZone::checkNodeID( 'content', 'view', $NodeID );

        if ( isset( $Params['UserParameters'] ) )
        {
            $UserParameters = $Params['UserParameters'];
        }
        else
        {
            $UserParameters = array();
        }

        if ( $Offset )
            $Offset = (int) $Offset;
        if ( $Year )
            $Year = (int) $Year;
        if ( $Month )
            $Month = (int) $Month;
        if ( $Day )
            $Day = (int) $Day;

        $NodeID = (int) $NodeID;

        if ( $NodeID < 1 )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
        }

        $ini = \eZINI::instance();

        // Be able to filter node id for general use
        $NodeID = \ezpEvent::getInstance()->filter( 'content/view', $NodeID, $ini );

        $testingHandler = new \ezpMultivariateTest( \ezpMultivariateTest::getHandler() );

        if ( $testingHandler->isEnabled() )
            $NodeID = $testingHandler->execute( $NodeID );

        // Audit (doc/bc/6.0/audit.md, content.node.view): a sampled read, never the rendered page
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::read( 'content.node.view', $NodeID, function () use ( $NodeID, $Params ) {
                return array( 'object' => array( 'type' => 'node', 'id' => (int)$NodeID ),
                              'after' => array( 'view_mode' => isset( $Params['ViewMode'] ) ? (string)$Params['ViewMode'] : null ) );
            } );

        $viewCacheEnabled = ( $ini->variable( 'ContentSettings', 'ViewCaching' ) == 'enabled' );

        if ( isset( $Params['ViewCache'] ) )
        {
            $viewCacheEnabled = $Params['ViewCache'];
        }
        elseif ( $viewCacheEnabled && !in_array( $ViewMode, $ini->variableArray( 'ContentSettings', 'CachedViewModes' ) ) )
        {
            $viewCacheEnabled = false;
        }

        if ( $viewCacheEnabled && $ini->hasVariable( 'ContentSettings', 'ViewCacheTweaks' ) )
        {
            $viewCacheTweaks = $ini->variable( 'ContentSettings', 'ViewCacheTweaks' );
            if ( isset( $viewCacheTweaks[$NodeID] ) && strpos( $viewCacheTweaks[$NodeID], 'disabled' ) !== false )
            {
                $viewCacheEnabled = false;
            }
        }

        $collectionAttributes = false;
        if ( isset( $Params['CollectionAttributes'] ) )
            $collectionAttributes = $Params['CollectionAttributes'];

        $validation = array( 'processed' => false,
                             'attributes' => array() );
        if ( isset( $Params['AttributeValidation'] ) )
            $validation = $Params['AttributeValidation'];

        $res = \eZTemplateDesignResource::instance();
        $keys = $res->keys();
        if ( isset( $keys['layout'] ) )
            $layout = $keys['layout'];
        else
            $layout = false;

        $viewParameters = array(
            'offset' => $Offset,
            'year' => $Year,
            'month' => $Month,
            'day' => $Day,
            'namefilter' => false,
            '_custom' => $UserParameters
        );
        // Keep the following array_merge for BC
        // All user parameters will be exposed as direct variables in template.
        $viewParameters = array_merge( $viewParameters, $UserParameters );

        $user = \eZUser::currentUser();

        \eZDebugSetting::addTimingPoint( 'kernel-content-view', 'Operation start' );


        $operationResult = array();
        $Result = [];

        if ( \eZOperationHandler::operationIsAvailable( 'content_read' ) )
        {
            $operationResult = \eZOperationHandler::execute( 'content', 'read', array( 'node_id' => $NodeID,
                                                                                      'user_id' => $user->id(),
                                                                                      'language_code' => $LanguageCode ), null, true );
        }

        if ( ( isset( $operationResult['status'] ) && $operationResult['status'] != \eZModuleOperationInfo::STATUS_CONTINUE ) )
        {
            switch( $operationResult['status'] )
            {
                case \eZModuleOperationInfo::STATUS_HALTED:
                case \eZModuleOperationInfo::STATUS_REPEAT:
                {
                    if ( isset( $operationResult['redirect_url'] ) )
                    {
                        $Module->redirectTo( $operationResult['redirect_url'] );
                        return $this->viewResult( isset( $Result ) ? $Result : null, null );
                    }
                    else if ( isset( $operationResult['result'] ) )
                    {
                        $result = $operationResult['result'];
                        $resultContent = false;
                        if ( is_array( $result ) )
                        {
                            if ( isset( $result['content'] ) )
                            {
                                $resultContent = $result['content'];
                            }
                            if ( isset( $result['path'] ) )
                            {
                                $Result['path'] = $result['path'];
                            }
                        }
                        else
                        {
                            $resultContent = $result;
                        }
                        $Result['content'] = $resultContent;
                    }
                } break;
                case \eZModuleOperationInfo::STATUS_CANCELLED:
                {
                    $Result = array();
                    $Result['content'] = "Content view cancelled<br/>";
                } break;
            }
            return $this->viewResult( isset( $Result ) ? $Result : null,  class_exists( 'ezpHttpCacheListener' ) ? \ezpHttpCacheListener::noteContentView( $Result, $ViewMode ) : $Result );
        }
        else
        {
            $args = compact(
                array(
                    "NodeID", "Module", "tpl", "LanguageCode", "ViewMode", "Offset", "ini", "viewParameters", "collectionAttributes", "validation"
                )
            );
            if ( $viewCacheEnabled )
            {
                $cacheFileArray = \eZNodeviewfunctions::generateViewCacheFile(
                    \eZUser::currentUser(),
                    $NodeID,
                    $Offset,
                    $layout,
                    $LanguageCode,
                    $ViewMode,
                    $viewParameters,
                    false
                );

                if ( class_exists( 'sevenxValkeyCacheBlock' ) )
                {
                    $sevenxValkeyCacheBlock = \sevenxValkeyCacheBlock::instance();

                    $result = $sevenxValkeyCacheBlock->get( $cacheFileArray['cache_path'], true, 0, $NodeID, 0 );

                    if ( is_array( $result ) && isset( $result['no_cache'] ) && $result['no_cache'] )
                    {
                        $data = \eZNodeviewfunctions::contentViewGenerate( false, $args );
                        $result = $data['content'];
                    }
                    else if ( $result === false )
                    {
                        $data = \eZNodeviewfunctions::contentViewGenerate( false, $args );

                        $result = $data['content']; // Return the $Result array

                        if ( !isset( $result['no_cache'] ) || !$result['no_cache'] )
                        {
                            $sevenxValkeyCacheBlock->put( $cacheFileArray['cache_path'], $result, 3600, 0, $NodeID );
                        }
                    }
                }
                else
                {
                    $result = \eZClusterFileHandler::instance( $cacheFileArray['cache_path'] )
                        ->processCache(
                            array( 'eZNodeviewfunctions', 'contentViewRetrieve' ),
                            array( 'eZNodeviewfunctions', 'contentViewGenerate' ),
                            null,
                            null,
                            $args
                        );
                }

                // check if $result is an array (could also be eZClusterFileFailure) and contains responseHeaders
                if ( is_array( $result ) && !empty( $result['responseHeaders'] ) )
                {
                    foreach ( $result['responseHeaders'] as $header )
                    {
                        header( $header );
                    }
                }

                return $this->viewResult( isset( $Result ) ? $Result : null,  class_exists( 'ezpHttpCacheListener' ) ? \ezpHttpCacheListener::noteContentView( $result, $ViewMode ) : $result );
            }

            $data = \eZNodeviewfunctions::contentViewGenerate( false, $args ); // the false parameter will disable generation of the 'binarydata' entry
            // Return the $Result array, noted for the role-aware HTTP cache.
            return $this->viewResult( isset( $Result ) ? $Result : null,  class_exists( 'ezpHttpCacheListener' ) ? \ezpHttpCacheListener::noteContentView( $data['content'], $ViewMode ) : $data['content'] );
        }

        // Looking for some view-cache code?
        // Try the eZNodeviewfunctions class for enlightenment.

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
