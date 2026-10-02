<?php
/**
 * The code of kernel/infocollector/view.php, moved into a class (#207 stage 1). The file kernel/infocollector/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/infocollector/view.php:
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

class View extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $Module = $Params['Module'];
        $collectionID = $Params['CollectionID'];

        $collection = false;
        $object = false;

        if( is_numeric( $collectionID ) )
        {
            $collection = \eZInformationCollection::fetch( $collectionID );
        }

        if( !$collection )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        $object = \eZContentObject::fetch( $collection->attribute( 'contentobject_id' ) );

        if( !$object )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        $objectID   = $collection->attribute( 'contentobject_id' );
        $objectName = $object->attribute( 'name' );

        // Audit (doc/bc/6.0/audit.md, data.infocollection.view): which collection was opened, never its values
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.infocollection.view', array( 'object' => array( 'type' => 'collection', 'id' => (int)$collection->attribute( 'id' ) ),
                'target' => array( 'type' => 'object', 'id' => (int)$objectID, 'name' => (string)$objectName ), 'verb' => 'read' ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'collection', $collection );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:infocollector/view.tpl' );
        $Result['path'] = array( array( 'url' => '/infocollector/overview',
                                        'text' => \ezpI18n::tr( 'kernel/infocollector', 'Collected information' ) ),
                                 array( 'url' => '/infocollector/collectionlist/' . $objectID,
                                        'text' => $objectName ),
                                 array( 'url' => false,
                                        'text' => $collectionID ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
