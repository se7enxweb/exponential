<?php
/**
 * The code of kernel/state/groups.php, moved into a class (#207 stage 1). The file kernel/state/groups.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/groups.php:
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

class Groups extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $offset = $Params['Offset'];

        $listLimitPreferenceName = 'admin_state_group_list_limit';
        $listLimitPreferenceValue = \eZPreferences::value( $listLimitPreferenceName );

        // The sizes on offer are configured, not written here; the preference holds
        // the position in that list, which is what it has always held.
        list( $limit, $limitChoice, $limitChoices ) =
            \expAdminPagination::chosen( 'state/groups', $listLimitPreferenceName );

        $languages = \eZContentLanguage::fetchList();



        $tpl = \eZTemplate::factory();

        \eZDebug::writeDebug( $Module->currentAction() );
        if ( $Module->isCurrentAction( 'Remove' ) && $Module->hasActionParameter( 'RemoveIDList' ) )
        {
            $removeIDList = $Module->actionParameter( 'RemoveIDList' );

            foreach ( $removeIDList as $removeID )
            {
                $group = \eZContentObjectStateGroup::fetchById( $removeID );
                if ( $group && !$group->isInternal() )
                {
                    \eZContentObjectStateGroup::removeByID( $removeID );
                    \ezpEvent::getInstance()->notify( 'content/state/group/cache', array( $removeID ) );
                }
            }
        }
        else if ( $Module->isCurrentAction( 'Create' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( 'state/group_edit' ) );
        }

        $groups = \eZContentObjectStateGroup::fetchByOffset( $limit, $offset );
        $groupCount = \eZPersistentObject::count( \eZContentObjectStateGroup::definition() );

        $viewParameters = array( 'offset' => $offset );

        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'list_limit_preference_name', $listLimitPreferenceName );
        $tpl->setVariable( 'list_limit_preference_value', $listLimitPreferenceValue );
        $tpl->setVariable( 'groups', $groups );
        $tpl->setVariable( 'group_count', $groupCount );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'languages', $languages );

        $Result = array(
            'content' => $tpl->fetch( 'design:state/groups.tpl' ),
            'path'    => array(
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'Groups' ) )
            )
        );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
