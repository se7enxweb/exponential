<?php
/**
 * File containing the expReauthSettings class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The settings of "Verify it's you" as the page setup/reauth edits them (doc/eng-us/guides/verify-its-you.md): the
 * switch of the whole feature, the minutes a confirmation counts, and the switch of each group. They are written to
 * the global override settings/override/site.ini.append.php (the file every siteaccess reads last), line by line
 * through expIniEditor, so the rest of the file stays as it is, with a backup in var/backup/ini and the change
 * recorded in the audit (system.setting.write). The rules of the groups are not edited here: they are settings of
 * site.ini, changed in a file by whoever deploys the site.
 */
class expReauthSettings
{
    /** @var string|null the directory written to (tests); null: settings/override of the installation */
    protected static $dir = null;

    /**
     * The values for the page.
     *
     * @return array enabled, minutes, groups (name => enabled, rule_count)
     */
    public static function current()
    {
        $s = expReauth::settings();
        $groups = array();
        foreach ( $s['groups'] as $name => $g )
            $groups[$name] = array( 'name' => $name, 'enabled' => (bool)$g['enabled'], 'rule_count' => count( $g['rules'] ) );
        return array( 'enabled' => (bool)$s['enabled'], 'minutes' => (int)$s['minutes'], 'groups' => $groups );
    }

    /**
     * The lines to write for a form's values: the groups the settings know, each true or false.
     *
     * @param array $known group names
     * @param array $post the form (ReauthEnabled, ReauthMinutes, ReauthGroups[])
     * @return array enabled (bool), minutes (int, 1-1440), groups (name => bool)
     */
    public static function fromForm( array $known, array $post )
    {
        $chosen = isset( $post['ReauthGroups'] ) && is_array( $post['ReauthGroups'] ) ? array_map( 'strval', $post['ReauthGroups'] ) : array();
        $groups = array();
        foreach ( $known as $name )
            $groups[$name] = in_array( (string)$name, $chosen, true );
        $minutes = isset( $post['ReauthMinutes'] ) && is_numeric( $post['ReauthMinutes'] ) ? (int)$post['ReauthMinutes'] : 10;
        return array( 'enabled' => !empty( $post['ReauthEnabled'] ), 'minutes' => max( 1, min( 1440, $minutes ) ), 'groups' => $groups );
    }

    /**
     * Writes the values to settings/override/site.ini.append.php.
     *
     * @param array $values fromForm()
     * @return string|null null when written, else why not
     */
    public static function write( array $values )
    {
        $dir = self::$dir !== null ? rtrim( self::$dir, '/' ) : rtrim( eZSys::rootDir(), '/' ) . '/settings/override';
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) )
            return 'The directory settings/override cannot be made.';
        try
        {
            $scope = new expIniScope( 'global', expIniScope::KIND_GLOBAL, basename( $dir ), dirname( $dir ) . '/', 'Global override (settings/override)' );
            $editor = new expIniEditor( $scope, 'site' );
            $editor->set( 'ReauthSettings', 'Reauth', $values['enabled'] ? 'enabled' : 'disabled' );
            $editor->set( 'ReauthSettings', 'Minutes', (string)(int)$values['minutes'] );
            foreach ( $values['groups'] as $name => $on )
                $editor->set( 'ReauthGroup_' . $name, 'Enabled', $on ? 'true' : 'false' );
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

    /** Sets the directory written to (tests), null for settings/override. */
    public static function setDirectory( $dir )
    {
        self::$dir = $dir;
    }
}
