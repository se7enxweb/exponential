<?php
/**
 * The code of bin/php/addmembermanagement.php.
 * @description Set up member management from membermanagement.ini: the role that lets a group (default Editors) manage the accounts of another (default Members), never Administrator users or Editors (dry run unless --apply)
 * Guide: doc/eng-us/bc/6.0/member-management.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Addmembermanagement extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Set up member management as membermanagement.ini describes it: for each rule, the role with the\n"
                    . "rule's policy set (each content policy limited to the managed groups), assigned to the manager groups.\n"
                    . "The defaults give the Editors group the role \"Member management\" over Members. A rule whose managed\n"
                    . "groups reach a protected group (default Administrator users and Editors) is refused. What a role already\n"
                    . "has is left alone, so the script can run again. Prints PASS or FAIL at the end.\n"
                    . "Without --apply nothing is written: the script lists what it would create, add and assign. --dry-run\n"
                    . "is still accepted and means the same as no flag; --apply together with --dry-run is a usage error (exit 2).\n\n"
                    . "Example: php bin/php/addmembermanagement.php\n"
                    . "         php bin/php/addmembermanagement.php --apply\n"
                    . "         php bin/php/addmembermanagement.php --apply --rule=editors_manage_members",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[apply][dry-run][rule:*]",
            '',
            array(
                'apply' => "Write the changes (the default is a dry run that lists them and changes nothing)",
                'dry-run' => "The default, kept for scripts that pass it: list what would be created, added and assigned; change nothing",
                'rule' => "Only this rule ([MemberRule_<name>]), may be repeated"
            )
        );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;
        $only = $options['rule'] ? array_map( 'strval', (array)$options['rule'] ) : array();

        if ( !\expMemberManagement::enabled() )
        {
            $cli->output( 'PASS: member management is disabled ([MemberManagementSettings] MemberManagement), nothing to do' );
            $script->shutdown();
        }
        $rules = \expMemberManagement::rules();
        foreach ( $only as $name )
        {
            if ( !isset( $rules[$name] ) )
            {
                $cli->error( "FAIL: no rule '$name' in [MemberManagementSettings] Rules[]; rules: " . implode( ', ', array_keys( $rules ) ) );
                $script->shutdown( 1 );
            }
        }
        $settings = \expMemberManagement::guardSettings();
        $cli->output( 'guard: ' . ( $settings['guard'] ? 'enabled for ' . implode( ', ', $settings['functions'] ) : 'disabled' )
                      . '; user/setting needs content/edit: ' . ( $settings['setting_requires_edit'] ? 'yes' : 'no' ) );

        $failed = 0;
        $changed = 0;
        foreach ( $rules as $name => $rule )
        {
            if ( $only && !in_array( $name, $only, true ) )
                continue;
            $rule = \expMemberManagement::prepare( $rule );
            $cli->output();
            $cli->output( "rule $name: role \"{$rule['role']}\"" );
            foreach ( $rule['manager_groups'] as $group )
                $cli->output( sprintf( '  manager group  %s (node %d)', $group['name'], $group['node_id'] ) );
            foreach ( $rule['managed_groups'] as $group )
                $cli->output( sprintf( '  managed group  %s (node %d, %s)', $group['name'], $group['node_id'], $group['path_string'] ) );
            foreach ( $rule['protected_groups'] as $group )
                $cli->output( sprintf( '  protected      %s (node %d)', $group['name'], $group['node_id'] ) );
            if ( $rule['errors'] )
            {
                foreach ( $rule['errors'] as $error )
                    $cli->error( "  FAIL: $error" );
                $failed++;
                continue;
            }
            $changes = \expMemberManagement::changes( $rule );
            if ( $changes['create_role'] )
                $cli->output( ( $dryRun ? '  would create ' : '  create       ' ) . "role \"{$rule['role']}\"" );
            foreach ( $rule['planned'] as $policy )
            {
                $missing = in_array( \expMemberManagement::key( $policy ), array_map( array( '\expMemberManagement', 'key' ), $changes['add'] ), true );
                $cli->output( ( $missing ? ( $dryRun ? '  would add    ' : '  add          ' ) : '  has          ' ) . \expMemberManagement::describe( $policy ) );
            }
            foreach ( $changes['assign'] as $group )
                $cli->output( ( $dryRun ? '  would assign ' : '  assign       ' ) . "to {$group['name']} (no limitation)" );
            if ( !$dryRun )
            {
                \expMemberManagement::apply( $rule );
                // Check what was stored
                $after = \expMemberManagement::changes( $rule );
                if ( $after['create_role'] || $after['add'] || $after['assign'] )
                {
                    $cli->error( "  FAIL: the role \"{$rule['role']}\" is not complete after applying the rule" );
                    $failed++;
                    continue;
                }
            }
            $changed += (int)$changes['create_role'] + count( $changes['add'] ) + count( $changes['assign'] );
        }

        $cli->output();
        if ( $failed )
        {
            $cli->error( "FAIL: $failed rule(s) could not be applied" );
            $script->shutdown( 1 );
        }
        $cli->output( $dryRun ? "PASS: $changed change(s) would be made (dry run, nothing changed; --apply writes)" : "PASS: $changed change(s) made" );
        $script->shutdown();
    }
}

}
