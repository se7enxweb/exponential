<?php
/**
 * The code of bin/php/addanonymouspublicpolicies.php.
 * @description Give the Anonymous role the public policies it lacks: user/register, rss/feed, tags/view, the newsletter's public views (--dry-run lists them first)
 * Guide: doc/eng-us/bc/6.0/anonymous-public-policies.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Addanonymouspublicpolicies extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Give the Anonymous role the policies a visitor who is not signed in needs on a public site and\n"
                    . "it lacks: user/register (a new installation takes it out of PolicyOmitList[]), rss/feed, and where\n"
                    . "the module exists tags/view (eztags) and newsletter subscribe, configure, unsubscribe and archive\n"
                    . "(cjw_newsletter). A policy the role has in any form (also limited, or module/*) is left alone, so\n"
                    . "the script can run again. Prints PASS or FAIL at the end.\n\n"
                    . "Example: php bin/php/addanonymouspublicpolicies.php --dry-run\n"
                    . "         php bin/php/addanonymouspublicpolicies.php --only=user/register\n"
                    . "         php bin/php/addanonymouspublicpolicies.php --skip=user/register --role=Visitors",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[dry-run][role:][only:*][skip:*]",
            '',
            array(
                'dry-run' => "List what would be added, change nothing",
                'role' => "The role to complete (ID or name); default Anonymous",
                'only' => "Only this policy (module/function), may be repeated",
                'skip' => "Never this policy (module/function), may be repeated; e.g. --skip=user/register keeps registration closed"
            )
        );

        $dryRun = (bool)$options['dry-run'];
        $roleName = $options['role'] ? (string)$options['role'] : \expAnonymousPublicPolicies::ROLE_NAME;
        $only = $options['only'] ? array_map( 'strval', (array)$options['only'] ) : array();
        $skip = $options['skip'] ? array_map( 'strval', (array)$options['skip'] ) : array();

        $known = array();
        foreach ( \expAnonymousPublicPolicies::definitions() as $definition )
            $known[] = $definition['module'] . '/' . $definition['function'];
        foreach ( array_merge( $only, $skip ) as $name )
        {
            if ( !in_array( $name, $known, true ) )
            {
                $cli->error( "FAIL: unknown policy '$name'; known: " . implode( ', ', $known ) );
                $script->shutdown( 1 );
            }
        }

        $role = ctype_digit( $roleName ) ? \eZRole::fetch( (int)$roleName ) : \eZRole::fetchByName( $roleName );
        if ( !is_object( $role ) )
        {
            $cli->error( "FAIL: no role '$roleName'" );
            $script->shutdown( 1 );
        }
        $label = sprintf( '%s (%d)', $role->attribute( 'name' ), $role->attribute( 'id' ) );

        $plan = \expAnonymousPublicPolicies::plan( \expAnonymousPublicPolicies::policiesOf( $role ),
                                                   \expAnonymousPublicPolicies::availableModules(), $only, $skip );
        $granted = 0;
        foreach ( $plan as $entry )
        {
            $name = sprintf( '%-22s', $entry['module'] . '/' . $entry['function'] );
            if ( $entry['action'] !== 'grant' )
            {
                $cli->output( "skip        $name {$entry['why']}" );
                continue;
            }
            $cli->output( ( $dryRun ? 'would add  ' : 'add        ' ) . " $name {$entry['reason']}" );
            if ( !$dryRun )
                \expAnonymousPublicPolicies::apply( $role, $entry );
            $granted++;
        }

        if ( $granted && !$dryRun )
        {
            \eZRole::expireCache();
            // Check what was stored: every planned policy is there now.
            $after = \expAnonymousPublicPolicies::plan( \expAnonymousPublicPolicies::policiesOf( \eZRole::fetch( (int)$role->attribute( 'id' ) ) ),
                                                        \expAnonymousPublicPolicies::availableModules(), $only, $skip );
            foreach ( $after as $entry )
            {
                if ( $entry['action'] === 'grant' )
                {
                    $cli->error( "FAIL: {$entry['module']}/{$entry['function']} is still missing from $label" );
                    $script->shutdown( 1 );
                }
            }
        }

        $cli->output();
        if ( $dryRun )
            $cli->output( "PASS: $granted polic" . ( $granted === 1 ? 'y' : 'ies' ) . " would be added to $label (dry run, nothing changed)" );
        else
            $cli->output( "PASS: $granted polic" . ( $granted === 1 ? 'y' : 'ies' ) . " added to $label" );
        $script->shutdown();
    }
}

}
