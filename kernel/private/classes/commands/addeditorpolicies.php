<?php
/**
 * The code of bin/php/addeditorpolicies.php.
 * @description Give the Editor role the everyday policies it lacks: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, the own account (--dry-run lists them first)
 * Guide: doc/eng-us/bc/6.0/editor-role-policies.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Addeditorpolicies extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Give the Editor role the policies an editor needs for everyday work and it lacks: hiding and\n"
                    . "revealing (content/hide), the trash (content/restore), the list of what uses an object\n"
                    . "(content/reverserelatedlist), where the module exists ezmultiupload, and the own account\n"
                    . "(user/password, user/selfedit, notification/use) when no other role of the editors has it.\n"
                    . "A policy the role, or another role of the same users or groups, has in any form (also limited, or\n"
                    . "module/*) is left alone, so the script can run again. Also lists what the role holds that an editor\n"
                    . "role should not (WARN lines; nothing is removed). Prints PASS or FAIL at the end.\n\n"
                    . "Example: php bin/php/addeditorpolicies.php --dry-run\n"
                    . "         php bin/php/addeditorpolicies.php --only=content/restore\n"
                    . "         php bin/php/addeditorpolicies.php --skip=ezmultiupload/* --role=Redakteur",
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
                'role' => "The role to complete (ID or name); default Editor",
                'only' => "Only this policy (module/function), may be repeated",
                'skip' => "Never this policy (module/function), may be repeated; e.g. --skip=content/restore keeps the trash closed"
            )
        );

        $dryRun = (bool)$options['dry-run'];
        $roleName = $options['role'] ? (string)$options['role'] : \expEditorPolicies::ROLE_NAME;
        $only = $options['only'] ? array_map( 'strval', (array)$options['only'] ) : array();
        $skip = $options['skip'] ? array_map( 'strval', (array)$options['skip'] ) : array();

        $known = array();
        foreach ( \expEditorPolicies::definitions() as $definition )
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

        $modules = \expEditorPolicies::availableModules();
        $plan = \expEditorPolicies::plan( \expEditorPolicies::policiesOf( $role ), $modules, $only, $skip,
                                          \expEditorPolicies::heldAlongside( $role ) );
        $granted = 0;
        foreach ( $plan as $entry )
        {
            $name = sprintf( '%-28s', $entry['module'] . '/' . $entry['function'] );
            if ( $entry['action'] !== 'grant' )
            {
                $cli->output( "skip        $name {$entry['why']}" );
                continue;
            }
            $cli->output( ( $dryRun ? 'would add  ' : 'add        ' ) . " $name {$entry['reason']}" );
            if ( !$dryRun )
                \expEditorPolicies::apply( $role, $entry );
            $granted++;
        }

        foreach ( \expEditorPolicies::overGrants( \expEditorPolicies::policiesOf( $role ) ) as $over )
        {
            $cli->output( sprintf( 'WARN        %-28s granted by %s: %s', $over['module'] . '/' . $over['function'], $over['by'], $over['reason'] ) );
        }

        if ( $granted && !$dryRun )
        {
            \eZRole::expireCache();
            // Check what was stored: every planned policy is there now.
            $stored = \eZRole::fetch( (int)$role->attribute( 'id' ) );
            $after = \expEditorPolicies::plan( \expEditorPolicies::policiesOf( $stored ), $modules, $only, $skip,
                                               \expEditorPolicies::heldAlongside( $stored ) );
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
