<?php
/**
 * The code of bin/php/cleanuppolicies.php, moved into a class (#207 stage 1). The file bin/php/cleanuppolicies.php is one call to it.
 * @description Remove policies of modules that no longer exist (dry run unless --apply)
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/cleanuppolicies.php:
 *
 *
 * File containing the script to cleanup from database policies defined on module which do not exist in a modules folder
 * according to settings from module.ini/[ModuleSettings]/ExtensionRepositories
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package
 *
 */

namespace Exponential\Command\Kernel
{

class Cleanuppolicies extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();

        $script = $this->script(
            array(
                'description' => "Remove from database policies defined on module which do not exist in a modules folder\n"
                    . "according to settings from module.ini/[ModuleSettings]/ExtensionRepositories.\n"
                    . "Without --apply nothing is written: the script lists the policies it would remove. --dry-run is\n"
                    . "still accepted and means the same as no flag; --apply together with --dry-run is a usage error\n"
                    . "(exit 2). With --apply the script waits 10 seconds before it removes anything (-n: do not wait)\n"
                    . "and checks afterwards that none is left. Prints PASS or FAIL at the end.\n\n"
                    . "Example: ./bin/php/cleanuppolicies.php\n"
                    . "         ./bin/php/cleanuppolicies.php --apply -n",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[apply][dry-run][n]",
            '',
            array(
                'apply' => "Remove the policies (the default is a dry run that lists them and changes nothing)",
                'dry-run' => "The default, kept for scripts that pass it: list the policies that would be removed, change nothing",
                'n' => "With --apply: do not wait 10 seconds before removing"
            )
        );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;

        $modules = \eZModule::globalPathList();
        $orphans = self::orphanPolicies( $modules );
        $cli->output( self::policyCount() . ' policies checked' );
        foreach ( $orphans as $policy )
        {
            $cli->output( sprintf( '%s policy %d of role %d: module \'%s\' does not exist',
                                   $dryRun ? 'would remove' : 'remove      ',
                                   $policy->attribute( 'id' ), $policy->attribute( 'role_id' ), $policy->attribute( 'module_name' ) ) );
        }

        if ( $orphans && !$dryRun )
        {
            if ( !$options['n'] )
            {
                $cli->warning( "This cleanup script is going to remove the policies listed above." );
                $cli->warning( "You have 10 seconds to break the script (press Ctrl-C)" );
                sleep( 10 );
            }
            foreach ( $orphans as $policy )
            {
                if ( !$dryRun )
                    $policy->removeThis();
            }
            \eZRole::expireCache();
            // Check what was stored: no policy of a missing module is left.
            $left = count( self::orphanPolicies( $modules ) );
            if ( $left )
            {
                $cli->error( "FAIL: $left polic" . ( $left === 1 ? 'y' : 'ies' ) . " of missing modules still there" );
                $script->shutdown( 1 );
            }
        }

        $count = count( $orphans );
        $cli->output();
        if ( $dryRun )
            $cli->output( "PASS: $count polic" . ( $count === 1 ? 'y' : 'ies' ) . " would be removed (dry run, nothing changed; --apply writes)" );
        else
            $cli->output( "PASS: $count polic" . ( $count === 1 ? 'y' : 'ies' ) . " removed" );
        $script->shutdown();
    }

    /** @return int the number of policies */
    protected static function policyCount()
    {
        $rows = \eZPersistentObject::fetchObjectList( \eZPolicy::definition(), array(), null, false, null, false, false,
                                                     array( array( 'operation' => 'count( * )', 'name' => 'count' ) ) );
        return (int)$rows[0]['count'];
    }

    /**
     * The policies whose module is in none of the module directories, read in pages of 100 by ID; nothing is changed.
     *
     * @param array $modules the module directories, eZModule::globalPathList()
     * @return \eZPolicy[]
     */
    public static function orphanPolicies( array $modules )
    {
        $orphans = array();
        $offset = 0;
        while ( true )
        {
            $policies = \eZPersistentObject::fetchObjectList( \eZPolicy::definition(), null, null, array( 'id' => 'asc' ),
                                                             array( 'offset' => $offset, 'length' => 100 ), true );
            if ( empty( $policies ) )
                break;
            foreach ( $policies as $policy )
            {
                $module = (string)$policy->attribute( 'module_name' );
                if ( $module !== '*' && !self::moduleExists( $module, $modules ) )
                    $orphans[] = $policy;
            }
            $offset += count( $policies );
        }
        return $orphans;
    }

    /**
     * @param string $module a module name
     * @param array $modules the module directories
     * @return bool whether one of them holds the module
     */
    public static function moduleExists( $module, array $modules )
    {
        foreach ( $modules as $directory )
        {
            if ( file_exists( $directory . '/' . $module ) )
                return true;
        }
        return false;
    }
}

}
