<?php
/**
 * The code of kernel/state/group.php, moved into a class (#207 stage 1). The file kernel/state/group.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/group.php:
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
        $GroupIdentifier = $Params['GroupIdentifier'];
        $LanguageCode = $Params['Language'];

        $group = \eZContentObjectStateGroup::fetchByIdentifier( $GroupIdentifier );

        if ( !is_object( $group ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
        }



        $tpl = \eZTemplate::factory();

        $currentAction = $Module->currentAction();

        if ( !$group->isInternal() )
        {
            if ( $currentAction == 'Remove' && $Module->hasActionParameter( 'RemoveIDList' ) )
            {
                $removeIDList = $Module->actionParameter( 'RemoveIDList' );
                $group->removeStatesByID( $removeIDList );
            }
            else if ( $currentAction == 'Edit' )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( "state/group_edit/$GroupIdentifier" ) );
            }
            else if ( $currentAction == 'Create' )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( "state/edit/$GroupIdentifier" ) );
            }
            else if ( $currentAction == 'UpdateOrder' && $Module->hasActionParameter( 'Order' ) )
            {
                $orderArray = $Module->actionParameter( 'Order' );
                asort( $orderArray );
                $stateIDList = array_keys( $orderArray );

                $group->reorderStates( $stateIDList );
            }
        }

        if ( $LanguageCode )
        {
            $group->setCurrentLanguage( $LanguageCode );
        }

        $tpl->setVariable( 'group', $group );

        $Result = array(
            'path' => array(
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => 'state/groups', 'text' => \ezpI18n::tr( 'kernel/state', 'Groups' ) ),
                array( 'url' => false, 'text' => $group->attribute( 'current_translation' )->attribute( 'name' ) )
            ),
            'content' => $tpl->fetch( 'design:state/group.tpl' )
        );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
