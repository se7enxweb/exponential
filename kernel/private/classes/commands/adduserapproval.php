<?php
/**
 * The code of bin/php/adduserapproval.php.
 * @description Set up the approval of new accounts in a user group (default: new Editors wait for the Administrator users): the workflow event on content/publish before and the role that lets the requesters create such accounts (--dry-run lists it first)
 * Guide: doc/eng-us/bc/6.0/user-account-approval.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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
                    . "Prints PASS or FAIL at the end.\n\n"
                    . "Example: php bin/php/adduserapproval.php --dry-run\n"
                    . "         php bin/php/adduserapproval.php\n"
                    . "         php bin/php/adduserapproval.php --required=name:Partners --approvers=name:Partner\\ desk --no-role",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[dry-run][required:*][approvers:*][requesters:*][no-role][admin-id:]",
            '',
            array(
                'dry-run' => "List what would be created and changed; change nothing",
                'required' => "A group whose new accounts need approval (default Editors), may be repeated",
                'approvers' => "A group whose members approve (default Administrator users), may be repeated",
                'requesters' => "A group that may create accounts in the required groups and waits for approval (default Editors), may be repeated",
                'no-role' => "Do not create or assign the requesters' role (the requesters have their policies already)",
                'admin-id' => "The user the workflow is created by (default 14)"
            )
        );
        $dryRun = (bool)$options['dry-run'];
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
            $cli->output( "PASS: $count change(s) would be made (dry run, nothing changed)" );
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
}

}
