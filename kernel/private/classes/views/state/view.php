<?php
/**
 * The code of kernel/state/view.php, moved into a class (#207 stage 1). The file kernel/state/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/view.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

        $LanguageCode = Groups::knownLocale( $LanguageCode );
        if ( $LanguageCode )
        {
            $state->setCurrentLanguage( $LanguageCode );
        }

        // The state among the others of its group: its place in the order (the first is the
        // default), its objects, and the roles whose policies name it.
        $groupInfo = Groups::describeGroup( $group, Groups::references(), $LanguageCode );
        $stateInfo = false;
        foreach ( $groupInfo['states'] as $info )
            if ( $info['id'] === (int)$state->attribute( 'id' ) )
                $stateInfo = $info;
        $stateRoles = $stateInfo ? Groups::rolesNamingState( $groupInfo['roles'], $stateInfo['id'] ) : array();

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'group', $group );
        $tpl->setVariable( 'state', $state );
        $tpl->setVariable( 'group_info', $groupInfo );
        $tpl->setVariable( 'state_info', $stateInfo );
        $tpl->setVariable( 'state_roles', $stateRoles );
        $tpl->setVariable( 'current_language', $LanguageCode );

        $Result = array(
            'content' => $tpl->fetch( 'design:state/view.tpl' ),
            'path' => array(
                array( 'url' => false,
                       'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => 'state/groups',
                       'text' => \ezpI18n::tr( 'kernel/state', 'Groups' ) ),
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
