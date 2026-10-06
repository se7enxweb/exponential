<?php
/**
 * The code of kernel/state/edit.php, moved into a class (#207 stage 1). The file kernel/state/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/edit.php:
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
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( self::cancelURI( $Module, $GroupIdentifier ) ) );
        }
        else if ( $currentAction == 'Store' )
        {
            $state->fetchHTTPPersistentVariables();

            $messages = array();
            $isValid = $state->isValid( $messages );

            if ( $isValid )
            {
                $auditNew = !$state->attribute( 'id' );
                $state->store();
                \ezpEvent::getInstance()->notify( 'content/state/cache', array( $state->attribute( 'id' ) ) );
                // Audit (doc/bc/6.0/audit.md, content.state.change)
                if ( class_exists( 'expAuditHook' ) )
                    \expAuditHook::emit( 'content.state.change', function () use ( $state, $group, $auditNew ) {
                        $names = array();
                        foreach ( (array)$state->allTranslations() as $t )
                            { $l = \eZContentLanguage::fetch( (int)$t->attribute( 'language_id' ) & ~1 ); $names[$l ? (string)$l->attribute( 'locale' ) : (string)$t->attribute( 'language_id' )] = (string)$t->attribute( 'name' ); }
                        return array( 'object' => array( 'type' => 'state', 'id' => (int)$state->attribute( 'id' ),
                                                         'identifier' => (string)$state->attribute( 'identifier' ) ),
                                      'target' => array( 'type' => 'state_group', 'id' => (int)$group->attribute( 'id' ),
                                                         'identifier' => (string)$group->attribute( 'identifier' ) ),
                                      'verb' => $auditNew ? 'create' : 'change',
                                      'after' => array( 'identifier' => (string)$state->attribute( 'identifier' ), 'translations' => $names ) );
                    } );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $redirectUrl ) );
            }

            $tpl->setVariable( 'is_valid', $isValid );
            $tpl->setVariable( 'validation_messages', $messages );
        }

        $tpl->setVariable( 'state', $state );
        $tpl->setVariable( 'group', $group );
        $tpl->setVariable( 'redirect_if_discarded', \eZRedirectManager::formReturnURI( $Module ) );
        // The other states of the group, so the form can say where a new state goes and
        // whether it becomes the default.
        $tpl->setVariable( 'group_info', Groups::describeGroup( $group, Groups::references() ) );
        // The address the form posts back to: the identifier the state is stored under, not one
        // typed into a form the kernel refused (that one names no state).
        $tpl->setVariable( 'form_identifier', $StateIdentifier === null ? '' : (string)$StateIdentifier );

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

    /**
     * Where Cancel goes: the page the form names in RedirectIfDiscarded, else the page viewed last, else the
     * state group, where it always went. The rules are those of \eZRedirectManager::returnURI().
     *
     * @param \eZModule|null $module
     * @param string $groupIdentifier
     * @param array $options see \eZRedirectManager::returnURI()
     * @return string
     */
    public static function cancelURI( $module, $groupIdentifier, $options = array() )
    {
        return \eZRedirectManager::returnURI( $module, '/state/group/' . $groupIdentifier, \eZRedirectManager::formReturnURIs(), $options );
    }
}

}
