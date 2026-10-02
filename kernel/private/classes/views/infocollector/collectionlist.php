<?php
/**
 * The code of kernel/infocollector/collectionlist.php, moved into a class (#207 stage 1). The file kernel/infocollector/collectionlist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/infocollector/collectionlist.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Infocollector
{

class Collectionlist extends \Exponential\Runnable\ModuleView
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
        $objectID = $Params['ObjectID'];
        $offset = $Params['Offset'];

        if( !is_numeric( $offset ) )
        {
            $offset = 0;
        }


        if( $module->isCurrentAction( 'RemoveCollections' ) && $http->hasPostVariable( 'CollectionIDArray' ) )
        {
            $collectionIDArray = $http->postVariable( 'CollectionIDArray' );
            $http->setSessionVariable( 'CollectionIDArray', $collectionIDArray );
            $http->setSessionVariable( 'ObjectID', $objectID );

            $collections = count( $collectionIDArray );

            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'module', $module );
            $tpl->setVariable( 'collections', $collections );
            $tpl->setVariable( 'object_id', $objectID );
            $tpl->setVariable( 'remove_type', 'collections' );

            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:infocollector/confirmremoval.tpl' );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => \ezpI18n::tr( 'kernel/infocollector', 'Collected information' ) ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }


        if( $module->isCurrentAction( 'ConfirmRemoval' ) )
        {
            $collectionIDArray = $http->sessionVariable( 'CollectionIDArray' );

            if( is_array( $collectionIDArray ) )
            {
                foreach( $collectionIDArray as $collectionID )
                {
                    \eZInformationCollection::removeCollection( $collectionID );
                }
            }

            $objectID = $http->sessionVariable( 'ObjectID' );
            // Audit (doc/bc/6.0/audit.md, data.infocollection.remove): never the collected values
            if ( is_array( $collectionIDArray ) && $collectionIDArray && class_exists( 'expAuditHook' ) )
                \expAuditHook::emit( 'data.infocollection.remove', array(
                    'object' => array( 'type' => 'collection', 'id' => implode( ',', array_map( 'intval', $collectionIDArray ) ) ),
                    'target' => \expAuditHook::object( (int)$objectID ), 'verb' => 'remove',
                    'before' => array( 'count' => count( $collectionIDArray ) ) ) );
            $module->redirectTo( '/infocollector/collectionlist/' . $objectID );
        }


        if( \eZPreferences::value( 'admin_infocollector_list_limit' ) )
        {
            switch( \eZPreferences::value( 'admin_infocollector_list_limit' ) )
            {
                case '2': { $limit = 25; } break;
                case '3': { $limit = 50; } break;
                default:  { $limit = 10; } break;
            }
        }
        else
        {
            $limit = 10;
        }

        $object = false;

        if( is_numeric( $objectID ) )
        {
            $object = \eZContentObject::fetch( $objectID );
        }

        if( !$object )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        $collections = \eZInformationCollection::fetchCollectionsList( $objectID, /* object id */
                                                                      false, /* creator id */
                                                                      false, /* user identifier */
                                                                      array( 'limit' => $limit,'offset' => $offset ) /* limit array */ );
        $numberOfCollections = \eZInformationCollection::fetchCollectionsCount( $objectID );

        $viewParameters = array( 'offset' => $offset );
        $objectName = $object->attribute( 'name' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $module );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'object', $object );
        $tpl->setVariable( 'collection_array', $collections );
        $tpl->setVariable( 'collection_count', $numberOfCollections );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:infocollector/collectionlist.tpl' );
        $Result['path'] = array( array( 'url' => '/infocollector/overview',
                                        'text' => \ezpI18n::tr( 'kernel/infocollector', 'Collected information' ) ),
                                 array( 'url' => false,
                                        'text' => $objectName ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
