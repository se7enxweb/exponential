<?php
/**
 * The code of kernel/collaboration/group.php, moved into a class (#207 stage 1). The file kernel/collaboration/group.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/collaboration/group.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Collaboration
{

class Group extends \Exponential\Runnable\ModuleView
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
        $GroupID = $Params['GroupID'];

        $Offset = $Params['Offset'];
        if ( !is_numeric( $Offset ) )
            $Offset = 0;

        $collabGroup = \eZCollaborationGroup::fetch( $GroupID );
        if ( $collabGroup === null )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( !\eZCollaborationViewHandler::groupExists( $ViewMode ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $view = \eZCollaborationViewHandler::instance( $ViewMode, \eZCollaborationViewHandler::TYPE_GROUP );

        $template = $view->template();

        $collabGroupTitle = $collabGroup->attribute( 'title' );

        $viewParameters = array( 'offset' => $Offset );

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'collab_group', $collabGroup );

        $Result = array();
        $Result['content'] = $tpl->fetch( $template );
        $Result['path'] = array( array( 'url' => 'collaboration/view/summary',
                                        'text' => \ezpI18n::tr( 'kernel/collaboration', 'Collaboration' ) ),
                                 array( 'url' => false,
                                        'text' => 'Group' ),
                                 array( 'url' => false,
                                        'text' => $collabGroupTitle ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
