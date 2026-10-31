<?php
/**
 * File containing the expEditorPolicies class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
 * bin/php/addeditorpolicies.php --apply adds them on an installation that exists (plan() and apply(); without --apply it
 * only lists them). The policies are added to the Editor role, so the role's assignment limits them as it limits the
 * rest of the role (Content and Media for the Editors group). A policy the editor already has counts, whether it is in the Editor role or in another role
 * assigned to the same users or groups (the Member role gives user/password on the site packages).
 *
 * overGrants() names what an editor role should not hold: functions that work on the whole installation and not on
 * the content the role is limited to (the trash emptied for everybody, the languages, the URL aliases and
 * wildcards, everybody's content jobs) and the administration modules. They are reported by the command, never
 * removed one by one. The base data's content/* (kept by plain_site) holds the first group: the setup step and the
 * command replace a content/* without limitations by the single content functions the site packages give editors
 * (narrowPlan(), narrow(); the command's --keep-content-wildcard leaves it).
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
            array( 'module' => 'expimageeditor', 'function' => 'edit', 'needs' => 'expimageeditor',
                   'reason' => 'the image editor beside an image in the edit form (expimageeditor; its save view still checks the draft and content/edit)' ),
            // ezie's module.php has no policy functions, so every view asks for ezie/*; each view still checks that
            // the user may edit the object's draft in its language. Only where ezie is active (a site that kept it).
            array( 'module' => 'ezie', 'function' => '*', 'needs' => 'ezie',
                   'reason' => 'the older image editor beside an image in the edit form (ezie, where a site keeps it active; every view still checks the draft)' ),
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
                                 'function' => (string)$policy->attribute( 'function_name' ),
                                 'limited' => count( (array)$policy->limitationList( false, true ) ) > 0 );
        }
        return $policies;
    }

    /**
     * The content functions an editor gets in place of content/*: the list the site packages give their Editor role
     * (create, edit, read, manage_locations, diff, versionread, versionremove, remove, translate, bookmark,
     * pendinglist, dashboard, view_embed) and the three of definitions() (hide, restore, reverserelatedlist). Left
     * out are the functions overGrantDefinitions() names (cleantrash, translations, urltranslator, jobs) and those no
     * editor view asks for (move, publish, pdf, tipafriend, view_system_url, publish_without_notification).
     *
     * @return string[]
     */
    public static function narrowedContentFunctions()
    {
        return array( 'create', 'edit', 'read', 'manage_locations', 'diff', 'versionread', 'versionremove', 'remove',
                      'translate', 'bookmark', 'pendinglist', 'dashboard', 'view_embed',
                      'hide', 'restore', 'reverserelatedlist' );
    }

    /**
     * Whether and how content/* of an editor role is replaced by single functions (narrowedContentFunctions()). Only
     * a content/* without limitations is replaced (the base data's, kept by plain_site); a limited one is the site's
     * choice and is reported, not changed. The single functions are added without limitations, so the role's
     * assignment limits them as it limited content/*; a function the role holds as a single policy already (also a
     * limited one) is not added again.
     *
     * @param array $policies the role's policies (policiesOf())
     * @param string[] $skip content/<function> entries never to add
     * @return array action ("narrow" or "keep"), why, remove (content/* policies to remove, a count), add (functions)
     */
    public static function narrowPlan( array $policies, array $skip = array() )
    {
        $wildcards = 0;
        $limitedWildcards = 0;
        $rest = array();
        foreach ( $policies as $policy )
        {
            if ( $policy['module'] === 'content' && $policy['function'] === '*' )
            {
                if ( !empty( $policy['limited'] ) )
                {
                    $limitedWildcards++;
                    $rest[] = $policy;
                }
                else
                    $wildcards++;
            }
            else
                $rest[] = $policy;
        }
        if ( $wildcards === 0 )
            return array( 'action' => 'keep', 'remove' => 0, 'add' => array(),
                          'why' => $limitedWildcards ? 'content/* has limitations: the site\'s choice, narrow it by hand' : 'the role has no content/*' );
        $add = array();
        foreach ( self::narrowedContentFunctions() as $function )
        {
            if ( in_array( 'content/' . $function, $skip, true ) || self::covers( $rest, 'content', $function ) )
                continue;
            $add[] = $function;
        }
        return array( 'action' => 'narrow', 'remove' => $wildcards, 'add' => $add,
                      'why' => 'content/* also grants ' . implode( ', ', self::wildcardOnly() ) );
    }

    /**
     * The content functions content/* grants and narrowedContentFunctions() does not.
     *
     * @return string[]
     */
    public static function wildcardOnly()
    {
        $over = array();
        foreach ( self::overGrantDefinitions() as $definition )
            if ( $definition['module'] === 'content' )
                $over[] = 'content/' . $definition['function'];
        return $over;
    }

    /**
     * Replaces the role's content/* without limitations by single functions (narrowPlan()).
     *
     * @param eZRole $role
     * @param array $plan a "narrow" plan of narrowPlan()
     * @return bool whether the role changed
     */
    public static function narrow( $role, array $plan )
    {
        if ( $plan['action'] !== 'narrow' )
            return false;
        $db = eZDB::instance();
        $db->begin();
        foreach ( $role->policyList() as $policy )
        {
            if ( $policy->attribute( 'module_name' ) === 'content' && $policy->attribute( 'function_name' ) === '*'
                 && count( (array)$policy->limitationList( false, true ) ) === 0 )
                $policy->removeThis();
        }
        foreach ( $plan['add'] as $function )
            $role->appendPolicy( 'content', $function );
        $db->commit();
        // the role's policy list is read again on the next use
        $role->Policies = null;
        eZRole::expireCache();
        return true;
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
     * extensions are not active yet in the setup wizard, and, when $activeExtensions is given, the new installation
     * activates it: ezie is installed with every installation but activated only by the site packages that keep it,
     * so its policy is not added where expimageeditor takes its place.
     *
     * @param string $extensionRoot the extension directory of the installation
     * @param string[]|null $activeExtensions the extensions the new installation activates; null or empty: not checked
     * @return string what happened, for the setup log
     */
    public static function ensure( $extensionRoot = 'extension', $activeExtensions = null )
    {
        $role = eZRole::fetchByName( self::ROLE_NAME );
        if ( !is_object( $role ) )
            return 'no role ' . self::ROLE_NAME . ', nothing added';
        // content/* of the base data (plain_site keeps it; the other site packages replace it themselves) becomes the
        // single functions the site packages give editors
        $narrowed = '';
        $narrowPlan = self::narrowPlan( self::policiesOf( $role ) );
        if ( self::narrow( $role, $narrowPlan ) )
            $narrowed = 'content/* replaced by content/' . implode( ', content/', $narrowPlan['add'] ) . '; ';
        $modules = self::setupModules( self::availableModules(), $extensionRoot, $activeExtensions );
        $added = array();
        foreach ( self::plan( self::policiesOf( $role ), $modules, array(), array(), self::heldAlongside( $role ) ) as $entry )
        {
            if ( $entry['action'] === 'grant' && self::apply( $role, $entry ) )
                $added[] = $entry['module'] . '/' . $entry['function'];
        }
        if ( $added )
            eZRole::expireCache();
        return $narrowed . ( $added ? 'added to the role ' . self::ROLE_NAME . ': ' . implode( ', ', $added )
                                    : 'the role ' . self::ROLE_NAME . ' has every editor policy' );
    }

    /**
     * The policy modules ensure() plans with: those of the running installation, and the module of each definition's
     * extension that is in the extension directory and, when $activeExtensions is given (not empty), activated by the
     * new installation.
     *
     * @param string[] $modules the modules of the running installation (availableModules())
     * @param string $extensionRoot the extension directory of the installation
     * @param string[]|null $activeExtensions the extensions the new installation activates; null or empty: not checked
     * @return string[]
     */
    public static function setupModules( array $modules, $extensionRoot, $activeExtensions = null )
    {
        $checkActive = is_array( $activeExtensions ) && count( $activeExtensions ) > 0;
        foreach ( self::definitions() as $definition )
        {
            $needs = $definition['needs'];
            if ( $needs === '' || in_array( $needs, $modules, true ) )
                continue;
            if ( $checkActive && !in_array( $needs, $activeExtensions, true ) )
                continue;
            if ( is_file( rtrim( $extensionRoot, '/' ) . '/' . $needs . '/modules/' . $needs . '/module.php' ) )
                $modules[] = $needs;
        }
        return $modules;
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
