<?php
/**
 * File containing the expDebugBarUser class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The current user as the content view cache sees them, for the debug bar's User tab and its chip: who the user is,
 * which roles they have and where each one comes from, and their cache profile.
 *
 * The cache profile is a short fingerprint of the parts of a view cache key that depend on the user
 * (eZNodeviewfunctions::userCacheKeys(): role ids, values of limited assignments, discounts, an interface switch),
 * under the ViewCacheTweaks of the page's node (the global ones without a content view), and the user id with the tweak
 * pr_user. With a content view on the page its key decides: a part a content/view/cachekeys listener left out is not
 * in the profile, a part it changed is in it as changed. Two users with the same profile share the cache files of
 * views under the same tweaks, as far as the parts of the view itself do not differ: the preferences of
 * CachedViewPreferences and the keys listeners add. Those have a fingerprint of their own, for the view of the page.
 *
 * Shown to users with setup/setup and, with debugbar.ini ReadAccess=debug (the default), to whoever gets the debug
 * output (mayView()): a logged-in user sees their own role and limitation ids, an anonymous visitor's are those of
 * every anonymous visitor. A logged-in user also sees the profile
 * of anonymous visitors, to tell at once whether the two share the cache files.
 */
class expDebugBarUser
{
    /** Characters of a fingerprint. */
    const FINGERPRINT_LENGTH = 8;

    /**
     * The current user's profile, or null when there is no user to ask (an early error, a script without the kernel).
     *
     * @param array|null $viewCache the content view cache of the page as the Rendering tab has it
     *                              (expRenderStats::viewCache(): parts, from_listeners), null when none ran
     * @return array|null
     */
    public static function data( $viewCache = null )
    {
        if ( !class_exists( 'eZUser', false ) )
            return null;
        try
        {
            $user = eZUser::currentUser();
            if ( !$user instanceof eZUser )
                return null;

            $parts = is_array( $viewCache ) && !empty( $viewCache['parts'] ) ? $viewCache['parts'] : array();
            $nodeID = isset( $parts['node_id'] ) ? (int)$parts['node_id'] : null;
            $tweak = eZNodeviewfunctions::viewCacheTweak( $nodeID );
            $userKeys = eZNodeviewfunctions::userCacheKeys( $user, $tweak );
            // the preferences of every view mode with CachedViewPreferences, each once: with the same values the views
            // of every view mode get the same part userpreferences
            $cachedViewPreferences = array();
            if ( class_exists( 'eZINI', false ) && eZINI::instance()->hasVariable( 'ContentSettings', 'CachedViewPreferences' ) )
                $cachedViewPreferences = (array)eZINI::instance()->variable( 'ContentSettings', 'CachedViewPreferences' );
            $preferences = self::preferences( $cachedViewPreferences, class_exists( 'eZPreferences' ) ? (array)eZPreferences::values( $user ) : array() );
            if ( strpos( $tweak, 'ignore_userpreferences' ) === false && $preferences['key'] !== '' )
                $userKeys['userpreferences'] = $preferences['key'];
            $perUser = strpos( $tweak, 'pr_user' ) !== false && !$user->isAnonymous();
            $profile = self::profile( $userKeys, $viewCache, $perUser ? (int)$user->attribute( 'contentobject_id' ) : null );

            // only how many keys and how large, for the overview (one serialize() while the bar is rendered for this
            // user): the content is loaded when its section is opened
            $session = self::sessionStarted() && isset( $_SESSION ) && is_array( $_SESSION ) ? $_SESSION : null;
            $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
            $profile['anonymous_profile'] = $user->isAnonymous() ? null : self::anonymousProfile( $tweak, $profile['removed'], $cachedViewPreferences );
            $profile['preferences'] = $preferences['rows'];
            return array(
                'id'          => (int)$user->attribute( 'contentobject_id' ),
                'login'       => (string)$user->attribute( 'login' ),
                'logged_in'   => (bool)$user->isRegistered(),
                'siteaccess'  => $access,
                'session'     => class_exists( 'eZSession', false ) && eZSession::hasStarted(),
                'session_keys'  => $session === null ? 0 : count( $session ),
                'session_bytes' => $sessionBytes = ( $session === null ? 0 : expDebugBarSession::bytes( $session ) ),
                'session_warn_bytes' => $warnBytes = expDebugBarSession::warnBytes(),
                'session_large' => $sessionBytes > $warnBytes,
                'node_id'     => $nodeID,
                'tweak'       => $tweak,
                'per_user'    => $perUser,
                'assignments' => self::assignments( $user ),
            ) + $profile;
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /** Whether the request has a running session. */
    protected static function sessionStarted()
    {
        return class_exists( 'eZSession', false ) && eZSession::hasStarted();
    }

    /**
     * The cache profile from the user's parts (eZNodeviewfunctions::userCacheKeys()) and the view of the page.
     *
     * With a view its key decides: a user part a content/view/cachekeys listener left out is dropped (listed in
     * removed), one it changed takes the key's value (listed in changed); the preferences (userpreferences, those of
     * every view mode) are left as they are. The other keys of listeners make the view profile. With pr_user the user id is part of
     * the profile, as it is part of the file name.
     *
     * @param array $userKeys name => value
     * @param array|null $viewCache expRenderStats::viewCache() (parts, from_listeners), null when no view ran
     * @param int|null $perUserID the user id when the tweak pr_user makes the files per user, else null
     * @return array keys, removed, changed, profile, view_keys, from_listeners, view_profile
     */
    public static function profile( array $userKeys, $viewCache = null, $perUserID = null )
    {
        $parts = is_array( $viewCache ) && !empty( $viewCache['parts'] ) ? $viewCache['parts'] : array();
        $fromListeners = is_array( $viewCache ) && isset( $viewCache['from_listeners'] ) ? (array)$viewCache['from_listeners'] : array();
        $removed = array();
        $changed = array();
        if ( $parts )
        {
            foreach ( $userKeys as $key => $value )
            {
                // the preferences are those of every view mode here; the view's key has its own mode's only, or none
                if ( $key === 'userpreferences' )
                    continue;
                if ( !array_key_exists( $key, $parts ) )
                {
                    $removed[] = $key;
                    unset( $userKeys[$key] );
                }
                else if ( in_array( $key, $fromListeners, true ) )
                {
                    $changed[] = $key;
                    $userKeys[$key] = self::scalarText( $parts[$key] );
                }
            }
        }
        $profileParts = $userKeys;
        // pr_user puts the user id in front of the file name: every logged-in user has files of their own
        if ( $perUserID !== null )
            $profileParts['pr_user'] = (string)(int)$perUserID;

        $viewKeys = array();
        foreach ( $fromListeners as $key )
        {
            if ( array_key_exists( $key, $parts ) && !array_key_exists( $key, $userKeys ) )
                $viewKeys[$key] = self::scalarText( $parts[$key] );
        }
        return array(
            'keys'           => $userKeys,
            'removed'        => $removed,
            'changed'        => $changed,
            'profile'        => self::fingerprint( $profileParts ),
            'view_keys'      => $viewKeys,
            'from_listeners' => array_values( array_intersect( $fromListeners, array_keys( $viewKeys ) ) ),
            'view_profile'   => $viewKeys ? self::fingerprint( $viewKeys ) : null,
        );
    }

    /**
     * The cache profile of anonymous visitors under the same ViewCacheTweaks, without the parts of a view (listeners
     * may read the session of the current request): the user parts of the anonymous user, with the default
     * preferences (an anonymous visitor's are in their session), less those a listener left out on this page. Null
     * when the anonymous user cannot be read.
     *
     * @param string $tweak
     * @param array $removed the user parts a listener left out of this page's key
     * @param array $cachedViewPreferences site.ini [ContentSettings] CachedViewPreferences[]
     * @return string|null
     */
    public static function anonymousProfile( $tweak, array $removed = array(), array $cachedViewPreferences = array() )
    {
        try
        {
            $anonymous = eZUser::fetch( eZUser::anonymousId() );
            if ( !$anonymous instanceof eZUser )
                return null;
            $keys = eZNodeviewfunctions::userCacheKeys( $anonymous, $tweak );
            // an interface switch is the signed-in user's choice: an anonymous visitor has none
            unset( $keys['uiswitch'] );
            // an anonymous visitor's preferences live in their session: a new visitor has the defaults
            $preferencesKey = self::preferences( $cachedViewPreferences, array() )['key'];
            if ( strpos( $tweak, 'ignore_userpreferences' ) === false && $preferencesKey !== '' )
                $keys['userpreferences'] = $preferencesKey;
            foreach ( $removed as $key )
                unset( $keys[$key] );
            return self::fingerprint( $keys );
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /**
     * The preferences of site.ini [ContentSettings] CachedViewPreferences[] with the user's values: a row per view mode
     * and preference, and the part userpreferences of every view mode together, as generateViewCacheFile() makes it
     * for each (eZNodeviewfunctions::userPreferencesKey()), "mode=part" joined by "|" in the order of the view modes'
     * names. Two users with the same key get the same part in every view mode, whatever defaults the modes give.
     *
     * @param array $cachedViewPreferences view mode => "name=default;name2"
     * @param array $values the user's preferences (eZPreferences::values()), name => value
     * @return array rows (view_mode, name, value, default, set), key ('' when no view mode has preferences)
     */
    public static function preferences( array $cachedViewPreferences, array $values )
    {
        $rows = array();
        $keys = array();
        ksort( $cachedViewPreferences, SORT_STRING );
        foreach ( $cachedViewPreferences as $viewMode => $list )
        {
            if ( !is_string( $list ) || trim( $list ) === '' )
                continue;
            foreach ( explode( ';', $list ) as $pref )
            {
                $pref = explode( '=', $pref, 2 );
                $name = trim( $pref[0] );
                if ( $name === '' )
                    continue;
                // set as the key reads it: a value that is null counts as not set (isset())
                $set = isset( $values[$name] );
                $rows[] = array( 'view_mode' => (string)$viewMode, 'name' => $name, 'value' => $set ? (string)$values[$name] : null,
                                 'default' => isset( $pref[1] ) ? $pref[1] : null, 'set' => $set );
            }
            $keys[] = $viewMode . '=' . eZNodeviewfunctions::userPreferencesKey( $list, $values );
        }
        return array( 'rows' => $rows, 'key' => implode( '|', $keys ) );
    }

    /**
     * A short fingerprint of named parts: the same parts, in the same order, give the same fingerprint.
     *
     * @param array $parts name => value
     * @return string FINGERPRINT_LENGTH hex digits
     */
    public static function fingerprint( array $parts )
    {
        $text = '';
        foreach ( $parts as $name => $value )
            $text .= $name . '=' . self::scalarText( $value ) . "\n";
        return substr( md5( $text ), 0, self::FINGERPRINT_LENGTH );
    }

    /**
     * The role assignments of the user and of their groups, each with the role, its limitation and where it comes
     * from (the user or a group, by content object id and name). Empty when the assignments cannot be read.
     *
     * @param eZUser $user
     * @return array of array( role_id, role, limit_identifier, limit_value, from_id, from_name, direct )
     */
    public static function assignments( $user )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        $ids = array_values( array_unique( array_map( 'intval', array_merge( (array)$user->groups(), array( $userID ) ) ) ) );
        try
        {
            $db = eZDB::instance();
            $in = $db->generateSQLINStatement( $ids, 'ezuser_role.contentobject_id', false, false, 'int' );
            $rows = $db->arrayQuery( "SELECT ezuser_role.contentobject_id, ezuser_role.role_id, ezrole.name, ezuser_role.limit_identifier, ezuser_role.limit_value
                                      FROM ezuser_role, ezrole
                                      WHERE ezuser_role.role_id = ezrole.id AND $in" );
        }
        catch ( Throwable $e )
        {
            return array();
        }
        if ( !is_array( $rows ) )
            return array();

        $names = array();
        $list = array();
        foreach ( $rows as $row )
        {
            $from = (int)$row['contentobject_id'];
            if ( !array_key_exists( $from, $names ) )
            {
                $object = $from === $userID ? null : eZContentObject::fetch( $from );
                $names[$from] = $object ? (string)$object->attribute( 'name' ) : '';
            }
            $list[] = array( 'role_id'          => (int)$row['role_id'],
                             'role'             => (string)$row['name'],
                             'limit_identifier' => (string)$row['limit_identifier'],
                             'limit_value'      => (string)$row['limit_value'],
                             'from_id'          => $from,
                             'from_name'        => $names[$from],
                             'direct'           => $from === $userID );
        }
        usort( $list, function ( $a, $b ) {
            return $a['role_id'] <=> $b['role_id'] ?: strcmp( $a['limit_identifier'] . $a['limit_value'], $b['limit_identifier'] . $b['limit_value'] ) ?: $a['from_id'] <=> $b['from_id'];
        } );
        return $list;
    }

    /**
     * Whether the current request may see the User and the Rendering tab. A user with setup/setup always. With
     * debugbar.ini ReadAccess=debug (the default, expDebugBarRegistry::readAccess()) whoever gets the debug output
     * (DebugByIP, DebugByUser), logged in or not: the tabs hold the user's own data (their roles, profile, preferences
     * and session; an anonymous visitor's are those of every anonymous visitor). With ReadAccess=privileged, as before,
     * besides setup/setup only an anonymous visitor who gets the debug output.
     *
     * @param bool $canSetup the user has setup/setup
     * @param bool $loggedIn the user is logged in (not the anonymous user)
     * @param bool $debugOutput the request gets the debug output
     * @param string|null $readAccess 'debug' or 'privileged' (null: expDebugBarRegistry::readAccess())
     * @return bool
     */
    public static function mayView( $canSetup, $loggedIn, $debugOutput, $readAccess = null )
    {
        if ( $canSetup )
            return true;
        if ( $readAccess === null )
            $readAccess = class_exists( 'expDebugBarRegistry' ) ? expDebugBarRegistry::readAccess() : 'privileged';
        return (bool)$debugOutput && ( $readAccess === 'debug' || !$loggedIn );
    }

    /**
     * The profile as plain text, one name=value per line, to set two users' side by side.
     *
     * @param array $data data()
     * @return string
     */
    public static function text( array $data )
    {
        $lines = array( 'user_id=' . $data['id'], 'siteaccess=' . $data['siteaccess'] );
        if ( $data['tweak'] !== '' )
            $lines[] = 'viewcachetweaks=' . $data['tweak'];
        foreach ( $data['keys'] as $name => $value )
            $lines[] = $name . '=' . $value;
        if ( !empty( $data['per_user'] ) )
            $lines[] = 'pr_user=' . $data['id'];
        foreach ( isset( $data['removed'] ) ? $data['removed'] : array() as $name )
            $lines[] = $name . '=(left out by a listener)';
        $lines[] = 'profile=' . $data['profile'];
        if ( $data['view_keys'] )
        {
            $lines[] = 'view=node ' . $data['node_id'];
            foreach ( $data['view_keys'] as $name => $value )
                $lines[] = $name . '=' . $value;
            $lines[] = 'view_profile=' . $data['view_profile'];
        }
        return implode( "\n", $lines ) . "\n";
    }

    /** A key's value as the view cache hashes it: a string. */
    protected static function scalarText( $value )
    {
        if ( $value === null )
            return '';
        if ( is_bool( $value ) )
            return $value ? '1' : '';
        return is_scalar( $value ) ? (string)$value : '';
    }
}
