<?php
/**
 * The code of bin/php/cleanuppolicies.php, moved into a class (#207 stage 1). The file bin/php/cleanuppolicies.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Command\Kernel
{

class Cleanuppolicies extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'db', 'limitation', 'module', 'moduleExists', 'modules', 'optDryRun', 'options', 'policies', 'policy', 'removedPolicies', 'rows', 'script', 'total' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();

        $script = \eZScript::instance(
            array(
                'description' => "Remove from database policies defined on module which do not exist in a modules folder"
                    . "according to settings from module.ini/[ModuleSettings]/ExtensionRepositories",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $script->startup();
        $options = $script->getOptions(
            "[dry-run][n]",
            '',
            array(
                'dry-run' => "Test mode, output the list of affected policies without removing them",
                'n' => "Do not wait"
            )
        );
        $script->initialize();

        $optDryRun = (bool) $options['dry-run'];

        if ( !$optDryRun && !isset( $options['n'] ) )
        {
            $cli->warning( "This cleanup script is going to remove policies defined on module that do not exist." );
            $cli->warning();
            $cli->warning( "You have 10 seconds to break the script (press Ctrl-C)" );
            sleep( 10 );
            $cli->output();
        }

        $rows =  \eZPersistentObject::fetchObjectList( \eZPolicy::definition(),
            array(),
            null,
            false,
            null,
            false, false,
            array( array( 'operation' => 'count( * )',
                          'name' => 'count' ) ) );
        $total = $rows[0]['count'];
        if ( !$optDryRun )
        {
            $cli->output( "{$total} policies to check... (In the progess bar, 'R' means that the policy was removed)" );
        }
        else
        {
            $cli->output( "{$total} policies to check..." );
        }

        if ( !$optDryRun )
        {
            $script->setIterationData( 'R', '.' );
            $script->resetIteration( $total );
        }

        $limitation = array( 'offset' => 0, 'limit' => 100 );

        $db = \eZDB::instance();

        $modules = \eZModule::globalPathList();

        $removedPolicies = 0;
        while ( true )
        {
            $policies = \eZPersistentObject::fetchObjectList(
                \eZPolicy::definition(),
                null,
                null,
                null,
                $limitation,
                true
            );
            if ( empty( $policies ) )
            {
                break;
            }
            foreach ( $policies as $policy )
            {
                if ( $policy->attribute('module_name') === '*' )
                {
                    continue;
                }

                $moduleExists = false;
                foreach ( $modules as $module )
                {
                    if ( file_exists( $module . '/' . $policy->attribute('module_name') ) )
                    {
                        $moduleExists = true;
                        break;
                    }
                }
                if ( !$moduleExists )
                {
                    if ( !$optDryRun )
                    {
                        $policy->removeThis();
                    }
                    $removedPolicies++;
                }
                if ( $optDryRun )
                {
                    if ( !$moduleExists )
                    {
                        $cli->output( "Policy defined on module '" . $policy->attribute('module_name') . "' can be removed" );
                    }
                }
                else
                {
                    $script->iterate( $cli, !$moduleExists );
                }
            }
            $limitation['offset'] += $limitation['limit'] - $removedPolicies;
        }

        $cli->output();

        if ( $optDryRun && $removedPolicies > 0 )
        {
            $cli->output( "{$removedPolicies} policies can be removed" );
        }
        else if ( $removedPolicies > 0 )
        {
            $cli->output( "Removed {$removedPolicies} policies" );
        }
        else
        {
            $cli->output( "No policies found to remove" );
        }

        $script->shutdown();
    }
}

}
