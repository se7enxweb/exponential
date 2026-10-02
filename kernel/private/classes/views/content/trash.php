<?php
/**
 * The code of kernel/content/trash.php, moved into a class (#207 stage 1). The file kernel/content/trash.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/trash.php:
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

class Trash extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $Offset = $Params['Offset'];
        if ( isset( $Params['UserParameters'] ) )
        {
            $UserParameters = $Params['UserParameters'];
        }
        else
        {
            $UserParameters = array();
        }
        $viewParameters = array( 'offset' => $Offset, 'namefilter' => false );
        $viewParameters = array_merge( $viewParameters, $UserParameters );

        $http = \eZHTTPTool::instance();

        // The trash service is shared with bin/php/trashpurge.php and cronjobs/trashpurge.php. Loaded by path:
        // a server whose workers kept an autoload array from before the class existed (Velocity) still renders.
        require_once 'kernel/private/classes/services/trash.php';

        $user = \eZUser::currentUser();
        $userID = $user->id();

        if ( $http->hasPostVariable( 'RemoveButton' )  )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                if ( \Exponential\Service\Trash::canEmpty( $user ) )
                {
                    \Exponential\Service\Trash::purgeObjects( $http->postVariable( 'DeleteIDArray' ) );
                }
                else
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
                }
            }
        }
        else if ( $http->hasPostVariable( 'EmptyButton' )  )
        {
            if ( \Exponential\Service\Trash::canEmpty( $user ) )
            {
                // as the command does: 100 at a time, each batch in a transaction of its own, a pause between them
                \Exponential\Service\Trash::emptyTrash( 100, 1 );
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'view_parameters', $viewParameters );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/trash.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'Trash' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
