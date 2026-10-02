<?php
/**
 * The code of kernel/content/trash.php, moved into a class (#207 stage 1). The file kernel/content/trash.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/trash.php:
 *
 *
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

        $user = \eZUser::currentUser();
        $userID = $user->id();

        if ( $http->hasPostVariable( 'RemoveButton' )  )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $access = $user->hasAccessTo( 'content', 'cleantrash' );
                if ( $access['accessWord'] == 'yes' || $access['accessWord'] == 'limited' )
                {
                    $deleteIDArray = $http->postVariable( 'DeleteIDArray' );

                    foreach ( $deleteIDArray as $deleteID )
                    {

                        $objectList = \eZPersistentObject::fetchObjectList( \eZContentObject::definition(),
                                                                           null,
                                                                           array( 'id' => $deleteID ),
                                                                           null,
                                                                           null,
                                                                           true );
                        foreach ( $objectList as $object )
                        {
                            $object->purge();
                        }
                    }
                }
                else
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
                }
            }
        }
        else if ( $http->hasPostVariable( 'EmptyButton' )  )
        {
            $access = $user->hasAccessTo( 'content', 'cleantrash' );
            if ( $access['accessWord'] == 'yes' || $access['accessWord'] == 'limited' )
            {
                while ( true )
                {
                    // Fetch 100 objects at a time, to limit transaction size
                    $objectList = \eZPersistentObject::fetchObjectList( \eZContentObject::definition(),
                                                                       null,
                                                                       array( 'status' => \eZContentObject::STATUS_ARCHIVED ),
                                                                       null,
                                                                       100,
                                                                       true );
                    if ( count( $objectList ) < 1 )
                        break;

                    foreach ( $objectList as $object )
                    {
                        $object->purge();
                    }
                }
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
