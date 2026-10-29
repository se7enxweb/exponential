<?php
/**
 * The code of bin/php/adduserapproval.php.
 * @description Set up the approval of new accounts in a user group (default: new Editors wait for the Administrator users): the workflow event on content/publish before and the role that lets the requesters create such accounts; --mode=section sets up the optional alternative, a section Staff with the plain approval event (both a dry run unless --apply)
 * Guide: doc/eng-us/bc/6.0/user-account-approval.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Adduserapproval extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Set up the approval of new user accounts: the workflow \"" . \expUserApproval::WORKFLOW_NAME . "\" with the event\n"
                    . "\"Approve user accounts\" on content/publish before (or the event first in the workflow that trigger runs\n"
                    . "already), and the role \"" . \expUserApproval::ROLE_NAME . "\" that lets the requesting groups create accounts in\n"
                    . "the groups that require approval. By default an editor who creates an account in Editors waits for the\n"
                    . "Administrator users, who get a mail with the link to the approval item. What exists is left alone, so the\n"
                    . "script can run again. Groups are remote:<remote id>, node:<node id> or name:<top-level group name>.\n"
                    . "Prints PASS or FAIL at the end. Without --apply nothing is written, in either mode: the script lists\n"
                    . "what it would create and change. --dry-run is still accepted and means the same as no flag; --apply\n"
                    . "together with --dry-run is a usage error (exit 2).\n\n"
                    . "--mode=section sets up the optional alternative instead, with the plain approval event: the section\n"
                    . "\"" . \expUserApprovalSection::SECTION_NAME . "\" for Administrator users and Editors, and the approval event (section Staff,\n"
                    . "approvers and the group without approval Administrator users, new objects only) on content/publish\n"
                    . "before. It refuses while the event \"Approve user accounts\" runs on that trigger: choose one of the\n"
                    . "two, never both.\n\n"
                    . "Example: php bin/php/adduserapproval.php\n"
                    . "         php bin/php/adduserapproval.php --apply\n"
                    . "         php bin/php/adduserapproval.php --apply --required=name:Partners --approvers=name:Partner\\ desk --no-role\n"
                    . "         php bin/php/adduserapproval.php --mode=section            (dry run)\n"
                    . "         php bin/php/adduserapproval.php --mode=section --apply",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[apply][dry-run][mode:][staff:*][exempt:*][required:*][approvers:*][requesters:*][no-role][admin-id:]",
            '',
            array(
                'apply' => "Make the changes, in either mode (the default is a dry run that lists them and changes nothing)",
                'dry-run' => "The default, kept for scripts that pass it: list what would be created and changed, change nothing",
                'mode' => "group (default: the event \"Approve user accounts\") or section (the section Staff and the plain approval event)",
                'staff' => "--mode=section: a group whose accounts go into the section Staff (default Administrator users and Editors), may be repeated",
                'exempt' => "--mode=section: a group whose members need no approval (default Administrator users), may be repeated",
                'required' => "A group whose new accounts need approval (default Editors), may be repeated",
                'approvers' => "A group whose members approve (default Administrator users), may be repeated",
                'requesters' => "A group that may create accounts in the required groups and waits for approval (default Editors), may be repeated",
                'no-role' => "Do not create or assign the requesters' role (the requesters have their policies already)",
                'admin-id' => "The user the workflow is created by (default 14)"
            )
        );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;
        $mode = $options['mode'] ? strtolower( (string)$options['mode'] ) : 'group';
        if ( !in_array( $mode, array( 'group', 'section' ), true ) )
        {
            $cli->error( "FAIL: --mode is group or section, not '$mode'" );
            $script->shutdown( 1 );
        }
        if ( $mode === 'section' )
        {
            $this->runSection( $cli, $script, $options, $dryRun );
            return;
        }
        $planOptions = array( 'role' => !$options['no-role'] );
        foreach ( array( 'required', 'approvers', 'requesters' ) as $name )
        {
            if ( $options[$name] )
                $planOptions[$name] = array_map( 'strval', (array)$options[$name] );
        }
        $plan = \expUserApproval::plan( $planOptions );
        foreach ( $plan['required_groups'] as $group )
            $cli->output( sprintf( 'requires approval  %s (node %d, object %d)', $group['name'], $group['node_id'], $group['object_id'] ) );
        foreach ( $plan['approver_groups'] as $group )
            $cli->output( sprintf( 'approves           %s (node %d, object %d)', $group['name'], $group['node_id'], $group['object_id'] ) );
        foreach ( $plan['requester_groups'] as $group )
            $cli->output( sprintf( 'requests           %s (node %d, object %d)', $group['name'], $group['node_id'], $group['object_id'] ) );
        if ( $plan['errors'] )
        {
            foreach ( $plan['errors'] as $error )
                $cli->error( "FAIL: $error" );
            $script->shutdown( 1 );
        }

        $sectionEvent = \expUserApprovalSection::eventOnTrigger();
        if ( $sectionEvent )
            $cli->warning( 'WARNING: the section based approval (approval event ' . (int)$sectionEvent->attribute( 'id' ) . ', section '
                           . \expUserApprovalSection::SECTION_NAME . ') runs on content/publish before too: a new account in both would be '
                           . 'asked for twice. Keep one of the two (doc/eng-us/bc/6.0/user-account-approval.md).' );

        $changes = \expUserApproval::changes( $plan );
        $verb = function ( $what ) use ( $dryRun ) {
            return $dryRun ? "would $what" : $what;
        };
        $target = $changes['target_workflow'];
        $lines = array();
        if ( $changes['workflow'] === 'create' )
            $lines[] = $verb( 'create' ) . ' workflow "' . \expUserApproval::WORKFLOW_NAME . '"';
        if ( $changes['trigger'] === 'create' )
            $lines[] = $verb( 'create' ) . ' trigger content/publish/before';
        else if ( $changes['trigger'] === 'add_event' )
            $lines[] = 'content/publish/before runs the workflow "' . ( $target ? $target->attribute( 'name' ) : '?' ) . '": the event goes first into it';
        if ( $changes['event'] === 'create' )
            $lines[] = $verb( 'add' ) . ' event "Approve user accounts"';
        else if ( $changes['event'] === 'update' )
            $lines[] = $verb( 'add' ) . ' the groups to the event "Approve user accounts" that is there';
        if ( $changes['role'] === 'create' )
            $lines[] = $verb( 'create' ) . ' role "' . \expUserApproval::ROLE_NAME . '"';
        foreach ( $changes['add'] as $policy )
            $lines[] = $verb( 'add' ) . ' policy ' . \expMemberManagement::describe( $policy );
        foreach ( $changes['assign'] as $group )
            $lines[] = $verb( 'assign' ) . " the role to {$group['name']} (no limitation)";
        $count = count( $lines ) - ( $changes['trigger'] === 'add_event' ? 1 : 0 );
        if ( !$lines )
            $lines[] = 'has everything: workflow, trigger, event' . ( $plan['role'] ? ', role, policies, assignments' : '' );
        foreach ( $lines as $line )
            $cli->output( '  ' . $line );

        if ( $dryRun )
        {
            $cli->output( "PASS: $count change(s) would be made (dry run, nothing changed; --apply writes)" );
            // shutdown() without an exit code does not end the script
            $script->shutdown();
            return;
        }
        \expUserApproval::apply( $plan, $options['admin-id'] ? (int)$options['admin-id'] : 14 );
        // Check what was stored
        $after = \expUserApproval::changes( $plan );
        $incomplete = array();
        if ( $after['trigger'] === 'create' )
            $incomplete[] = 'no trigger';
        if ( $after['event'] !== 'has' )
            $incomplete[] = 'the event is missing or lacks groups';
        if ( $plan['role'] && ( $after['role'] !== 'has' || $after['add'] || $after['assign'] ) )
            $incomplete[] = 'the role is not complete';
        if ( $incomplete )
        {
            $cli->error( 'FAIL: ' . implode( '; ', $incomplete ) );
            $script->shutdown( 1 );
        }
        $cli->output( 'PASS: the approval of new accounts is set up; on Velocity restart the workers (exp:velocity deploy --kernel)' );
        $script->shutdown();
    }

    /**
     * --mode=section: the section Staff and the plain approval event; a dry run unless --apply.
     *
     * @param bool $dryRun true without --apply: list the changes, make none
     */
    protected function runSection( $cli, $script, array $options, $dryRun )
    {
        $planOptions = array();
        foreach ( array( 'staff', 'approvers', 'exempt' ) as $name )
        {
            if ( $options[$name] )
                $planOptions[$name] = array_map( 'strval', (array)$options[$name] );
        }
        $plan = \expUserApprovalSection::plan( $planOptions );
        foreach ( $plan['staff_groups'] as $group )
            $cli->output( sprintf( 'section %-8s  %s (node %d, object %d)', \expUserApprovalSection::SECTION_NAME, $group['name'], $group['node_id'], $group['object_id'] ) );
        foreach ( $plan['approver_groups'] as $group )
            $cli->output( sprintf( 'approves          %s (node %d, object %d)', $group['name'], $group['node_id'], $group['object_id'] ) );
        foreach ( $plan['exempt_groups'] as $group )
            $cli->output( sprintf( 'no approval       %s (node %d, object %d)', $group['name'], $group['node_id'], $group['object_id'] ) );
        if ( $plan['errors'] )
        {
            foreach ( $plan['errors'] as $error )
                $cli->error( "FAIL: $error" );
            $script->shutdown( 1 );
        }
        foreach ( $plan['warnings'] as $warning )
            $cli->warning( 'check: ' . $warning . ' (the staff accounts leave that section)' );

        $changes = \expUserApprovalSection::changes( $plan );
        $verb = function ( $what ) use ( $dryRun ) {
            return $dryRun ? "would $what" : $what;
        };
        $lines = array();
        if ( $changes['section'] === 'create' )
            $lines[] = $verb( 'create' ) . ' section "' . \expUserApprovalSection::SECTION_NAME . '" (identifier ' . \expUserApprovalSection::SECTION_IDENTIFIER . ')';
        foreach ( $changes['assign'] as $assign )
            $lines[] = $verb( 'assign' ) . ' the section to ' . $assign['group']['name'] . ' and below (' . $assign['objects'] . ' object(s))';
        if ( $changes['workflow'] === 'create' )
            $lines[] = $verb( 'create' ) . ' workflow "' . \expUserApprovalSection::WORKFLOW_NAME . '"';
        if ( $changes['trigger'] === 'create' )
            $lines[] = $verb( 'create' ) . ' trigger content/publish/before';
        $target = $changes['target_workflow'];
        if ( $changes['trigger'] === 'add_event' && $changes['event'] === 'create' )
            $lines[] = $verb( 'add' ) . ' the approval event first into the workflow "' . ( $target ? $target->attribute( 'name' ) : '?' ) . '" that content/publish/before runs';
        else if ( $changes['event'] === 'create' )
            $lines[] = $verb( 'add' ) . ' the approval event (section ' . \expUserApprovalSection::SECTION_NAME . ', new objects only)';
        else if ( $changes['event'] === 'update' )
            $lines[] = $verb( 'add' ) . ' the groups to the approval event that is there';
        $count = count( $lines );
        if ( !$lines )
            $lines[] = 'has everything: section, assignments, trigger, approval event';
        foreach ( $lines as $line )
            $cli->output( '  ' . $line );
        if ( $dryRun )
        {
            $cli->output( "PASS: $count change(s) would be made (dry run, nothing changed; --apply writes)" );
            $script->shutdown();
            return;
        }
        \expUserApprovalSection::apply( $plan, $options['admin-id'] ? (int)$options['admin-id'] : 14 );
        $after = \expUserApprovalSection::changes( $plan );
        $incomplete = array();
        if ( $after['section'] !== 'has' )
            $incomplete[] = 'no section';
        if ( $after['assign'] )
            $incomplete[] = 'objects of the groups are not in the section';
        if ( $after['trigger'] === 'create' || $after['event'] !== 'has' )
            $incomplete[] = 'the approval event is missing on content/publish before';
        if ( $incomplete )
        {
            $cli->error( 'FAIL: ' . implode( '; ', $incomplete ) );
            $script->shutdown( 1 );
        }
        $cli->output( 'PASS: the section based approval of new staff accounts is set up; on Velocity restart the workers (exp:velocity deploy --kernel)' );
        $script->shutdown();
    }
}

}
