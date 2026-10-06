<?php
/**
 * The code of kernel/state/group_edit.php, moved into a class (#207 stage 1). The file kernel/state/group_edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/group_edit.php:
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

class GroupEdit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $GroupIdentifier = $Params['GroupIdentifier'];
        $Module = $Params['Module'];

        $group = $GroupIdentifier === null ? new \eZContentObjectStateGroup() : \eZContentObjectStateGroup::fetchByIdentifier( $GroupIdentifier );

        if ( !is_object( $group ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
        }

        if ( $group->isInternal() )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }



        $tpl = \eZTemplate::factory();

        $currentAction = $Module->currentAction();

        if ( $currentAction == 'Cancel' )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( 'state/groups' ) );
        }
        else if ( $currentAction == 'Store' )
        {
            $group->fetchHTTPPersistentVariables();

            $messages = array();
            $isValid = $group->isValid( $messages );

            if ( $isValid )
            {
                $auditNew = !$group->attribute( 'id' );
                $group->store();
                \ezpEvent::getInstance()->notify( 'content/state/group/cache', array( $group->attribute( 'id' ) ) );
                // Audit (doc/bc/6.0/audit.md, content.state.change)
                if ( class_exists( 'expAuditHook' ) )
                    \expAuditHook::emit( 'content.state.change', function () use ( $group, $auditNew ) {
                        $names = array();
                        foreach ( (array)$group->allTranslations() as $t )
                            { $l = \eZContentLanguage::fetch( (int)$t->attribute( 'language_id' ) & ~1 ); $names[$l ? (string)$l->attribute( 'locale' ) : (string)$t->attribute( 'language_id' )] = (string)$t->attribute( 'name' ); }
                        return array( 'object' => array( 'type' => 'state_group', 'id' => (int)$group->attribute( 'id' ),
                                                         'identifier' => (string)$group->attribute( 'identifier' ) ),
                                      'verb' => $auditNew ? 'create' : 'change',
                                      'after' => array( 'identifier' => (string)$group->attribute( 'identifier' ), 'translations' => $names ) );
                    } );
                if ( $GroupIdentifier === null )
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( 'state/group/' . $group->attribute( 'identifier' ) ) );
                }
                else
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( 'state/groups' ) );
                }
            }

            $tpl->setVariable( 'is_valid', $isValid );
            $tpl->setVariable( 'validation_messages', $messages );
        }

        $tpl->setVariable( 'group', $group );
        // An existing group: its states and the roles that name it as StateGroup_<identifier>,
        // which the form mentions next to the identifier.
        $storedGroup = $GroupIdentifier === null ? false : \eZContentObjectStateGroup::fetchByIdentifier( $GroupIdentifier );
        $tpl->setVariable( 'group_info', $storedGroup ? Groups::describeGroup( $storedGroup, Groups::references() ) : false );
        // The address the form posts back to: the identifier the group is stored under, not one
        // typed into a form the kernel refused (that one names no group).
        $tpl->setVariable( 'form_identifier', $GroupIdentifier === null ? '' : (string)$GroupIdentifier );

        if ( $GroupIdentifier === null )
        {
            $path = array(
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'New group' ) )
            );
        }
        else
        {
            $path = array(
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'Group edit' ) ),
                array( 'url' => false, 'text' => $group->attribute( 'identifier' ) )
            );
        }

        $Result = array(
            'content' => $tpl->fetch( 'design:state/group_edit.tpl' ),
            'path'    => $path
        );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
