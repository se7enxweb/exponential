<?php
/**
 * The code of kernel/collaboration/item.php, moved into a class (#207 stage 1). The file kernel/collaboration/item.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/collaboration/item.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Collaboration
{

class Item extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $ViewMode = $Params['ViewMode'];
        $ItemID = $Params['ItemID'];

        $Offset = $Params['Offset'];
        if ( !is_numeric( $Offset ) )
            $Offset = 0;

        /** @var eZCollaborationItem $collabItem */
        $collabItem = \eZCollaborationItem::fetch( $ItemID );

        if ( !$collabItem->userIsParticipant( \eZUser::currentUser() ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel', array() ) );
        }

        $collabHandler = $collabItem->handler();
        $collabItem->handleView( $ViewMode );
        $template = $collabHandler->template( $ViewMode );
        $collabTitle = $collabItem->title();

        $viewParameters = array( 'offset' => $Offset );

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'collab_item', $collabItem );

        $Result = array();
        $Result['content'] = $tpl->fetch( $template );

        $collabHandler->readItem( $collabItem );

        $Result['path'] = array( array( 'url' => 'collaboration/view/summary',
                                        'text' => \ezpI18n::tr( 'kernel/collaboration', 'Collaboration' ) ),
                                 array( 'url' => false,
                                        'text' => $collabTitle ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
