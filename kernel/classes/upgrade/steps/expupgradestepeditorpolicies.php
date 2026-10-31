<?php
/**
 * File containing the expUpgradeStepEditorPolicies class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The everyday policies of the Editor role, through expEditorPolicies as bin/php/addeditorpolicies.php --apply with
 * its default options: a content/* without limitations is replaced by the single content functions, then the missing
 * policies are added (the UI switch among them), then the class sets of editorpolicies.ini and the removal of duplicate
 * policies. Not applicable without a role Editor.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepEditorPolicies extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-editor-policies';
    }

    public function description()
    {
        return 'The everyday policies of the Editor role (bin/php/addeditorpolicies.php)';
    }

    protected function changes( expUpgradeContext $context )
    {
        $role = self::role( expEditorPolicies::ROLE_NAME );
        if ( !$role )
        {
            $this->notApplicable = true;
            $this->reason = 'no role ' . expEditorPolicies::ROLE_NAME;
            return null;
        }
        $lines = array();
        $policies = expEditorPolicies::policiesOf( $role );
        $narrow = expEditorPolicies::narrowPlan( $policies, array() );
        if ( $narrow['action'] === 'narrow' )
        {
            $lines[] = 'replace content/* by ' . count( $narrow['add'] ) . ' content functions: ' . $narrow['why'];
            $kept = array();
            foreach ( $policies as $policy )
                if ( !( $policy['module'] === 'content' && $policy['function'] === '*' && empty( $policy['limited'] ) ) )
                    $kept[] = $policy;
            foreach ( $narrow['add'] as $function )
                $kept[] = array( 'module' => 'content', 'function' => $function, 'limited' => false );
            $policies = $kept;
        }
        foreach ( expEditorPolicies::plan( $policies, expEditorPolicies::availableModules(), array(), array(), expEditorPolicies::heldAlongside( $role ) ) as $entry )
            if ( $entry['action'] === 'grant' )
                $lines[] = "add {$entry['module']}/{$entry['function']}: {$entry['reason']}";
        // the class sets of editorpolicies.ini and the duplicates, without writing
        $more = expEditorPolicies::completeClassesAndDuplicates( $role, false );
        return array_merge( $lines, $more['lines'] );
    }

    protected function write( expUpgradeContext $context )
    {
        $role = self::role( expEditorPolicies::ROLE_NAME );
        $done = array();
        $narrow = expEditorPolicies::narrowPlan( expEditorPolicies::policiesOf( $role ), array() );
        if ( $narrow['action'] === 'narrow' )
        {
            expEditorPolicies::narrow( $role, $narrow );
            $role = eZRole::fetch( (int)$role->attribute( 'id' ) );
            $done[] = 'replaced content/* by ' . count( $narrow['add'] ) . ' content functions';
        }
        foreach ( expEditorPolicies::plan( expEditorPolicies::policiesOf( $role ), expEditorPolicies::availableModules(), array(), array(), expEditorPolicies::heldAlongside( $role ) ) as $entry )
        {
            if ( $entry['action'] !== 'grant' )
                continue;
            expEditorPolicies::apply( $role, $entry );
            $done[] = "added {$entry['module']}/{$entry['function']}";
        }
        eZRole::expireCache();
        $more = expEditorPolicies::completeClassesAndDuplicates( eZRole::fetch( (int)$role->attribute( 'id' ) ), true );
        $done = array_merge( $done, $more['lines'] );
        foreach ( expEditorPolicies::overGrants( expEditorPolicies::policiesOf( eZRole::fetch( (int)$role->attribute( 'id' ) ) ) ) as $over )
            $done[] = sprintf( 'WARN %s/%s granted by %s: %s', $over['module'], $over['function'], $over['by'], $over['reason'] );
        return $done;
    }
}
