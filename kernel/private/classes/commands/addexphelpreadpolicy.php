<?php
/**
 * The code of bin/php/addexphelpreadpolicy.php.
 * @description Grant exphelp/read (the help system) to the Editor role or the roles named (dry run unless --apply)
 * @alias help-grant
 * Guide: doc/eng-us/features/6.0/help-system.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Addexphelpreadpolicy extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Grant the policy exphelp/read, reading the help system, to the Editor role or to the roles\n"
                    . "named with --role, so editors see the Help control and the help pages. New installations give it\n"
                    . "to the Editor role; administrators have it through their */* policy. Roles that have it already\n"
                    . "are left alone; the script can run again. Prints PASS or FAIL at the end.\n"
                    . "Without --apply nothing is written: the script lists the roles it would grant the policy to.\n"
                    . "--dry-run is still accepted and means the same as no flag; --apply together with --dry-run is a\n"
                    . "usage error (exit 2).\n\n"
                    . "Example: php bin/php/addexphelpreadpolicy.php\n"
                    . "         php bin/php/addexphelpreadpolicy.php --apply\n"
                    . "         php bin/php/addexphelpreadpolicy.php --apply --role=Editor --role=5",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[apply][dry-run][role:*]",
            '',
            array(
                'apply' => "Write the changes (the default is a dry run that lists them and changes nothing)",
                'dry-run' => "The default, kept for scripts that pass it: list what would be granted, change nothing",
                'role' => "This role (ID or name), may be repeated; the Editor role when none is named"
            )
        );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;
        $onlyRoles = $options['role'] ? (array)$options['role'] : array();

        $roles = array();
        $roleData = array();
        foreach ( \eZRole::fetchList() as $role )
        {
            $roles[(int)$role->attribute( 'id' )] = $role;
            $roleData[] = \expHelpReadPolicyGrant::roleData( $role );
        }

        $known = array();
        $granted = 0;
        foreach ( \expHelpReadPolicyGrant::plan( $roleData, $onlyRoles ) as $entry )
        {
            $known[] = (string)$entry['id'];
            $known[] = $entry['name'];
            if ( $entry['reason'] === 'not listed' )
                continue;
            $label = sprintf( "%-30s (%d)", $entry['name'], $entry['id'] );
            if ( $entry['action'] !== 'grant' )
            {
                $cli->output( "skip   $label: {$entry['reason']}" );
                continue;
            }
            $cli->output( ( $dryRun ? 'would grant' : 'grant' ) . " $label: exphelp/read" );
            if ( !$dryRun )
                \expHelpReadPolicyGrant::apply( $roles[$entry['id']], $entry );
            $granted++;
        }

        foreach ( $onlyRoles ? $onlyRoles : array( \expHelpReadPolicyGrant::DEFAULT_ROLE ) as $name )
        {
            if ( !in_array( (string)$name, $known, true ) )
                $cli->warning( "No role '$name'" );
        }

        if ( $granted && !$dryRun )
        {
            \eZRole::expireCache();
            // Check what was stored: no listed role lacks the policy now.
            $after = array();
            foreach ( \eZRole::fetchList() as $role )
                $after[] = \expHelpReadPolicyGrant::roleData( $role );
            foreach ( \expHelpReadPolicyGrant::plan( $after, $onlyRoles ) as $entry )
            {
                if ( $entry['action'] === 'grant' )
                {
                    $cli->error( "FAIL: {$entry['name']} ({$entry['id']}) still lacks exphelp/read" );
                    $script->shutdown( 1 );
                }
            }
        }

        $cli->output();
        $cli->output( $dryRun ? "PASS: $granted role(s) would get exphelp/read (dry run, nothing changed; --apply writes)"
                              : "PASS: $granted role(s) got exphelp/read" );
        $script->shutdown();
    }
}

}
