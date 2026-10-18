<?php
/**
 * The code of bin/php/addexphelpreadpolicy.php.
 * @description Grant exphelp/read (the help system) to the Editor role or the roles named (--dry-run lists them first)
 * @alias help-grant
 * Guide: doc/features/6.0/help-system.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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
                    . "are left alone; the script can run again.\n\n"
                    . "Example: php bin/php/addexphelpreadpolicy.php --dry-run\n"
                    . "         php bin/php/addexphelpreadpolicy.php --role=Editor --role=5",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[dry-run][role:*]",
            '',
            array(
                'dry-run' => "List what would be granted, change nothing",
                'role' => "This role (ID or name), may be repeated; the Editor role when none is named"
            )
        );

        $dryRun = (bool)$options['dry-run'];
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
            \eZRole::expireCache();

        $cli->output();
        $cli->output( $dryRun ? "$granted role(s) would get exphelp/read (dry run, nothing changed)"
                              : "$granted role(s) got exphelp/read" );
        $script->shutdown();
    }
}

}
