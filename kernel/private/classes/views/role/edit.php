<?php
/**
 * The code of kernel/role/edit.php, moved into a class (#207 stage 1). The file kernel/role/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/role/edit.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Role
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

        $tpl = \eZTemplate::factory();
        $Module = $Params['Module'];
        $roleID = $Params['RoleID'];

        $ini = \eZINI::instance( 'module.ini' );
        $modules = $ini->variable( 'ModuleSettings', 'ModuleList' );
        sort( $modules );

        $role = \eZRole::fetch( 0, $roleID );
        if ( $role === null )
        {
            $role = \eZRole::fetch( $roleID );
            if ( $role )
            {
                if ( $role->attribute( 'version' ) == '0' )
                {
                    $temporaryRole = $role->createTemporaryVersion();
                    unset( $role );
                    $role = $temporaryRole;
                }
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }
        }

        $http = \eZHTTPTool::instance();

        $tpl->setVariable( 'module', $Module );

        $role->turnOffCaching();

        $tpl->setVariable( 'role', $role );
        $Module->setTitle( 'Edit ' . $role->attribute( 'name' ) );

        if ( $http->hasPostVariable( 'NewName' ) && $role->attribute( 'name' ) != $http->postVariable( 'NewName' ) )
        {
            $role->setAttribute( 'name' , $http->postVariable( 'NewName' ) );
            $role->store();
            // Set flag for audit. If true audit will be processed
            $http->setSessionVariable( 'RoleWasChanged', true );
        }

        $showModules = true;
        $showFunctions = false;
        $showLimitations = false;
        $noFunctions = false;
        $noLimitations = false;

        $this->applyRole( $http, $originalRole, $role, $Module );

        if ( $http->hasPostVariable( 'Discard' ) )
        {
            $http->removeSessionVariable( 'RoleWasChanged' );

            $role = \eZRole::fetch( $roleID ) ;
            $originalRole = \eZRole::fetch( $role->attribute( 'version') );
            $role->removeThis();
            if ( $originalRole != null && $originalRole->attribute( 'is_new' ) == 1 )
            {
                // A new role that was never stored: its policies go with it (remove() took only the role row and
                // left them behind); removePolicies() records nothing for a role that is still new
                $originalRole->removePolicies();
                $originalRole->remove();
            }
            return $this->viewResult( null, $Module->redirectTo( self::cancelURI( $Module ) ) );
        }

        if ( $http->hasPostVariable( 'ChangeRoleName' ) )
        {
            $role->setAttribute( 'name', $http->postVariable( 'NewName' ) );
            // Set flag for audit. If true audit will be processed
            $http->setSessionVariable( 'RoleWasChanged', true );
        }
        if ( $http->hasPostVariable( 'AddModule' ) )
        {
            if ( $http->hasPostVariable( 'Modules' ) )
                $currentModule = $http->postVariable( 'Modules' );
            else if ( $http->hasPostVariable( 'CurrentModule' ) )
                $currentModule = $http->postVariable( 'CurrentModule' );
            $policy = \eZPolicy::createNew( $roleID, array( 'ModuleName'=> $currentModule,
                                                           'FunctionName' => '*' ) );
        }
        if ( $http->hasPostVariable( 'AddFunction' ) )
        {
            $currentModule = $http->postVariable( 'CurrentModule' );
            $currentFunction = $http->postVariable( 'ModuleFunction' );
            \eZDebugSetting::writeDebug( 'kernel-role-edit', $currentModule, 'currentModule');
            $policy = \eZPolicy::createNew( $roleID, array( 'ModuleName'=> $currentModule,
                                                           'FunctionName' => $currentFunction ) );
        }

        $this->addLimitation( $http, $policy, $limitationList, $limitation, $limitationID, $limitationIdentifier, $nodeLimitationValues, $currentModule, $currentFunction, $mod, $functions, $currentFunctionLimitations, $functionLimitation, $limitationValues, $policyLimitation, $limitationValue, $roleID, $db );

        $this->removePolicies( $http, $role, $policyID, $removedPolicies );

        $this->movePolicies( $http, $role, $movedTo );

        if ( ( $__return = $this->customFunction( $http, $currentModule, $mod, $functions, $functionNames, $showModules, $showFunctions, $showLimitations, $noFunctions, $tpl, $Module, $role, $Result ) ) !== $this )
            return $__return;

        if ( $http->hasPostVariable( 'DiscardFunction' ) )
        {
            $showModules = true;
            $showFunctions = false;
        }

        if ( ( $__return = $this->selectLimitationValues( $http, $db, $currentModule, $mod, $functions, $functionNames, $showModules, $showFunctions, $showLimitations, $policyID, $nodeLimitationValues, $currentFunction, $currentFunctionLimitations, $key, $limitation, $limitationValue, $noLimitations, $policy, $limitationList, $limitationID, $limitationIdentifier, $functionLimitation, $limitationValues, $policyLimitation, $roleID, $Module, $Result, $tpl ) ) !== $this )
            return $__return;

        if ( ( $__return = $this->discardLimitation( $http, $currentModule, $mod, $functions, $functionNames, $showModules, $showFunctions, $tpl, $Result ) ) !== $this )
            return $__return;

        if ( ( $__return = $this->createPolicy( $http, $Module, $role, $tpl, $modules, $Result ) ) !== $this )
            return $__return;

        // Set flag for audit. If true audit will be processed
        // Cancel button was pressed
        if ( $http->hasPostVariable( 'CancelPolicyButton' ) )
            $http->setSessionVariable( 'RoleWasChanged', false );

        // The policy list is paged. $role.policies is every policy the role has, which
        // is what the permission system needs and what a screen must not ask for: on an
        // installation whose roles carry policies in the millions, loading them all to
        // draw twenty five exhausts memory before the first row is written.
        //
        // The offset arrives as (policy_offset) rather than (offset), so a page that
        // grows a second list later does not find the two moving together.
        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();

        $policyLimit = (int)\eZINI::instance( 'site.ini' )->variable( 'RoleSettings', 'PoliciesPerPage' );
        if ( $policyLimit < 1 )
            $policyLimit = 25;

        $policyOffset = isset( $userParameters['policy_offset'] ) ? (int)$userParameters['policy_offset'] : 0;
        if ( $policyOffset < 0 )
            $policyOffset = 0;

        // Sorted by the database, not in the browser, for the reason role/list is: the
        // list is shown a page at a time. (policy_sort)/(policy_dir), like the offset,
        // carry the name of the list. The default, id ascending, is the role's own
        // order, the one the up and down buttons change; they are offered only then.
        $policySort = isset( $userParameters['policy_sort'] ) ? (string)$userParameters['policy_sort'] : 'id';
        if ( !isset( \eZRole::sortColumnsForPolicyList()[$policySort] ) )
            $policySort = 'id';
        $policyDir = ( isset( $userParameters['policy_dir'] ) && strtolower( $userParameters['policy_dir'] ) === 'desc' ) ? 'desc' : 'asc';

        $policyCount = $role->policyCount();
        $policies    = $role->policyPage( $policyOffset, $policyLimit, $policySort, $policyDir );

        $tpl->setVariable( 'policy_sort', array( 'field'     => $policySort,
                                                 'direction' => $policyDir,
                                                 'opposite'  => $policyDir === 'asc' ? 'desc' : 'asc' ) );
        $tpl->setVariable( 'policy_order_editable', $policySort === 'id' && $policyDir === 'asc' );
        $tpl->setVariable( 'policy_offset', $policyOffset );

        $tpl->setVariable( 'policy_count', $policyCount );
        // Editing a role works on a temporary version, which is a row of its own with
        // an id of its own. Paging must not put that id in the address: the page is
        // /role/edit/<the role>, and a link to /role/edit/<the draft> edits the draft
        // directly, so Apply would then write back to the wrong row.
        $tpl->setVariable( 'policy_page_uri', '/role/edit/' . (int)$roleID );
        $tpl->setVariable( 'policy_limit', $policyLimit );
        $tpl->setVariable( 'view_parameters', array_merge( $userParameters,
                                                           array( 'policy_offset' => $policyOffset,
                                                                  'policy_sort'   => $policySort,
                                                                  'policy_dir'    => $policyDir ) ) );
        $tpl->setVariable( 'no_functions', $noFunctions );
        $tpl->setVariable( 'no_limitations', $noLimitations );

        $tpl->setVariable( 'show_modules', $showModules );
        $tpl->setVariable( 'show_limitations', $showLimitations );
        $tpl->setVariable( 'show_functions', $showFunctions );

        $tpl->setVariable( 'policies', $policies );
        // The policies of the page in words, by policy id; whether the draft differs from the saved role (null:
        // too many policies to compare); the saved role, for the page's links back to it
        $tpl->setVariable( 'policy_sentences', \expRolePage::describePolicies( $policies ) );
        $tpl->setVariable( 'draft_differs', \expRolePage::draftDiffers( $role ) );
        $tpl->setVariable( 'original_role_id', (int)$role->attribute( 'version' ) );
        $tpl->setVariable( 'policies_removed', $removedPolicies );
        $tpl->setVariable( 'policy_moved_to', $movedTo );
        $tpl->setVariable( 'modules', $modules );
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'role', $role );
        $tpl->setVariable( 'redirect_if_discarded', \eZRedirectManager::formReturnURI( $Module ) );

        $tpl->setVariable( 'step', 0 );

        $Module->setTitle( 'Edit ' . $role->attribute( 'name' ) );

        $Result = array();
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/role', 'Role list' ),
                                        'url' => 'role/list' ),
                                 array( 'text' => $role->attribute( 'name' ),
                                        'url' => false ) );

        $Result['content'] = $tpl->fetch( 'design:role/edit.tpl' );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     */
    protected function movePolicies( &$http, &$role, &$movedTo )
    {
        // The up and down buttons of the policy list. They are image buttons named
        // MovePolicyUp_<id> and MovePolicyDown_<id>, which eZHTTPTool turns into
        // MovePolicyUp=<id>. The move is made in the temporary version this page
        // edits, so Save keeps it and Cancel drops it; movePolicy() refuses a policy
        // that is not this role's.
        // Drag and drop, and the "Move to position" field: MovePolicyTo=<policy id> with MovePolicyPosition[<policy id>]
        // = the place in the whole list, 1 for the first. Made as up and down moves in this draft
        // (expRolePage::movePolicyTo()), so the order is the same one the buttons change: Save keeps it, Cancel drops it.
        $movedTo = false;
        if ( $http->hasPostVariable( 'MovePolicyTo' ) )
        {
            $moveID = $http->postVariable( 'MovePolicyTo' );
            $positions = $http->hasPostVariable( 'MovePolicyPosition' ) ? (array)$http->postVariable( 'MovePolicyPosition' ) : array();
            if ( self::isPolicyOf( $moveID, $role ) && isset( $positions[(int)$moveID] ) && is_scalar( $positions[(int)$moveID] )
                 && ctype_digit( (string)$positions[(int)$moveID] ) )
            {
                if ( \expRolePage::movePolicyTo( $role, (int)$moveID, (int)$positions[(int)$moveID] ) > 0 )
                {
                    $movedTo = (int)$positions[(int)$moveID];
                    // Set flag for audit. If true audit will be processed
                    $http->setSessionVariable( 'RoleWasChanged', true );
                }
            }
        }

        foreach ( array( 'MovePolicyUp' => 'up', 'MovePolicyDown' => 'down' ) as $movePostName => $moveDirection )
        {
            if ( $http->hasPostVariable( $movePostName ) )
            {
                if ( $role->movePolicy( (int)$http->postVariable( $movePostName ), $moveDirection ) )
                {
                    // Set flag for audit. If true audit will be processed
                    $http->setSessionVariable( 'RoleWasChanged', true );
                }
                break;
            }
        }

    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     */
    protected function removePolicies( &$http, &$role, &$policyID, &$removedPolicies )
    {
        // Removing works on the draft this page edits, and only on its policies: eZPolicy::removeByID() removes any
        // policy by its id, so an id of a saved role's policy posted here removed it from that role at once.
        $removedPolicies = false;
        if ( $http->hasPostVariable( 'RemovePolicy' ) )
        {
            $policyID = $http->postVariable( 'RolePolicy' ) ;
            \eZDebugSetting::writeDebug( 'kernel-role-edit', $policyID, 'trying to remove policy' );
            if ( self::isPolicyOf( $policyID, $role ) )
            {
                \eZPolicy::removeByID( $policyID );
                $removedPolicies = 1;
            }
            // Set flag for audit. If true audit will be processed
            $http->setSessionVariable( 'RoleWasChanged', true );
        }
        if ( $http->hasPostVariable( 'RemovePolicies' ) )
        {
            $removedPolicies = 0;
            $db = \eZDB::instance();
            $db->begin();
            foreach( (array)( $http->hasPostVariable( 'DeleteIDArray' ) ? $http->postVariable( 'DeleteIDArray' ) : array() ) as $deleteID )
            {
                \eZDebugSetting::writeDebug( 'kernel-role-edit', $deleteID, 'trying to remove policy' );
                if ( self::isPolicyOf( $deleteID, $role ) )
                {
                    \eZPolicy::removeByID( $deleteID );
                    $removedPolicies++;
                }
            }
            $db->commit();
            // Set flag for audit. If true audit will be processed
            if ( $removedPolicies )
                $http->setSessionVariable( 'RoleWasChanged', true );
        }
    }

    /**
     * Whether $policyID is a policy of $role (the draft this page edits).
     *
     * @param mixed $policyID
     * @param \eZRole $role
     * @return bool
     */
    public static function isPolicyOf( $policyID, $role )
    {
        if ( !is_scalar( $policyID ) || !ctype_digit( (string)$policyID ) || !$role instanceof \eZRole )
            return false;
        $policy = \eZPolicy::fetch( (int)$policyID );
        return $policy instanceof \eZPolicy && (int)$policy->attribute( 'role_id' ) === (int)$role->attribute( 'id' );
    }

    /**
     * Where Discard goes, after the draft of the role is removed: the page the form names in RedirectIfDiscarded
     * (the role's page, the list), else the page viewed last, else the list of roles, where it always went. The
     * rules are those of \eZRedirectManager::returnURI().
     *
     * @param \eZModule|null $module
     * @param array $options see \eZRedirectManager::returnURI()
     * @return string
     */
    public static function cancelURI( $module, $options = array() )
    {
        return \eZRedirectManager::returnURI( $module, '/role/list/', \eZRedirectManager::formReturnURIs(), $options );
    }

    /**
     * Records a role stored by Apply (doc/bc/6.0/audit.md): access.role.create or access.role.change, the parent
     * of one access.policy.add / access.policy.remove per policy that differs (compared by module, function and
     * limitations).
     *
     * @param int $roleID
     * @param array $before name, policies
     * @param bool $isNew
     */
    protected function auditRoleStored( $roleID, array $before, $isNew )
    {
        $role = \eZRole::fetch( $roleID );
        if ( !$role )
            return;
        $after = array( 'name' => (string)$role->attribute( 'name' ), 'policies' => \expAuditHook::policies( $role ) );
        $sig = function ( array $p ) {
            unset( $p['id'] );
            return json_encode( $p );
        };
        $old = array();
        foreach ( $before['policies'] as $p )
            $old[$sig( $p )] = $p;
        $new = array();
        foreach ( $after['policies'] as $p )
            $new[$sig( $p )] = $p;
        $added = array_diff_key( $new, $old );
        $removed = array_diff_key( $old, $new );
        if ( !$isNew && !$added && !$removed && $before['name'] === $after['name'] )
            return;
        $name = $isNew ? 'access.role.create' : 'access.role.change';
        $parent = \expAuditHook::begin( $name, array( 'object' => \expAuditHook::role( $role ),
                                                      'before' => $isNew ? null : $before, 'after' => $after ) );
        foreach ( $added as $p )
            \expAuditHook::emit( 'access.policy.add', array( 'parent' => $parent, 'object' => array( 'type' => 'policy', 'id' => $p['id'] ),
                                                             'target' => \expAuditHook::role( $role ), 'after' => $p ) );
        foreach ( $removed as $p )
            \expAuditHook::emit( 'access.policy.remove', array( 'parent' => $parent, 'object' => array( 'type' => 'policy', 'id' => $p['id'] ),
                                                                'target' => \expAuditHook::role( $role ), 'before' => $p ) );
        \expAuditHook::end( $parent, array( 'after' => array( 'policies_added' => count( $added ), 'policies_removed' => count( $removed ) ) ) );
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     */
    protected function applyRole( &$http, &$originalRole, &$role, &$Module )
    {
        if ( $http->hasPostVariable( 'Apply' ) )
        {
            $originalRole = \eZRole::fetch( $role->attribute( 'version' ) );
            $originalRoleName = $originalRole->attribute( 'name' );
            $originalRoleID = $originalRole->attribute( 'id' );

            // Who changes which role (doc/bc/6.0/audit.md): access.role.create for a new role, else access.role.change
            // with its name and policies before and after, and a child access.policy.add / access.policy.remove per
            // policy that differs
            $http->removeSessionVariable( 'RoleWasChanged' );
            $auditBefore = null;
            $auditNew = (bool)$originalRole->attribute( 'is_new' );
            if ( class_exists( 'expAuditHook' ) && ( \expAuditHook::on( $auditNew ? 'access.role.create' : 'access.role.change' ) ) )
                $auditBefore = array( 'name' => (string)$originalRoleName, 'policies' => \expAuditHook::policies( $originalRole ) );

            $originalRole->revertFromTemporaryVersion();

            if ( $auditBefore !== null )
                $this->auditRoleStored( $originalRoleID, $auditBefore, $auditNew );
            \eZContentCacheManager::clearAllContentCache();

            $Module->redirectTo( $Module->functionURI( 'view' ) . '/' . $originalRoleID . '/');

            /* Clean up policy cache */
            \eZUser::cleanupCache();
        }
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     */
    protected function addLimitation( &$http, &$policy, &$limitationList, &$limitation, &$limitationID, &$limitationIdentifier, &$nodeLimitationValues, &$currentModule, &$currentFunction, &$mod, &$functions, &$currentFunctionLimitations, &$functionLimitation, &$limitationValues, &$policyLimitation, &$limitationValue, &$roleID, &$db )
    {
        if ( $http->hasPostVariable( 'AddLimitation' ) )
        {
            $policy = false;

            if ( $http->hasSessionVariable( 'BrowsePolicyID' ) )
            {
                $hasNodeLimitation = false;
                $policy = \eZPolicy::fetch( $http->sessionVariable( 'BrowsePolicyID' ) );
                if ( $policy )
                {
                    $limitationList = \eZPolicyLimitation::fetchByPolicyID( $policy->attribute( 'id' ) );
                    foreach ( $limitationList as $limitation )
                    {
                        $limitationID = $limitation->attribute( 'id' );
                        $limitationIdentifier = $limitation->attribute( 'identifier' );
                        if ( $limitationIdentifier != 'Node' and $limitationIdentifier != 'Subtree' )
                            \eZPolicyLimitation::removeByID( $limitationID );
                        if ( $limitationIdentifier == 'Node' )
                        {
                            $nodeLimitationValues = \eZPolicyLimitationValue::fetchList( $limitationID );
                            if ( $nodeLimitationValues != null )
                                $hasNodeLimitation = true;
                            else
                                \eZPolicyLimitation::removeByID( $limitationID );
                        }

                        if ( $limitationIdentifier == 'Subtree' )
                        {
                            $nodeLimitationValues = \eZPolicyLimitationValue::fetchList( $limitationID );
                            if ( $nodeLimitationValues == null )
                                \eZPolicyLimitation::removeByID( $limitationID );
                        }
                    }

        //             if ( !$hasNodeLimitation )
                    {
                        $currentModule = $http->postVariable( 'CurrentModule' );
                        $currentFunction = $http->postVariable( 'CurrentFunction' );

                        $mod = \eZModule::exists( $currentModule );
                        $functions = $mod->attribute( 'available_functions' );
                        $currentFunctionLimitations = $functions[ $currentFunction ];
                        foreach ( $currentFunctionLimitations as $functionLimitation )
                        {
                            if ( $http->hasPostVariable( $functionLimitation['name'] ) and
                                 $functionLimitation['name'] != 'Node' and
                                 $functionLimitation['name'] != 'Subtree' )
                            {
                                $limitationValues = \eZPolicyLimitation::validValues( $functionLimitation, $http->postVariable( $functionLimitation['name'] ) );

                                if ( $limitationValues && !in_array( '-1', $limitationValues ) )
                                {
                                    $policyLimitation = \eZPolicyLimitation::createNew( $policy->attribute('id'), $functionLimitation['name'] );
                                    foreach ( $limitationValues as $limitationValue )
                                    {
                                        \eZPolicyLimitationValue::createNew( $policyLimitation->attribute( 'id' ), $limitationValue );
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ( !$policy )
            {
                $currentModule = $http->postVariable( 'CurrentModule' );
                $currentFunction = $http->postVariable( 'CurrentFunction' );
                $policy = \eZPolicy::createNew( $roleID, array( 'ModuleName'=> $currentModule,
                                                                'FunctionName' => $currentFunction,
                                                                'Limitation' => '' ) );

                $mod = \eZModule::exists( $currentModule );
                $functions = $mod->attribute( 'available_functions' );
                $currentFunctionLimitations = $functions[ $currentFunction ];
                \eZDebugSetting::writeDebug( 'kernel-role-edit', $currentFunctionLimitations, 'currentFunctionLimitations' );

                $db = \eZDB::instance();
                $db->begin();
                foreach ( $currentFunctionLimitations as $functionLimitation )
                {
                    if ( $http->hasPostVariable( $functionLimitation['name'] ) )
                    {
                        $limitationValues = \eZPolicyLimitation::validValues( $functionLimitation, $http->postVariable( $functionLimitation['name'] ) );
                        \eZDebugSetting::writeDebug( 'kernel-role-edit', $limitationValues, 'limitationValues' );

                        if ( $limitationValues && !in_array( '-1', $limitationValues ) )
                        {
                            $policyLimitation = \eZPolicyLimitation::createNew( $policy->attribute('id'), $functionLimitation['name'] );
                            foreach ( $limitationValues as $limitationValue )
                            {
                                \eZPolicyLimitationValue::createNew( $policyLimitation->attribute( 'id' ), $limitationValue );
                            }
                        }
                    }
                }
                $db->commit();
            }
        }
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     *
     * @return mixed what run() returns, or $this when run() goes on
     */
    protected function customFunction( &$http, &$currentModule, &$mod, &$functions, &$functionNames, &$showModules, &$showFunctions, &$showLimitations, &$noFunctions, &$tpl, &$Module, &$role, &$Result )
    {
        if ( $http->hasPostVariable( 'CustomFunction' ) )
        {
            if ( $http->hasPostVariable( 'Modules' ) )
                $currentModule = $http->postVariable( 'Modules' );
            else if ( $http->hasPostVariable( 'CurrentModule' ) )
                $currentModule = $http->postVariable( 'CurrentModule' );
            if ( $currentModule != '*' )
            {
                $mod = \eZModule::exists( $currentModule );
                $functions = $mod->attribute( 'available_functions' );
                $functionNames = array_keys( $functions );
            }
            else
            {
                $functionNames = array();
            }

            $showModules = false;
            $showFunctions = true;

            if ( count( $functionNames ) < 1 )
            {
                $showModules = true;
                $showFunctions = false;
                $showLimitations = false;
                $noFunctions = true;
            }

            $tpl->setVariable( 'current_module', $currentModule );
            $tpl->setVariable( 'functions', $functionNames );
            $tpl->setVariable( 'no_functions', $noFunctions );

            $Module->setTitle( 'Edit ' . $role->attribute( 'name' ) );
            $Result = array();

            $Result['path'] = array( array( 'url' => false ,
                                            'text' => \ezpI18n::tr( 'kernel/role',
                                                              'Create new policy, step 2: select function' ) ) );

            $Result['content'] = $tpl->fetch( 'design:role/createpolicystep2.tpl' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        return $this;
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     *
     * @return mixed what run() returns, or $this when run() goes on
     */
    protected function selectLimitationValues( &$http, &$db, &$currentModule, &$mod, &$functions, &$functionNames, &$showModules, &$showFunctions, &$showLimitations, &$policyID, &$nodeLimitationValues, &$currentFunction, &$currentFunctionLimitations, &$key, &$limitation, &$limitationValue, &$noLimitations, &$policy, &$limitationList, &$limitationID, &$limitationIdentifier, &$functionLimitation, &$limitationValues, &$policyLimitation, &$roleID, &$Module, &$Result, &$tpl )
    {
        if ( $http->hasPostVariable( 'SelectButton' ) or
             $http->hasPostVariable( 'BrowseCancelButton' ) or
             $http->hasPostVariable( 'Limitation' ) or
             $http->hasPostVariable( 'SelectedNodeIDArray' ) or
             $http->hasPostVariable( 'BrowseLimitationNodeButton' ) or
             $http->hasPostVariable( 'DeleteNodeButton' ) or
             $http->hasPostVariable( 'BrowseLimitationSubtreeButton' ) or
             $http->hasPostVariable( 'DeleteSubtreeButton' ) )
        {
            $db = \eZDB::instance();
            $db->begin();
            if ( $http->hasPostVariable( 'DeleteNodeButton' ) and $http->hasSessionVariable( 'BrowsePolicyID' ) )
            {
                if ( $http->hasPostVariable( 'DeleteNodeIDArray' ) )
                {
                    $deletedIDList = $http->postVariable( 'DeleteNodeIDArray' );

                    foreach ( $deletedIDList as $deletedID )
                    {
                        \eZPolicyLimitationValue::removeByValue( $deletedID, $http->sessionVariable( 'BrowsePolicyID' ) );
                    }
                }
            }

            if ( $http->hasPostVariable( 'DeleteSubtreeButton' ) and $http->hasSessionVariable( 'BrowsePolicyID' ) )
            {
                if ( $http->hasPostVariable( 'DeleteSubtreeIDArray' ) )
                {
                    $deletedIDList = $http->postVariable( 'DeleteSubtreeIDArray' );

                    foreach ( $deletedIDList as $deletedID )
                    {
                        $subtree = \eZContentObjectTreeNode::fetch( $deletedID , false, false);
                        $path = $subtree['path_string'];
                        \eZPolicyLimitationValue::removeByValue( $path, $http->sessionVariable( 'BrowsePolicyID' ) );
                    }
                }
            }

            if ( $http->hasPostVariable( 'Limitation' ) and $http->hasSessionVariable( 'BrowsePolicyID' ) )
                $http->removeSessionVariable( 'BrowsePolicyID' );

            if ( $http->hasSessionVariable( 'BrowseCurrentModule' ) )
                $currentModule = $http->sessionVariable( 'BrowseCurrentModule' );

            if ( $http->hasPostVariable( 'CurrentModule' ) )
                $currentModule = $http->postVariable( 'CurrentModule' );

            $mod = \eZModule::exists( $currentModule );
            $functions = $mod->attribute( 'available_functions' );
            $functionNames = array_keys( $functions );

            $showModules = false;
            $showFunctions = false;
            $showLimitations = true;
            $nodeList = array();
            $nodeIDList = array();
            $subtreeList = array();
            $subtreeIDList = array();

            // Check for temporary node and subtree policy limitation
            if ( $http->hasSessionVariable( 'BrowsePolicyID' ) )
            {
                $policyID = $http->sessionVariable( 'BrowsePolicyID' );
                // Fetch node limitations
                $nodeLimitation = \eZPolicyLimitation::fetchByIdentifier( $policyID, 'Node' );
                if ( $nodeLimitation != null )
                {
                    $nodeLimitationID = $nodeLimitation->attribute('id');
                    $nodeLimitationValues = \eZPolicyLimitationValue::fetchList( $nodeLimitationID );
                    foreach ( $nodeLimitationValues as $nodeLimitationValue )
                    {
                        $nodeID = $nodeLimitationValue->attribute( 'value' );
                        $nodeIDList[] = $nodeID;
                        $node = \eZContentObjectTreeNode::fetch( $nodeID );
                        $nodeList[] = $node;
                    }
                }

                // Fetch subtree limitations
                $subtreeLimitation = \eZPolicyLimitation::fetchByIdentifier( $policyID, 'Subtree' );
                if ( $subtreeLimitation != null )
                {
                    $subtreeLimitationID = $subtreeLimitation->attribute('id');
                    $subtreeLimitationValues = \eZPolicyLimitationValue::fetchList( $subtreeLimitationID );

                    foreach ( $subtreeLimitationValues as $subtreeLimitationValue )
                    {
                        $subtreePath = $subtreeLimitationValue->attribute( 'value' );
                        $subtreeObject = \eZContentObjectTreeNode::fetchByPath( $subtreePath );
                        if ( $subtreeObject )
                        {
                            $subtreeID = $subtreeObject->attribute( 'node_id' );
                            $subtreeIDList[] = $subtreeID;
                            $subtree = \eZContentObjectTreeNode::fetch( $subtreeID );
                            $subtreeList[] = $subtree;
                        }
                    }
                }
            }

            if ( $http->hasSessionVariable( 'BrowseCurrentFunction' ) )
                $currentFunction = $http->sessionVariable( 'BrowseCurrentFunction' );

            if ( $http->hasPostVariable( 'CurrentFunction' ) )
                $currentFunction = $http->postVariable( 'CurrentFunction' );

            if ( $http->hasPostVariable( 'ModuleFunction' ) )
                $currentFunction = $http->postVariable( 'ModuleFunction' );

            $currentFunctionLimitations = array();
            foreach( $functions[ $currentFunction ] as $key => $limitation )
            {
                if( is_array( $limitation ) && count( $limitation[ 'values' ] ) == 0 && array_key_exists( 'class', $limitation ) )
                {
                    $obj = new $limitation['class']( array() );
                    $limitationValueList = call_user_func_array ( array( $obj , $limitation['function']) , $limitation['parameter'] );
                    $limitationValueArray =  array();
                    foreach( $limitationValueList as $limitationValue )
                    {
                        $limitationValuePair = array();
                        $limitationValuePair['Name'] = $limitationValue[ 'name' ];
                        $limitationValuePair['value'] = $limitationValue[ 'id' ];
                        $limitationValueArray[] = $limitationValuePair;
                    }
                    $limitation[ 'values' ] = $limitationValueArray;
                }
                $currentFunctionLimitations[ $key ] = $limitation;
            }

            if ( count( $currentFunctionLimitations ) < 1 )
            {
                $showModules = false;
                $showFunctions = true;
                $showLimitations = false;
                $noLimitations = true;
            }


            if ( $http->hasPostVariable( 'BrowseLimitationSubtreeButton' ) ||
                 $http->hasPostVariable( 'BrowseLimitationNodeButton' ) )
            {
                // Store other limitations
                if ( $http->hasSessionVariable( 'BrowsePolicyID' ) )
                {
                    $policy = \eZPolicy::fetch( $http->sessionVariable( 'BrowsePolicyID' ) );
                    $limitationList = \eZPolicyLimitation::fetchByPolicyID( $policy->attribute( 'id' ) );
                    foreach ( $limitationList as $limitation )
                    {
                        $limitationID = $limitation->attribute( 'id' );
                        $limitationIdentifier = $limitation->attribute( 'identifier' );
                        if ( $limitationIdentifier != 'Node' and $limitationIdentifier != 'Subtree' )
                            \eZPolicyLimitation::removeByID( $limitationID );
                    }

                    foreach ( $currentFunctionLimitations as $functionLimitation )
                    {
                        if ( $http->hasPostVariable( $functionLimitation['name'] ) and
                             $functionLimitation['name'] != 'Node' and
                             $functionLimitation['name'] != 'Subtree' )
                        {
                            $limitationValues = \eZPolicyLimitation::validValues( $functionLimitation, $http->postVariable( $functionLimitation['name'] ) );
                            \eZDebugSetting::writeDebug( 'kernel-role-edit', $limitationValues, 'limitationValues');

                            if ( $limitationValues && !in_array( '-1', $limitationValues ) )
                            {
                                $policyLimitation = \eZPolicyLimitation::createNew( $policy->attribute('id'), $functionLimitation['name'] );
                                foreach ( $limitationValues as $limitationValue )
                                {
                                    \eZPolicyLimitationValue::createNew( $policyLimitation->attribute( 'id' ), $limitationValue );
                                }
                            }
                        }
                    }
                }
                else
                {
                    $policy = \eZPolicy::createNew( $roleID, array( 'ModuleName'=> $currentModule,
                                                                    'FunctionName' => $currentFunction,
                                                                    'Limitation' => '') );

                    $http->setSessionVariable( 'BrowsePolicyID', $policy->attribute('id') );
                    foreach ( $currentFunctionLimitations as $functionLimitation )
                    {
                        if ( $http->hasPostVariable( $functionLimitation['name'] ))
                        {
                            $limitationValues = \eZPolicyLimitation::validValues( $functionLimitation, $http->postVariable( $functionLimitation['name'] ) );
                            \eZDebugSetting::writeDebug( 'kernel-role-edit', $limitationValues, 'limitationValues');

                            if ( $limitationValues && !in_array( '-1', $limitationValues ) )
                            {
                                $policyLimitation = \eZPolicyLimitation::createNew( $policy->attribute('id'), $functionLimitation['name'] );
                                \eZDebugSetting::writeDebug( 'kernel-role-edit', $policyLimitation, 'policyLimitationCreated' );
                                foreach ( $limitationValues as $limitationValue )
                                {
                                    \eZPolicyLimitationValue::createNew( $policyLimitation->attribute( 'id' ), $limitationValue );
                                }
                            }
                        }
                    }
                }
                $db->commit();

                $http->setSessionVariable( 'BrowseCurrentModule', $currentModule );
                $http->setSessionVariable( 'BrowseCurrentFunction', $currentFunction );
                if ( $http->hasPostVariable( 'BrowseLimitationSubtreeButton' ) )
                {

                    \eZContentBrowse::browse( array( 'action_name' => 'FindLimitationSubtree',
                                                    'from_page' => '/role/edit/' . $roleID . '/' ),
                                             $Module );
                }
                elseif ( $http->hasPostVariable( 'BrowseLimitationNodeButton' ) )
                {
                    \eZContentBrowse::browse( array( 'action_name' => 'FindLimitationNode',
                                                    'from_page' => '/role/edit/' . $roleID . '/' ),
                                             $Module );

                }
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }

            if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) and
                 $http->postVariable( 'BrowseActionName' ) == 'FindLimitationNode' and
                 !$http->hasPostVariable( 'BrowseCancelButton' ) )
            {
                $selectedNodeIDList = $http->postVariable( 'SelectedNodeIDArray' );

                if ( $http->hasSessionVariable( 'BrowsePolicyID' ) )
                {
                    $policy = \eZPolicy::fetch( $http->sessionVariable( 'BrowsePolicyID' ) );
                    $limitationList = \eZPolicyLimitation::fetchByPolicyID( $policy->attribute( 'id' ) );

                    // Remove other limitations. When the policy is applied to node, no other constraints needed.
                    // Removes limitations only from a DropList if it is specified in the module.
                    if ( isset( $currentFunctionLimitations['Node']['DropList'] ) )
                    {
                        $dropList = $currentFunctionLimitations['Node']['DropList'];
                        foreach ( $limitationList as $limitation )
                        {
                            $limitationID = $limitation->attribute( 'id' );
                            $limitationIdentifier = $limitation->attribute( 'identifier' );
                            if ( in_array( $limitationIdentifier, $dropList ) )
                            {
                                \eZPolicyLimitation::removeByID( $limitationID );
                            }
                        }
                    }
                    else
                    {
                        foreach ( $limitationList as $limitation )
                        {
                            $limitationID = $limitation->attribute( 'id' );
                            $limitationIdentifier = $limitation->attribute( 'identifier' );
                            if ( $limitationIdentifier != 'Node' and $limitationIdentifier != 'Subtree' )
                                \eZPolicyLimitation::removeByID( $limitationID );
                        }
                    }
                }
                else
                {
                    $policy = \eZPolicy::createNew( $roleID, array( 'ModuleName'=> $currentModule,
                                                                   'FunctionName' => $currentFunction,
                                                                   'Limitation' => '') );
                    $http->setSessionVariable( 'BrowsePolicyID', $policy->attribute('id') );
                }

                $nodeLimitation = \eZPolicyLimitation::fetchByIdentifier( $policy->attribute('id'), 'Node' );
                if ( $nodeLimitation == null )
                    $nodeLimitation = \eZPolicyLimitation::createNew( $policy->attribute('id'), 'Node' );

                foreach ( $selectedNodeIDList as $nodeID )
                {
                    if ( !in_array( $nodeID, $nodeIDList ) )
                    {
                        $nodeLimitationValue = \eZPolicyLimitationValue::createNew( $nodeLimitation->attribute( 'id' ),  $nodeID );
                        $node = \eZContentObjectTreeNode::fetch( $nodeID );
                        $nodeList[] = $node;
                    }
                }
            }

            if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) and
                 $http->postVariable( 'BrowseActionName' ) == 'FindLimitationSubtree' and
                 !$http->hasPostVariable( 'BrowseCancelButton' ) )
            {
                $selectedSubtreeIDList = $http->postVariable( 'SelectedNodeIDArray' );
                if ( $http->hasSessionVariable( 'BrowsePolicyID' ) )
                {
                    $policy = \eZPolicy::fetch( $http->sessionVariable( 'BrowsePolicyID' ) );
                }
                else
                {
                    $policy = \eZPolicy::createNew( $roleID, array( 'ModuleName'=> $currentModule,
                                                                    'FunctionName' => $currentFunction,
                                                                    'Limitation' => '') );
                    $http->setSessionVariable( 'BrowsePolicyID', $policy->attribute('id') );
                }

                $subtreeLimitation = \eZPolicyLimitation::fetchByIdentifier( $policy->attribute('id'), 'Subtree' );
                if ( $subtreeLimitation == null )
                    $subtreeLimitation = \eZPolicyLimitation::createNew( $policy->attribute('id'), 'Subtree' );

                foreach ( $selectedSubtreeIDList as $nodeID )
                {
                    if ( !in_array( $nodeID, $subtreeIDList ) )
                    {
                        $subtree = \eZContentObjectTreeNode::fetch( $nodeID );
                        $pathString = $subtree->attribute( 'path_string' );
                        $policyLimitationValue = \eZPolicyLimitationValue::createNew( $subtreeLimitation->attribute( 'id' ),  $pathString );
                        $subtreeList[] = $subtree;
                    }
                }
            }

            if ( $http->hasPostVariable( 'Limitation' ) && count( $currentFunctionLimitations ) == 0 )
            {
                $currentModule = $http->postVariable( 'CurrentModule' );
                $currentFunction = $http->postVariable( 'ModuleFunction' );
                \eZDebugSetting::writeDebug( 'kernel-role-edit', $currentModule, 'currentModule' );
                $policy = \eZPolicy::createNew( $roleID, array( 'ModuleName'=> $currentModule,
                                                               'FunctionName' => $currentFunction ) );
            }
            else
            {
                $db->commit();

                $currentLimitationList = array();
                foreach ( $currentFunctionLimitations as $currentFunctionLimitation )
                {
                    $limitationName = $currentFunctionLimitation['name'];
                    $currentLimitationList[$limitationName] = '-1';
                }
                if ( isset( $policyID ) )
                {
                    $limitationList = \eZPolicyLimitation::fetchByPolicyID( $policyID );
                    foreach ( $limitationList as $limitation )
                    {
                        $limitationID = $limitation->attribute( 'id' );
                        $limitationIdentifier = $limitation->attribute( 'identifier' );
                        $limitationValues = \eZPolicyLimitationValue::fetchList( $limitationID );
                        $valueList = array();
                        foreach ( $limitationValues as $limitationValue )
                        {
                            $value = $limitationValue->attribute( 'value' );
                            $valueList[] = $value;
                        }
                        $currentLimitationList[$limitationIdentifier] = $valueList;
                    }
                }


                $tpl->setVariable( 'current_function', $currentFunction );
                $tpl->setVariable( 'function_limitations', $currentFunctionLimitations );
                $tpl->setVariable( 'no_limitations', $noLimitations );

                $tpl->setVariable( 'current_module', $currentModule );
                $tpl->setVariable( 'functions', $functionNames );
                $tpl->setVariable( 'node_list', $nodeList );
                $tpl->setVariable( 'subtree_list', $subtreeList );
                $tpl->setVariable( 'current_limitation_list', $currentLimitationList );

                $Result = array();
                $Result['path'] = array( array( 'url' => false ,
                                                'text' => \ezpI18n::tr( 'kernel/role',
                                                                  'Create new policy, step three: set function limitations' ) ) );

                $Result['content'] = $tpl->fetch( 'design:role/createpolicystep3.tpl' );
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }
            $db->commit();
        }

        return $this;
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     *
     * @return mixed what run() returns, or $this when run() goes on
     */
    protected function discardLimitation( &$http, &$currentModule, &$mod, &$functions, &$functionNames, &$showModules, &$showFunctions, &$tpl, &$Result )
    {
        if ( $http->hasPostVariable( 'DiscardLimitation' )  || $http->hasPostVariable( 'Step2')  )
        {
            $currentModule = $http->postVariable( 'CurrentModule' );
            $mod = \eZModule::exists( $currentModule );
            $functions = $mod->attribute( 'available_functions' );
            $functionNames = array_keys( $functions );

            $showModules = false;
            $showFunctions = true;
            $tpl->setVariable( 'current_module', $currentModule );
            $tpl->setVariable( 'functions', $functionNames );
            $tpl->setVariable( 'no_functions', false );

            $Result = array();
            $Result['path'] = array( array( 'url' => false ,
                                            'text' => \ezpI18n::tr( 'kernel/role',
                                                              'Create new policy, step two: select function' ) ) );

            $Result['content'] = $tpl->fetch( 'design:role/createpolicystep2.tpl' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        return $this;
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); run()'s variables are passed by reference.
     *
     * @return mixed what run() returns, or $this when run() goes on
     */
    protected function createPolicy( &$http, &$Module, &$role, &$tpl, &$modules, &$Result )
    {
        if ( $http->hasPostVariable( 'CreatePolicy' ) || $http->hasPostVariable( 'Step1' ) )
        {
            // Set flag for audit. If true audit will be processed
            $http->setSessionVariable( 'RoleWasChanged', true );
            $Module->setTitle( 'Edit ' . $role->attribute( 'name' ) );
            $tpl->setVariable( 'modules', $modules );

            $moduleList = array();
            foreach( $modules as $module )
            {
                $moduleList[] = \eZModule::exists( $module );
            }
            $tpl->setVariable( 'module_list', $moduleList );
            $tpl->setVariable( 'role', $role );
            $tpl->setVariable( 'module', $Module );

            $Result = array();
            $Result['path'] = array( array( 'url' => false ,
                                            'text' => \ezpI18n::tr( 'kernel/role',
                                                              'Create new policy, step one: select module' ) ) );

            $Result['content'] = $tpl->fetch( 'design:role/createpolicystep1.tpl' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        return $this;
    }
}

}
