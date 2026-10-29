<?php
/**
 * File containing the expReauthSettings class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The settings of "Verify it's you" as the page setup/reauth edits them (doc/eng-us/guides/verify-its-you.md): the
 * switch of the whole feature, the minutes a confirmation counts, the switch of each group and the user groups a group
 * asks (UserGroups[]: empty for every user). They are written the supported way, line by line through expIniEditor, so
 * the rest of the file stays as it is, with a backup in var/backup/ini and the change recorded in the audit
 * (system.setting.write):
 *  - for every siteaccess: the global override settings/override/site.ini.append.php (the file read last);
 *  - for one siteaccess: settings/siteaccess/<name>/site.ini.append.php. A value the global override sets wins over
 *    it, so the page says when a siteaccess value is hidden by one there.
 * The rules of the groups are not edited here: they are settings of site.ini, changed in a file by whoever deploys
 * the site.
 */
class expReauthSettings
{
    /** The scope name of the global override */
    const SCOPE_GLOBAL = '';

    /** @var string|null the settings directory written to (tests): its override/ and siteaccess/<name>/ */
    protected static $dir = null;

    /**
     * The values for the page.
     *
     * @param string $siteAccess '' for every siteaccess (the values this request sees), else the siteaccess
     * @return array enabled, minutes, groups (name => name, enabled, rule_count, user_groups), scope
     */
    public static function current( $siteAccess = self::SCOPE_GLOBAL )
    {
        $ini = null;
        if ( $siteAccess !== self::SCOPE_GLOBAL && class_exists( 'eZSiteAccess' ) )
            $ini = eZSiteAccess::getIni( $siteAccess, 'site.ini' );
        $s = expReauth::settings( $ini instanceof eZINI ? $ini : null );
        $groups = array();
        foreach ( $s['groups'] as $name => $g )
            $groups[$name] = array( 'name' => $name, 'enabled' => (bool)$g['enabled'], 'rule_count' => count( $g['rules'] ),
                                    'user_groups' => $g['user_groups'] );
        return array( 'enabled' => (bool)$s['enabled'], 'minutes' => (int)$s['minutes'], 'groups' => $groups, 'scope' => (string)$siteAccess );
    }

    /**
     * The scope a form or address names: '' (every siteaccess) or one of the siteaccesses given; anything else is ''.
     *
     * @param mixed $value
     * @param string[] $siteAccesses
     * @return string
     */
    public static function scope( $value, array $siteAccesses )
    {
        return is_string( $value ) && $value !== '' && in_array( $value, $siteAccesses, true ) ? $value : self::SCOPE_GLOBAL;
    }

    /**
     * The lines to write for a form's values: the groups the settings know, each true or false, with their user groups.
     *
     * @param array $known group names
     * @param array $post the form (ReauthEnabled, ReauthMinutes, ReauthGroups[], ReauthUserGroups[<group>][])
     * @return array enabled (bool), minutes (int, 1-1440), groups (name => bool), user_groups (name => int[]) for the
     *               groups the form lists user groups for (ReauthUserGroupsShown[])
     */
    public static function fromForm( array $known, array $post )
    {
        $chosen = isset( $post['ReauthGroups'] ) && is_array( $post['ReauthGroups'] ) ? array_map( 'strval', $post['ReauthGroups'] ) : array();
        $groups = array();
        foreach ( $known as $name )
            $groups[$name] = in_array( (string)$name, $chosen, true );
        $minutes = isset( $post['ReauthMinutes'] ) && is_numeric( $post['ReauthMinutes'] ) ? (int)$post['ReauthMinutes'] : 10;
        $shown = isset( $post['ReauthUserGroupsShown'] ) && is_array( $post['ReauthUserGroupsShown'] ) ? array_map( 'strval', $post['ReauthUserGroupsShown'] ) : array();
        $userGroups = array();
        foreach ( $known as $name )
        {
            if ( !in_array( (string)$name, $shown, true ) )
                continue;
            $ids = array();
            $given = isset( $post['ReauthUserGroups'][$name] ) && is_array( $post['ReauthUserGroups'][$name] ) ? $post['ReauthUserGroups'][$name] : array();
            foreach ( $given as $id )
                if ( is_scalar( $id ) && ctype_digit( (string)$id ) && (int)$id > 0 )
                    $ids[] = (int)$id;
            $ids = array_values( array_unique( $ids ) );
            sort( $ids );
            $userGroups[$name] = $ids;
        }
        $out = array( 'enabled' => !empty( $post['ReauthEnabled'] ), 'minutes' => max( 1, min( 1440, $minutes ) ), 'groups' => $groups );
        if ( $userGroups )
            $out['user_groups'] = $userGroups;
        return $out;
    }

    /**
     * The file a scope writes, relative to the settings directory.
     *
     * @param string $siteAccess
     * @return string override or siteaccess/<name>
     */
    public static function scopeDirectory( $siteAccess )
    {
        return $siteAccess === self::SCOPE_GLOBAL ? 'override' : 'siteaccess/' . $siteAccess;
    }

    /**
     * Writes the values to the scope's site.ini.append.php.
     *
     * @param array $values fromForm()
     * @param string $siteAccess '' for every siteaccess, else the siteaccess (it must have a settings directory)
     * @return string|null null when written, else why not
     */
    public static function write( array $values, $siteAccess = self::SCOPE_GLOBAL )
    {
        if ( $siteAccess !== self::SCOPE_GLOBAL && !preg_match( '/^[A-Za-z0-9_\-]+$/', (string)$siteAccess ) )
            return 'Unknown siteaccess.';
        $settingsDir = self::$dir !== null ? rtrim( self::$dir, '/' ) : rtrim( eZSys::rootDir(), '/' ) . '/settings';
        $dir = $settingsDir . '/' . self::scopeDirectory( $siteAccess );
        if ( $siteAccess !== self::SCOPE_GLOBAL && !is_dir( $dir ) )
            return 'The siteaccess has no settings directory.';
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) )
            return 'The directory settings/override cannot be made.';
        try
        {
            $root = dirname( $settingsDir ) . '/';
            $relative = basename( $settingsDir ) . '/' . self::scopeDirectory( $siteAccess );
            $scope = $siteAccess === self::SCOPE_GLOBAL
                ? new expIniScope( 'global', expIniScope::KIND_GLOBAL, $relative, $root, 'Global override (settings/override)' )
                : new expIniScope( 'siteaccess:' . $siteAccess, expIniScope::KIND_SITEACCESS, $relative, $root, 'Siteaccess ' . $siteAccess,
                                   array( 'siteaccess' => $siteAccess ) );
            $editor = new expIniEditor( $scope, 'site' );
            $editor->set( 'ReauthSettings', 'Reauth', $values['enabled'] ? 'enabled' : 'disabled' );
            $editor->set( 'ReauthSettings', 'Minutes', (string)(int)$values['minutes'] );
            foreach ( $values['groups'] as $name => $on )
                $editor->set( 'ReauthGroup_' . $name, 'Enabled', $on ? 'true' : 'false' );
            foreach ( isset( $values['user_groups'] ) ? $values['user_groups'] : array() as $name => $ids )
            {
                $editor->clearArray( 'ReauthGroup_' . $name, 'UserGroups' );
                foreach ( $ids as $id )
                    $editor->add( 'ReauthGroup_' . $name, 'UserGroups', (string)(int)$id );
            }
            if ( $editor->hasChanges() )
                $editor->save( array( 'create' => true ) );
        }
        catch ( Exception $e )
        {
            return $e->getMessage();
        }
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $dir . '/site.ini.append.php', true );
        try
        {
            if ( self::$dir === null && class_exists( 'eZCache' ) )
                eZCache::clearByTag( 'ini' );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'INI cache: ' . $e->getMessage(), __METHOD__ );
        }
        return null;
    }

    /**
     * The user groups the page offers: every object of the user group class (UserGroupClassID of [UserSettings]),
     * by name, at most 200.
     *
     * @return array[] id, name
     */
    public static function userGroupChoices()
    {
        $ini = eZINI::instance();
        $classID = $ini->hasVariable( 'UserSettings', 'UserGroupClassID' ) ? (int)$ini->variable( 'UserSettings', 'UserGroupClassID' ) : 3;
        $out = array();
        $rows = eZPersistentObject::fetchObjectList( eZContentObject::definition(), array( 'id', 'name' ),
                                                     array( 'contentclass_id' => $classID, 'status' => eZContentObject::STATUS_PUBLISHED ),
                                                     array( 'name' => 'asc' ), array( 'offset' => 0, 'length' => 200 ), false );
        foreach ( (array)$rows as $row )
            $out[] = array( 'id' => (int)$row['id'], 'name' => (string)$row['name'] );
        return $out;
    }

    /**
     * Sets the settings directory written to (tests): its override/ and siteaccess/<name>/; null for the
     * installation's settings/.
     */
    public static function setDirectory( $dir )
    {
        self::$dir = $dir;
    }
}
