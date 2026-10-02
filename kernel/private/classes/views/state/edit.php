<?php
/**
 * The code of kernel/state/edit.php, moved into a class (#207 stage 1). The file kernel/state/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/edit.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\State
{

class Edit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $GroupIdentifier = $Params['GroupIdentifier'];
        $StateIdentifier = $Params['StateIdentifier'];

        $group = \eZContentObjectStateGroup::fetchByIdentifier( $GroupIdentifier );

        if ( !is_object( $group ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
        }

        if ( $group->isInternal() )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }

        $state = $StateIdentifier ? $group->stateByIdentifier( $StateIdentifier ) : $group->newState();

        if ( !is_object( $state ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
        }

        $redirectUrl = "state/group/$GroupIdentifier";


        $tpl = \eZTemplate::factory();

        $currentAction = $Module->currentAction();

        if ( $currentAction == 'Cancel' )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $redirectUrl ) );
        }
        else if ( $currentAction == 'Store' )
        {
            $state->fetchHTTPPersistentVariables();

            $messages = array();
            $isValid = $state->isValid( $messages );

            if ( $isValid )
            {
                $state->store();
                \ezpEvent::getInstance()->notify( 'content/state/cache', array( $state->attribute( 'id' ) ) );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $redirectUrl ) );
            }

            $tpl->setVariable( 'is_valid', $isValid );
            $tpl->setVariable( 'validation_messages', $messages );
        }

        $tpl->setVariable( 'state', $state );
        $tpl->setVariable( 'group', $group );

        if ( $StateIdentifier === null )
        {
            $path = array(
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'New' ) ),
                array( 'url' => false, 'text' => $GroupIdentifier )
            );
        }
        else
        {
            $path = array(
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'Edit' ) ),
                array( 'url' => false, 'text' => $GroupIdentifier ),
                array( 'url' => false, 'text' => $StateIdentifier ),
            );
        }

        $Result = array(
            'path' => $path,
            'content' => $tpl->fetch( 'design:state/edit.tpl' )
        );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
