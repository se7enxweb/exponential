<?php
/**
 * The code of kernel/state/view.php, moved into a class (#207 stage 1). The file kernel/state/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/view.php:
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

class View extends \Exponential\Runnable\ModuleView
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
        $LanguageCode = $Params['Language'];

        $group = \eZContentObjectStateGroup::fetchByIdentifier( $GroupIdentifier );

        if ( !is_object( $group ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
        }

        $state = $group->stateByIdentifier( $StateIdentifier );

        if ( !is_object( $state ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
        }

        $currentAction = $Module->currentAction();

        if ( $currentAction == 'Edit' )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( "state/edit/$GroupIdentifier/$StateIdentifier" ) );
        }

        if ( $LanguageCode )
        {
            $state->setCurrentLanguage( $LanguageCode );
        }



        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'group', $group );
        $tpl->setVariable( 'state', $state );

        $Result = array(
            'content' => $tpl->fetch( 'design:state/view.tpl' ),
            'path' => array(
                array( 'url' => false,
                       'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => 'state/group/' . $group->attribute( 'identifier' ),
                       'text' => $group->attribute( 'identifier' ) ),
                array( 'url' => false,
                       'text' => $state->attribute( 'identifier' ) )
            )
        );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
