<?php
/**
 * The code of bin/php/addeditorpolicies.php.
 * @description Give the Editor role the everyday policies it lacks: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, the image editor, the own account (dry run unless --apply)
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
                    . "(content/reverserelatedlist), where the modules exist ezmultiupload and the image editor\n"
                    . "(expimageeditor/edit), and the own account\n"
                    . "(user/password, user/selfedit, notification/use) when no other role of the editors has it.\n"
                    . "A policy the role, or another role of the same users or groups, has in any form (also limited, or\n"
                    . "module/*) is left alone, so the script can run again.\n"
                    . "First, a content/* without limitations (the base data's, which plain_site keeps) is replaced by the\n"
                    . "content functions the site packages give editors (create, edit, read, manage_locations, diff,\n"
                    . "versionread, versionremove, remove, translate, bookmark, pendinglist, dashboard, view_embed, hide,\n"
                    . "restore, reverserelatedlist): it also grants emptying everybody's trash, the languages, the URL\n"
                    . "aliases and everybody's content jobs. --keep-content-wildcard leaves it. Lists what the role still\n"
                    . "holds that an editor role should not (WARN lines). Prints PASS or FAIL at the end.\n"
                    . "Without --apply nothing is written: the script lists what it would remove and add. --dry-run is\n"
                    . "still accepted and means the same as no flag; --apply together with --dry-run is a usage error (exit 2).\n\n"
                    . "Example: php bin/php/addeditorpolicies.php\n"
                    . "         php bin/php/addeditorpolicies.php --apply\n"
                    . "         php bin/php/addeditorpolicies.php --apply --only=content/restore\n"
                    . "         php bin/php/addeditorpolicies.php --apply --skip=ezmultiupload/* --role=Redakteur",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            "[apply][dry-run][role:][only:*][skip:*][keep-content-wildcard]",
            '',
            array(
                'apply' => "Write the changes (the default is a dry run that lists them and changes nothing)",
                'dry-run' => "The default, kept for scripts that pass it: list what would be removed and added, change nothing",
                'keep-content-wildcard' => "Leave a content/* of the role as it is (not replaced by single functions)",
                'role' => "The role to complete (ID or name); default Editor",
                'only' => "Only this policy (module/function), may be repeated",
                'skip' => "Never this policy (module/function), may be repeated; e.g. --skip=content/restore keeps the trash closed"
            )
        );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        $dryRun = !$apply;
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

        // content/* without limitations becomes single functions; in a dry run the plan below works on the role as
        // the narrowing would leave it
        $policies = \expEditorPolicies::policiesOf( $role );
        $narrowed = 0;
        if ( $options['keep-content-wildcard'] )
            $narrowPlan = array( 'action' => 'keep', 'remove' => 0, 'add' => array(), 'why' => 'kept on request (--keep-content-wildcard)' );
        else if ( $only )
            $narrowPlan = array( 'action' => 'keep', 'remove' => 0, 'add' => array(), 'why' => 'kept: --only adds single policies only' );
        else
            $narrowPlan = \expEditorPolicies::narrowPlan( $policies, $skip );
        if ( $narrowPlan['action'] === 'narrow' )
        {
            $cli->output( ( $dryRun ? 'would remove' : 'remove      ' ) . sprintf( ' %-28s %s', 'content/*', $narrowPlan['why'] ) );
            foreach ( $narrowPlan['add'] as $function )
                $cli->output( ( $dryRun ? 'would add   ' : 'add         ' ) . sprintf( ' %-28s %s', 'content/' . $function, 'in place of content/*' ) );
            if ( !$dryRun )
            {
                \expEditorPolicies::narrow( $role, $narrowPlan );
                $role = \eZRole::fetch( (int)$role->attribute( 'id' ) );
                $policies = \expEditorPolicies::policiesOf( $role );
            }
            else
            {
                $kept = array();
                foreach ( $policies as $policy )
                    if ( !( $policy['module'] === 'content' && $policy['function'] === '*' && empty( $policy['limited'] ) ) )
                        $kept[] = $policy;
                foreach ( $narrowPlan['add'] as $function )
                    $kept[] = array( 'module' => 'content', 'function' => $function, 'limited' => false );
                $policies = $kept;
            }
            $narrowed = 1 + count( $narrowPlan['add'] );
        }
        else if ( strpos( $narrowPlan['why'], 'no content/*' ) === false )
            $cli->output( sprintf( 'keep         %-28s %s', 'content/*', $narrowPlan['why'] ) );

        $modules = \expEditorPolicies::availableModules();
        $plan = \expEditorPolicies::plan( $policies, $modules, $only, $skip,
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

        foreach ( \expEditorPolicies::overGrants( $dryRun ? $policies : \expEditorPolicies::policiesOf( \eZRole::fetch( (int)$role->attribute( 'id' ) ) ) ) as $over )
        {
            $cli->output( sprintf( 'WARN        %-28s granted by %s: %s', $over['module'] . '/' . $over['function'], $over['by'], $over['reason'] ) );
        }

        if ( $narrowed && !$dryRun )
        {
            // Check what was stored: no content/* without limitations, every single function there.
            $stored = \expEditorPolicies::policiesOf( \eZRole::fetch( (int)$role->attribute( 'id' ) ) );
            $again = \expEditorPolicies::narrowPlan( $stored, $skip );
            if ( $again['action'] === 'narrow' )
            {
                $cli->error( "FAIL: $label still has content/*" );
                $script->shutdown( 1 );
            }
            foreach ( $narrowPlan['add'] as $function )
            {
                if ( !\expEditorPolicies::covers( $stored, 'content', $function ) )
                {
                    $cli->error( "FAIL: content/$function is missing from $label" );
                    $script->shutdown( 1 );
                }
            }
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
        $replaced = $narrowed ? 'content/* ' . ( $dryRun ? 'would be' : 'was' ) . ' replaced by ' . count( $narrowPlan['add'] )
                                . ' content function' . ( count( $narrowPlan['add'] ) === 1 ? '' : 's' ) . '; ' : '';
        if ( $dryRun )
            $cli->output( "PASS: {$replaced}$granted polic" . ( $granted === 1 ? 'y' : 'ies' ) . " would be added to $label (dry run, nothing changed; --apply writes)" );
        else
            $cli->output( "PASS: {$replaced}$granted polic" . ( $granted === 1 ? 'y' : 'ies' ) . " added to $label" );
        $script->shutdown();
    }
}

}
