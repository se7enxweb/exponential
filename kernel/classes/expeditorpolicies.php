<?php
/**
 * File containing the expEditorPolicies class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The policies an editor needs for everyday work and the Editor role of a new installation lacks: hiding and
 * revealing, the trash (looking into it and restoring), the list of what uses an object before it is moved or
 * removed, uploading several files at once into the media library (ezmultiupload), and the editor's own account
 * (password, profile, notifications) on an installation whose Editors group holds no other role.
 *
 * The base data gives the Editor role content/* and assigns it to the Editors group limited to the Content (/1/2/)
 * and Media (/1/43/) subtrees. Every site package except plain_site replaces content/* with a list of single
 * functions (create limited by class, edit, read limited by section, remove, translate, versions, ...) and assigns
 * the Member role to the Editors group as well; that list has no content/hide, content/restore and
 * content/reverserelatedlist, and no package grants ezmultiupload. An editor of a new installation can remove an
 * object to the trash but not get it back, cannot hide or reveal anything, and the media library's multi-upload is
 * refused.
 *
 * The setup step adds the missing policies after the site package's roles (ensure()); the upgrade command
 * bin/php/addeditorpolicies.php adds them on an installation that exists (plan() and apply()). The policies are added
 * to the Editor role, so the role's assignment limits them as it limits the rest of the role (Content and Media for
 * the Editors group). A policy the editor already has counts, whether it is in the Editor role or in another role
 * assigned to the same users or groups (the Member role gives user/password on the site packages).
 *
 * overGrants() names what an editor role should not hold: functions that work on the whole installation and not on
 * the content the role is limited to (the trash emptied for everybody, the languages, the URL aliases and
 * wildcards, everybody's content jobs) and the administration modules. They are reported by the command, never
 * removed: the base data's content/* (kept by plain_site) holds the first group.
 *
 * plan() and overGrants() work on plain arrays, so they are tested without a database.
 */
class expEditorPolicies
{
    /**
     * The role name the base data and every site package give the editors.
     */
    const ROLE_NAME = 'Editor';

    /**
     * The policies, in the order they are planned. 'needs' names the policy module that must exist for the policy to
     * mean anything (an extension's module); a kernel module has none.
     *
     * @return array each: module, function, needs (module name or ''), reason
     */
    public static function definitions()
    {
        return array(
            array( 'module' => 'content', 'function' => 'hide', 'needs' => '',
                   'reason' => 'hiding and revealing a node (content/hide; the node menu\'s Hide and Reveal)' ),
            array( 'module' => 'content', 'function' => 'restore', 'needs' => '',
                   'reason' => 'the trash: looking into it (content/trash) and restoring an object (content/restore)' ),
            array( 'module' => 'content', 'function' => 'reverserelatedlist', 'needs' => '',
                   'reason' => 'the list of objects that use one (content/reverserelatedlist), before moving or removing it' ),
            array( 'module' => 'ezmultiupload', 'function' => '*', 'needs' => 'ezmultiupload',
                   'reason' => 'uploading several files at once into the media library (ezmultiupload)' ),
            array( 'module' => 'user', 'function' => 'password', 'needs' => '',
                   'reason' => 'changing the own password (user/password)' ),
            array( 'module' => 'user', 'function' => 'selfedit', 'needs' => '',
                   'reason' => 'editing the own account (user/edit)' ),
            array( 'module' => 'notification', 'function' => 'use', 'needs' => '',
                   'reason' => 'the own notification settings (notification/settings)' ),
        );
    }

    /**
     * Functions an editor role should not hold: they act on the whole installation, not on the content the role's
     * assignment limits it to, or they belong to the administration. Reported by the command, never removed.
     *
     * @return array each: module, function, reason
     */
    public static function overGrantDefinitions()
    {
        return array(
            array( 'module' => 'content', 'function' => 'cleantrash',
                   'reason' => 'empties the trash of everybody for good (content/trash, Empty trash)' ),
            array( 'module' => 'content', 'function' => 'translations',
                   'reason' => 'adds and removes the languages of the whole installation (content/translations)' ),
            array( 'module' => 'content', 'function' => 'urltranslator',
                   'reason' => 'the URL aliases and wildcards of the whole installation (content/urltranslator, content/urlwildcards)' ),
            array( 'module' => 'content', 'function' => 'jobs',
                   'reason' => 'everybody\'s content jobs (content/jobs/(all)/1)' ),
            array( 'module' => 'setup', 'function' => '*', 'reason' => 'the setup module (caches, settings, extensions)' ),
            array( 'module' => 'class', 'function' => '*', 'reason' => 'the content classes' ),
            array( 'module' => 'role', 'function' => '*', 'reason' => 'the roles and policies' ),
            array( 'module' => 'section', 'function' => '*', 'reason' => 'the sections' ),
            array( 'module' => 'state', 'function' => 'administrate', 'reason' => 'the object state groups' ),
            array( 'module' => 'workflow', 'function' => '*', 'reason' => 'the workflows' ),
            array( 'module' => 'user', 'function' => 'register', 'reason' => 'not needed by a signed in editor' ),
            array( 'module' => 'git_manager', 'function' => '*', 'reason' => 'updating the installation with git (git_manager)' ),
            array( 'module' => 'update', 'function' => '*', 'reason' => 'updating the Composer packages (ezupdate)' ),
        );
    }

    /**
     * The policies of a role as plain data for plan().
     *
     * @param eZRole $role
     * @return array each: module, function
     */
    public static function policiesOf( $role )
    {
        $policies = array();
        foreach ( $role->policyList() as $policy )
        {
            $policies[] = array( 'module' => (string)$policy->attribute( 'module_name' ),
                                 'function' => (string)$policy->attribute( 'function_name' ) );
        }
        return $policies;
    }

    /**
     * The policies of the other roles assigned to the users and groups the role is assigned to (the Member role of the
     * site packages' Editors group), as plain data for plan().
     *
     * @param eZRole $role
     * @return array each: module, function
     */
    public static function heldAlongside( $role )
    {
        $db = eZDB::instance();
        $roleID = (int)$role->attribute( 'id' );
        $rows = $db->arrayQuery( "SELECT DISTINCT b.role_id FROM ezuser_role a, ezuser_role b
                                  WHERE a.role_id = $roleID AND b.contentobject_id = a.contentobject_id AND b.role_id <> $roleID" );
        $policies = array();
        foreach ( (array)$rows as $row )
        {
            $other = eZRole::fetch( (int)$row['role_id'] );
            if ( is_object( $other ) )
                $policies = array_merge( $policies, self::policiesOf( $other ) );
        }
        return $policies;
    }

    /**
     * Whether policies grant module/function in any form: * / *, module / *, or module / function with or without
     * limitations (a limited policy is the site's choice and is not widened).
     *
     * @param array $policies as policiesOf() returns them
     * @param string $module
     * @param string $function
     * @return bool
     */
    public static function covers( array $policies, $module, $function )
    {
        foreach ( $policies as $policy )
        {
            if ( $policy['module'] === '*' )
                return true;
            if ( $policy['module'] === $module && ( $policy['function'] === '*' || $policy['function'] === $function ) )
                return true;
        }
        return false;
    }

    /**
     * Decides which policies the role gets.
     *
     * @param array $policies the role's policies (policiesOf())
     * @param string[] $modules the policy modules that exist on the installation (needs is checked against them)
     * @param string[] $only module/function entries to consider; empty for all
     * @param string[] $skip module/function entries never to grant
     * @param array $alongside the policies of the other roles the same users and groups hold (heldAlongside())
     * @return array one entry per definition: module, function, reason, action ("grant" or "skip"), why
     */
    public static function plan( array $policies, array $modules, array $only = array(), array $skip = array(), array $alongside = array() )
    {
        $plan = array();
        foreach ( self::definitions() as $definition )
        {
            $name = $definition['module'] . '/' . $definition['function'];
            $entry = array( 'module' => $definition['module'], 'function' => $definition['function'],
                            'reason' => $definition['reason'] );
            if ( $only && !in_array( $name, $only, true ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => 'not asked for' );
            else if ( in_array( $name, $skip, true ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => 'skipped on request' );
            else if ( $definition['needs'] !== '' && !in_array( $definition['needs'], $modules, true ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => "no module {$definition['needs']} on this installation" );
            else if ( self::covers( $policies, $definition['module'], $definition['function'] ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => 'the role has it already' );
            else if ( self::covers( $alongside, $definition['module'], $definition['function'] ) )
                $plan[] = $entry + array( 'action' => 'skip', 'why' => 'another role of the same users or groups has it' );
            else
                $plan[] = $entry + array( 'action' => 'grant', 'why' => 'missing' );
        }
        return $plan;
    }

    /**
     * What the role holds that an editor role should not (overGrantDefinitions()), with the policy that grants it.
     *
     * @param array $policies the role's policies (policiesOf())
     * @return array each: module, function, reason, by (the granting policy, e.g. "content/*")
     */
    public static function overGrants( array $policies )
    {
        $found = array();
        foreach ( self::overGrantDefinitions() as $definition )
        {
            foreach ( $policies as $policy )
            {
                $function = $definition['function'];
                $hit = $policy['module'] === '*'
                    || ( $policy['module'] === $definition['module']
                         && ( $policy['function'] === '*' || $function === '*' || $policy['function'] === $function ) );
                if ( $hit )
                {
                    $found[] = array( 'module' => $definition['module'], 'function' => $function,
                                      'reason' => $definition['reason'], 'by' => $policy['module'] . '/' . $policy['function'] );
                    break;
                }
            }
        }
        return $found;
    }

    /**
     * Adds a planned policy (without limitations) to the role.
     *
     * @param eZRole $role
     * @param array $entry a "grant" entry of plan()
     * @return eZPolicy|false
     */
    public static function apply( $role, array $entry )
    {
        if ( !isset( $entry['action'] ) || $entry['action'] !== 'grant' )
            return false;
        return $role->appendPolicy( $entry['module'], $entry['function'] );
    }

    /**
     * The setup step's guarantee: the Editor role of a new installation has every policy of definitions() that it
     * and the roles held alongside it lack, whatever the base data and the site package gave it. An extension's policy
     * is added when the extension's module is in the installation's extension directory, since the new site's
     * extensions are not active yet in the setup wizard.
     *
     * @param string $extensionRoot the extension directory of the installation
     * @return string what happened, for the setup log
     */
    public static function ensure( $extensionRoot = 'extension' )
    {
        $role = eZRole::fetchByName( self::ROLE_NAME );
        if ( !is_object( $role ) )
            return 'no role ' . self::ROLE_NAME . ', nothing added';
        $modules = self::availableModules();
        foreach ( self::definitions() as $definition )
        {
            if ( $definition['needs'] !== '' && is_file( rtrim( $extensionRoot, '/' ) . '/' . $definition['needs'] . '/modules/' . $definition['needs'] . '/module.php' ) )
                $modules[] = $definition['needs'];
        }
        $added = array();
        foreach ( self::plan( self::policiesOf( $role ), $modules, array(), array(), self::heldAlongside( $role ) ) as $entry )
        {
            if ( $entry['action'] === 'grant' && self::apply( $role, $entry ) )
                $added[] = $entry['module'] . '/' . $entry['function'];
        }
        if ( $added )
            eZRole::expireCache();
        return $added ? 'added to the role ' . self::ROLE_NAME . ': ' . implode( ', ', $added )
                      : 'the role ' . self::ROLE_NAME . ' has every editor policy';
    }

    /**
     * The policy modules of the installation (kernel and active extensions), for plan().
     *
     * @return string[]
     */
    public static function availableModules()
    {
        $modules = array();
        foreach ( eZModule::globalPathList() as $path )
        {
            foreach ( (array)glob( rtrim( $path, '/' ) . '/*/module.php' ) as $file )
                $modules[] = basename( dirname( $file ) );
        }
        return array_values( array_unique( $modules ) );
    }
}
