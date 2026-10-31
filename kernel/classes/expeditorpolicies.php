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
 * The class sets of editorpolicies.ini (default: the media site's ng_* classes) add one content/create limited to the
 * set's classes the role cannot create yet and their parent classes, and one websitetoolbar/use for them
 * (classPlan()); a policy the role holds twice with the same limitations is removed once (duplicates()). Both run
 * after the single policies, in ensure(), the command and the upgrade step (completeClassesAndDuplicates()).
 *
 * plan(), overGrants(), classPlan() and duplicates() work on plain arrays, so they are tested without a database.
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
            // needs "uiswitch:<function>": the function exists only while its provider is in uiswitch.ini ProviderList[]
            array( 'module' => 'uiswitch', 'function' => 'language', 'needs' => 'uiswitch:language',
                   'reason' => 'the display language of the administration interface (the UI switch)' ),
            array( 'module' => 'uiswitch', 'function' => 'design', 'needs' => 'uiswitch:design',
                   'reason' => 'the light or dark design of the administration interface (the UI switch)' ),
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
            $limitations = array();
            foreach ( (array)$policy->limitationList( false, true ) as $limitation )
            {
                $values = array();
                foreach ( (array)$limitation->attribute( 'values' ) as $value )
                    $values[] = (string)$value->attribute( 'value' );
                $limitations[(string)$limitation->attribute( 'identifier' )] = $values;
            }
            $policies[] = array( 'id' => (int)$policy->attribute( 'id' ),
                                 'module' => (string)$policy->attribute( 'module_name' ),
                                 'function' => (string)$policy->attribute( 'function_name' ),
                                 'limited' => count( $limitations ) > 0,
                                 'limitations' => $limitations );
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
        // the website toolbar's module is ezwt's, not active yet in the setup wizard either
        foreach ( (array)glob( rtrim( $extensionRoot, '/' ) . '/*/modules/websitetoolbar/module.php' ) as $file )
            $modules[] = 'websitetoolbar';
        $more = self::completeClassesAndDuplicates( eZRole::fetch( (int)$role->attribute( 'id' ) ), true, array(), true, $modules );
        return $narrowed . ( $added ? 'added to the role ' . self::ROLE_NAME . ': ' . implode( ', ', $added )
                                    : 'the role ' . self::ROLE_NAME . ' has every editor policy' )
             . ( $more['lines'] ? '; ' . implode( '; ', $more['lines'] ) : '' );
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
        // the UI switch's functions follow its providers (uiswitch.ini ProviderList[])
        if ( in_array( 'uiswitch', $modules, true ) && class_exists( 'expUISwitch' ) )
        {
            foreach ( expUISwitch::policyFunctions() as $function )
                $modules[] = 'uiswitch:' . $function;
        }
        return array_values( array_unique( $modules ) );
    }

    /**
     * Policies the role holds more than once: the same module, function and limitations (values in any order). The
     * first (lowest id) is kept. Policies without an id (plain test data) are never reported.
     *
     * @param array $policies the role's policies (policiesOf())
     * @return array each: id (the policy to remove), name (module/function), keep (the id kept)
     */
    public static function duplicates( array $policies )
    {
        usort( $policies, function ( $a, $b ) {
            return ( isset( $a['id'] ) ? (int)$a['id'] : 0 ) <=> ( isset( $b['id'] ) ? (int)$b['id'] : 0 );
        } );
        $seen = array();
        $found = array();
        foreach ( $policies as $policy )
        {
            if ( empty( $policy['id'] ) )
                continue;
            $limitations = isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array();
            ksort( $limitations );
            foreach ( $limitations as $identifier => $values )
            {
                $values = array_map( 'strval', (array)$values );
                sort( $values );
                $limitations[$identifier] = $values;
            }
            $key = $policy['module'] . '/' . $policy['function'] . '|' . json_encode( $limitations );
            if ( isset( $seen[$key] ) )
                $found[] = array( 'id' => (int)$policy['id'], 'name' => $policy['module'] . '/' . $policy['function'],
                                  'keep' => $seen[$key] );
            else
                $seen[$key] = (int)$policy['id'];
        }
        return $found;
    }

    /**
     * Removes the policies duplicates() reported.
     *
     * @param array $duplicates
     * @return int how many were removed
     */
    public static function removeDuplicates( array $duplicates )
    {
        $removed = 0;
        foreach ( $duplicates as $duplicate )
        {
            $policy = eZPolicy::fetch( (int)$duplicate['id'] );
            if ( is_object( $policy ) )
            {
                $policy->removeThis();
                $removed++;
            }
        }
        if ( $removed )
            eZRole::expireCache();
        return $removed;
    }

    /**
     * Whether duplicates are removed (editorpolicies.ini [EditorPolicySettings] RemoveDuplicates).
     *
     * @param eZINI|null $ini
     * @return bool
     */
    public static function removeDuplicatesEnabled( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'editorpolicies.ini' );
        return !$ini->hasVariable( 'EditorPolicySettings', 'RemoveDuplicates' )
            || $ini->variable( 'EditorPolicySettings', 'RemoveDuplicates' ) !== 'disabled';
    }

    /**
     * The class sets of editorpolicies.ini.
     *
     * @param eZINI|null $ini
     * @return array name => classes (identifier patterns), parents (identifiers), containers_are_parents, toolbar
     */
    public static function classSetDefinitions( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'editorpolicies.ini' );
        $sets = array();
        $names = $ini->hasVariable( 'EditorPolicySettings', 'ClassSets' ) ? (array)$ini->variable( 'EditorPolicySettings', 'ClassSets' ) : array();
        foreach ( $names as $name )
        {
            $group = 'ClassSet_' . $name;
            if ( $name === '' || !$ini->hasGroup( $group ) )
                continue;
            $sets[$name] = array(
                'classes' => $ini->hasVariable( $group, 'Classes' ) ? array_values( array_filter( (array)$ini->variable( $group, 'Classes' ), 'strlen' ) ) : array(),
                'parents' => $ini->hasVariable( $group, 'Parents' ) ? array_values( array_filter( (array)$ini->variable( $group, 'Parents' ), 'strlen' ) ) : array(),
                'containers_are_parents' => !$ini->hasVariable( $group, 'ContainersAreParents' ) || $ini->variable( $group, 'ContainersAreParents' ) === 'true',
                'toolbar' => !$ini->hasVariable( $group, 'WebsiteToolbar' ) || $ini->variable( $group, 'WebsiteToolbar' ) === 'true',
            );
        }
        return $sets;
    }

    /**
     * The content classes of the installation, for resolveClassSet().
     *
     * @return array each: id, identifier, is_container
     */
    public static function installedClasses()
    {
        $classes = array();
        foreach ( (array)eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
        {
            $classes[] = array( 'id' => (int)$class->attribute( 'id' ),
                                'identifier' => (string)$class->attribute( 'identifier' ),
                                'is_container' => (bool)$class->attribute( 'is_container' ) );
        }
        return $classes;
    }

    /**
     * The class and parent class ids of a set on an installation.
     *
     * @param array $set a classSetDefinitions() entry
     * @param array $classes installedClasses()
     * @return array class_ids (int[], sorted), parent_ids (int[], sorted)
     */
    public static function resolveClassSet( array $set, array $classes )
    {
        $classIds = array();
        $parentIds = array();
        foreach ( $classes as $class )
        {
            $inSet = false;
            foreach ( $set['classes'] as $pattern )
                if ( fnmatch( $pattern, $class['identifier'] ) )
                    $inSet = true;
            if ( $inSet )
            {
                $classIds[] = (int)$class['id'];
                if ( !empty( $set['containers_are_parents'] ) && !empty( $class['is_container'] ) )
                    $parentIds[] = (int)$class['id'];
            }
            if ( in_array( $class['identifier'], $set['parents'], true ) )
                $parentIds[] = (int)$class['id'];
        }
        $classIds = array_values( array_unique( $classIds ) );
        $parentIds = array_values( array_unique( $parentIds ) );
        sort( $classIds );
        sort( $parentIds );
        return array( 'class_ids' => $classIds, 'parent_ids' => $parentIds );
    }

    /**
     * The class ids no policy of the list grants module/function for: a policy grants a class when it covers the
     * function (covers()) and has no Class limitation or one that names the class. Other limitations (Section,
     * ParentClass, Subtree) are the site's choice and do not make a class missing.
     *
     * @param array $policies
     * @param string $module
     * @param string $function
     * @param int[] $classIds
     * @return int[]
     */
    public static function uncoveredClasses( array $policies, $module, $function, array $classIds )
    {
        $missing = array();
        foreach ( $classIds as $classId )
        {
            $covered = false;
            foreach ( $policies as $policy )
            {
                if ( !self::covers( array( $policy ), $module, $function ) )
                    continue;
                $limitations = isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array();
                if ( !isset( $limitations['Class'] ) || in_array( (string)$classId, array_map( 'strval', (array)$limitations['Class'] ), true ) )
                {
                    $covered = true;
                    break;
                }
            }
            if ( !$covered )
                $missing[] = (int)$classId;
        }
        return $missing;
    }

    /**
     * Which class-limited policies the role gets for the class sets: one content/create (Class = the set's classes
     * the role cannot create yet, ParentClass = the set's parents) and, with the website toolbar, one
     * websitetoolbar/use (Class = the set's classes the toolbar is not granted for). Only the role's own policies
     * count: a create policy of another role (the Member role) is limited to that role's own areas.
     *
     * @param array $policies the role's policies (policiesOf())
     * @param array $sets classSetDefinitions()
     * @param array $classes installedClasses()
     * @param string[] $modules availableModules()
     * @param string[] $skip set names never to grant
     * @return array each: set, module, function, limitations, action ("grant" or "skip"), why
     */
    public static function classPlan( array $policies, array $sets, array $classes, array $modules, array $skip = array() )
    {
        $plan = array();
        foreach ( $sets as $name => $set )
        {
            $resolved = self::resolveClassSet( $set, $classes );
            $wanted = array( array( 'content', 'create' ) );
            if ( !empty( $set['toolbar'] ) )
                $wanted[] = array( 'websitetoolbar', 'use' );
            foreach ( $wanted as $pair )
            {
                list( $module, $function ) = $pair;
                $entry = array( 'set' => $name, 'module' => $module, 'function' => $function, 'limitations' => array() );
                if ( in_array( $name, $skip, true ) )
                    $plan[] = $entry + array( 'action' => 'skip', 'why' => 'skipped on request' );
                else if ( !$resolved['class_ids'] )
                    $plan[] = $entry + array( 'action' => 'skip', 'why' => 'no class of the set on this installation' );
                else if ( $module !== 'content' && !in_array( $module, $modules, true ) )
                    $plan[] = $entry + array( 'action' => 'skip', 'why' => "no module $module on this installation" );
                else if ( $module === 'content' && !$resolved['parent_ids'] )
                    $plan[] = $entry + array( 'action' => 'skip', 'why' => 'no parent class of the set on this installation' );
                else
                {
                    $missing = self::uncoveredClasses( $policies, $module, $function, $resolved['class_ids'] );
                    if ( !$missing )
                    {
                        $plan[] = $entry + array( 'action' => 'skip', 'why' => 'the role has it for every class of the set' );
                        continue;
                    }
                    $entry['limitations'] = array( 'Class' => $missing );
                    if ( $module === 'content' )
                        $entry['limitations']['ParentClass'] = $resolved['parent_ids'];
                    $plan[] = $entry + array( 'action' => 'grant',
                                              'why' => count( $missing ) . ' class' . ( count( $missing ) === 1 ? '' : 'es' ) . ' of the set missing' );
                }
            }
        }
        return $plan;
    }

    /**
     * Adds a "grant" entry of classPlan() to the role.
     *
     * @param eZRole $role
     * @param array $entry
     * @return eZPolicy|false
     */
    public static function applyClassEntry( $role, array $entry )
    {
        if ( !isset( $entry['action'] ) || $entry['action'] !== 'grant' )
            return false;
        return $role->appendPolicy( $entry['module'], $entry['function'], $entry['limitations'] );
    }

    /**
     * A limitation list for output: "Class=43,44 ParentClass=1".
     *
     * @param array $limitations
     * @return string
     */
    public static function describeLimitations( array $limitations )
    {
        $parts = array();
        foreach ( $limitations as $identifier => $values )
            $parts[] = $identifier . '=' . implode( ',', (array)$values );
        return implode( ' ', $parts );
    }

    /**
     * The duplicates and the class sets of a role, the part of ensure(), the command and the upgrade step after the
     * single policies. Writes only with $apply.
     *
     * @param eZRole $role
     * @param bool $apply
     * @param string[] $skipSets
     * @param bool $dedupe
     * @param string[]|null $modules the policy modules (availableModules() when null)
     * @return array duplicates (duplicates()), classes (classPlan()), lines (what happened, for a log)
     */
    public static function completeClassesAndDuplicates( $role, $apply, array $skipSets = array(), $dedupe = true, $modules = null )
    {
        $duplicates = $dedupe && self::removeDuplicatesEnabled() ? self::duplicates( self::policiesOf( $role ) ) : array();
        $lines = array();
        foreach ( $duplicates as $duplicate )
            $lines[] = ( $apply ? 'removed' : 'remove' ) . " duplicate {$duplicate['name']} (policy {$duplicate['id']}, policy {$duplicate['keep']} kept)";
        if ( $apply && $duplicates )
        {
            self::removeDuplicates( $duplicates );
            $role = eZRole::fetch( (int)$role->attribute( 'id' ) );
        }
        $policies = self::policiesOf( $role );
        if ( !$apply && $duplicates )
        {
            $removed = array();
            foreach ( $duplicates as $duplicate )
                $removed[] = $duplicate['id'];
            $policies = array_values( array_filter( $policies, function ( $policy ) use ( $removed ) {
                return !in_array( (int)$policy['id'], $removed, true );
            } ) );
        }
        $classes = self::classPlan( $policies, self::classSetDefinitions(), self::installedClasses(),
                                    $modules === null ? self::availableModules() : $modules, $skipSets );
        foreach ( $classes as $entry )
        {
            if ( $entry['action'] !== 'grant' )
                continue;
            $lines[] = ( $apply ? 'added' : 'add' ) . " {$entry['module']}/{$entry['function']} "
                     . self::describeLimitations( $entry['limitations'] ) . " (class set {$entry['set']})";
            if ( $apply )
                self::applyClassEntry( $role, $entry );
        }
        if ( $apply )
            eZRole::expireCache();
        return array( 'duplicates' => $duplicates, 'classes' => $classes, 'lines' => $lines );
    }
}
