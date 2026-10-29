<?php
/**
 * File containing the expUserSessionTracker class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The signed-in sessions of a user ("Your devices", user/sessions): every sign-in gets a row in expuser_session
 * (expUserSession), the user sees where they are signed in and signs out one session or all others; an administrator
 * with the policy user/sessions does the same for any user (user/sessions/<user id>).
 *
 * How a session is found and ended, on every session handler (PHP files, the database, Symfony):
 *
 * - signedIn() (eZUser::setCurrentlyLoggedInUser()) puts a random token into the session (SESSION_KEY) and stores
 *   only its SHA-256 with the row. The session id is never stored: it changes (regenerate) and would be a key to
 *   the session in the database.
 * - sessionIsCurrent() (eZUser::currentUser(), once per request) finds the row by the token: when it is ended or
 *   gone, the session is signed out at once (endRevokedSession(): eZUser::logoutCurrent() removes the session's data,
 *   id and cookie). Remember me is the session cookie itself, kept for [Session] RememberMeTimeout: a revoked
 *   session's cookie signs nobody in again, on the browser it was set in or on any other.
 * - revoke() / revokeOthers() / revokeAll() end rows; with the database session handler the other sessions' rows
 *   are deleted at once as well (ezpSessionHandler::deleteOtherSessionsOfUser()).
 * - A session signed in before the table existed (no token) is adopted on its next request, method "unknown".
 *
 * A sign-in from a browser and system the user has not used before (no row of the same browser, system and device
 * kind within the retention) sends "New sign-in to your account" to the user (category security, opt-out on the
 * page: preference expsession_signin_mail) and, when [UserSessionSettings] NewDeviceAdminMail=enabled, a note to
 * AdminMailReceivers[]. The first sign-in a user ever has here is not a "new device" (nothing to compare with).
 *
 * The cronjob usersessions.php (frequent and infrequent parts) ends the rows not used for [Session] SessionTimeout and
 * removes the ended ones after [UserSessionSettings] RetentionDays and those of removed users. Audit: the sign-in and
 * the sign-out keep their events (access.session.login, access.session.logout, now with the session's row id);
 * ending a session from the page is access.session.revoke, a sign-in from a new device access.session.new_device.
 *
 * Statics are per request (keyed by the request's start time), so persistent workers (Velocity) carry nothing over.
 * Guide: doc/eng-us/features/6.0/user-sessions.md, doc/eng-us/guides/your-devices-and-sessions.md
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserSessionTracker
{
    /** The session variable that holds the token of the sign-in */
    const SESSION_KEY = 'expUserSessionToken';

    /** The user preference that turns the "new sign-in" mail off ('0'); unset or '1' is on */
    const MAIL_PREFERENCE = 'expsession_signin_mail';

    /** The policy function that lets an administrator see and end other users' sessions */
    const ADMIN_FUNCTION = 'sessions';

    const AUDIT_REVOKE = 'access.session.revoke';
    const AUDIT_NEW_DEVICE = 'access.session.new_device';

    /** @var string|null a method an extension names for the sign-in being made (setMethodHint()) */
    protected static $methodHint = null;

    /** @var array request key => array( token hash => bool ) the checks of this request */
    protected static $checked = array();

    /** @var array|null array( time, bool ) whether the table was there, and when that was asked */
    protected static $tableKnown = null;

    /** @var bool a revoked session is being signed out (the logout asks for the current user again) */
    protected static $ending = false;

    /** @var int|null the row of this request's sign-in */
    protected static $currentRowID = null;

    /** @var array|null the mails sent in this request (tests) */
    public static $sentMails = array();

    /**
     * A setting of [UserSessionSettings].
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $name, $default = null )
    {
        $ini = eZINI::instance();
        return $ini->hasVariable( 'UserSessionSettings', $name ) ? $ini->variable( 'UserSessionSettings', $name ) : $default;
    }

    /** @return bool [UserSessionSettings] Tracking is not disabled */
    public static function enabled()
    {
        return self::setting( 'Tracking', 'enabled' ) !== 'disabled';
    }

    /**
     * Whether the table is there (asked again after five minutes when it was not, so an update reaches a running
     * worker without a restart).
     *
     * @return bool
     */
    public static function available()
    {
        $now = time();
        if ( self::$tableKnown !== null && ( self::$tableKnown[1] || $now - self::$tableKnown[0] < 300 ) )
            return self::$tableKnown[1];
        $exists = false;
        try
        {
            if ( class_exists( 'expKernelTableSchema' ) )
                $exists = expKernelTableSchema::exists( expUserSession::TABLE, null, true ) === true;
        }
        catch ( Throwable $e )
        {
            $exists = false;
        }
        self::$tableKnown = array( $now, $exists );
        return $exists;
    }

    /** Forgets what this process knows (tests, after the table was created). */
    public static function reset()
    {
        self::$tableKnown = null;
        self::$checked = array();
        self::$currentRowID = null;
        self::$methodHint = null;
        self::$sentMails = array();
    }

    /**
     * Lets an extension name the method of the sign-in it is about to make (eZUser::setCurrentlyLoggedInUser()):
     * two_factor, single_sign_on ... Used once.
     *
     * @param string $method one of expUserSession::METHODS
     */
    public static function setMethodHint( $method )
    {
        self::$methodHint = in_array( $method, expUserSession::METHODS, true ) ? $method : null;
    }

    /**
     * The method of a sign-in made in this request, from the hint, the entry script and the view.
     *
     * @return string
     */
    public static function detectMethod()
    {
        if ( self::$methodHint !== null )
        {
            $method = self::$methodHint;
            self::$methodHint = null;
            return $method;
        }
        $script = basename( (string)eZSys::serverVariable( 'SCRIPT_NAME', true ) );
        if ( $script === 'soap.php' )
            return 'soap';
        // index_webdav.php, or webdav.php, its old name kept as a forwarding shim
        if ( $script === 'index_webdav.php' || $script === 'webdav.php' )
            return 'webdav';
        if ( $script === 'index_rest.php' )
            return 'rest';
        $module = '';
        $view = '';
        if ( isset( $GLOBALS['eZRequestedURI'] ) && $GLOBALS['eZRequestedURI'] instanceof eZURI )
        {
            $module = (string)$GLOBALS['eZRequestedURI']->element( 0, false );
            $view = (string)$GLOBALS['eZRequestedURI']->element( 1, false );
        }
        return self::methodFor( $module, $view );
    }

    /**
     * The method of a sign-in made by a view.
     *
     * @param string $module
     * @param string $view
     * @return string
     */
    public static function methodFor( $module, $view )
    {
        $module = strtolower( (string)$module );
        if ( $module === 'user2fa' || strpos( $module, '2fa' ) !== false )
            return 'two_factor';
        if ( $module === 'api' || $module === 'rest' )
            return 'rest';
        if ( $module === 'user' && in_array( $view, array( 'login', 'activate', 'register', 'password', 'forgotpassword' ), true ) )
            return 'password';
        if ( $module === 'expservices' )
            return 'rest';
        return $module === '' ? 'unknown' : 'single_sign_on';
    }

    /** @return string|null the token of this session, when it has one */
    public static function currentToken()
    {
        if ( !class_exists( 'eZSession' ) || !eZSession::hasStarted() )
            return null;
        $token = eZSession::get( self::SESSION_KEY, false );
        return is_string( $token ) && preg_match( '/^[0-9a-f]{64}$/', $token ) ? $token : null;
    }

    /**
     * @param string $token
     * @return string the stored form of a token
     */
    public static function hashToken( $token )
    {
        return hash( 'sha256', (string)$token );
    }

    /** @return string|null the stored form of this session's token */
    public static function currentTokenHash()
    {
        $token = self::currentToken();
        return $token === null ? null : self::hashToken( $token );
    }

    /** @return int|null the row of this session */
    public static function currentRowID()
    {
        if ( self::$currentRowID !== null )
            return self::$currentRowID;
        $hash = self::currentTokenHash();
        if ( $hash === null || !self::available() )
            return null;
        $row = expUserSession::fetchByTokenHash( $hash );
        return $row ? (int)$row->attribute( 'id' ) : null;
    }

    /**
     * After a sign-in (eZUser::setCurrentlyLoggedInUser()): a new row with a new token for this session, the row a
     * previous sign-in of the same session had ended as replaced, and the "new device" mails.
     *
     * @param eZUser $user
     * @param string|null $method null: detectMethod()
     * @return expUserSession|null
     */
    public static function signedIn( $user, $method = null )
    {
        if ( !$user instanceof eZUser || $user->isAnonymous() || !self::enabled() )
            return null;
        try
        {
            if ( !class_exists( 'eZSession' ) || !eZSession::hasStarted() || !self::available() )
                return null;
            $userID = (int)$user->attribute( 'contentobject_id' );
            $method = $method === null ? self::detectMethod() : $method;
            $old = self::currentTokenHash();
            if ( $old !== null )
            {
                $previous = expUserSession::fetchByTokenHash( $old );
                if ( $previous && $previous->isActive() )
                    $previous->end( 'replaced', $userID );
            }
            $row = self::create( $userID, $method );
            if ( $row && self::isNewDevice( $userID, $row ) )
                self::newDevice( $user, $row );
            return $row;
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not record the sign-in: ' . $e->getMessage(), __METHOD__ );
            return null;
        }
    }

    /**
     * A row for this session, with a new token in the session.
     *
     * @param int $userID
     * @param string $method
     * @return expUserSession|null
     */
    protected static function create( $userID, $method )
    {
        $token = bin2hex( random_bytes( 32 ) );
        eZSession::set( self::SESSION_KEY, $token );
        $now = time();
        $ua = expUserAgent::clean( eZSys::serverVariable( 'HTTP_USER_AGENT', true ) );
        $parsed = expUserAgent::parse( $ua );
        $ip = mb_substr( (string)eZSys::clientIP(), 0, 64 );
        $lifetime = (int)eZSession::get( eZSession::COOKIE_LIFETIME_KEY, 0 );
        $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        $row = new expUserSession( array(
            'user_id' => (int)$userID, 'token_hash' => self::hashToken( $token ), 'created' => $now, 'last_active' => $now,
            'first_ip' => $ip, 'last_ip' => $ip, 'user_agent' => $ua,
            'browser' => mb_substr( $parsed['browser'], 0, 64 ), 'os' => mb_substr( $parsed['os'], 0, 64 ), 'device_type' => $parsed['device'],
            'method' => in_array( $method, expUserSession::METHODS, true ) ? $method : 'unknown',
            'remember_until' => $lifetime > 0 ? $now + $lifetime : 0, 'siteaccess' => mb_substr( $access, 0, 64 ),
            'ended' => 0, 'ended_by' => 0, 'end_reason' => '',
        ) );
        $row->store();
        self::$currentRowID = (int)$row->attribute( 'id' );
        self::$checked = array( self::requestKey() => array( $row->attribute( 'token_hash' ) => true ) );
        return $row;
    }

    /**
     * Whether this session may go on as the user (eZUser::currentUser()): false when its row was ended (signed out
     * elsewhere, revoked) or removed. A session without a token is adopted. Every check is one query by the token's
     * hash; the row's last use is written at most every [UserSessionSettings] TouchInterval seconds.
     *
     * @param eZUser $user
     * @return bool
     */
    public static function sessionIsCurrent( $user )
    {
        if ( self::$ending || !$user instanceof eZUser || !self::enabled() || !class_exists( 'eZSession' ) || !eZSession::hasStarted() )
            return true;
        $id = (int)$user->attribute( 'contentobject_id' );
        // a temporary switch to another user (preview caches) is not a sign-in
        if ( (int)eZSession::get( 'eZUserLoggedInID', 0 ) !== $id || $user->isAnonymous() )
            return true;
        try
        {
            if ( !self::available() )
                return true;
            $token = self::currentToken();
            if ( $token === null )
            {
                // signed in before the sessions were tracked: from now on it is
                self::create( $id, 'unknown' );
                return true;
            }
            $hash = self::hashToken( $token );
            $key = self::requestKey();
            if ( isset( self::$checked[$key][$hash] ) )
                return self::$checked[$key][$hash];
            $row = expUserSession::fetchByTokenHash( $hash );
            $ok = $row !== null && $row->isActive() && (int)$row->attribute( 'user_id' ) === $id;
            if ( $ok )
                self::touch( $row );
            self::$checked = array( $key => array( $hash => $ok ) );
            return $ok;
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not check the session: ' . $e->getMessage(), __METHOD__ );
            return true;
        }
    }

    /**
     * Writes the last use and address of a row, when TouchInterval has passed or the address changed.
     *
     * @param expUserSession $row
     */
    protected static function touch( expUserSession $row )
    {
        self::$currentRowID = (int)$row->attribute( 'id' );
        $now = time();
        $ip = mb_substr( (string)eZSys::clientIP(), 0, 64 );
        $interval = max( 0, (int)self::setting( 'TouchInterval', 300 ) );
        if ( $now - (int)$row->attribute( 'last_active' ) < $interval && $ip === (string)$row->attribute( 'last_ip' ) )
            return;
        $db = eZPersistentObject::dbFor( expUserSession::definition() );
        if ( $db->databaseName() === 'mongo' )
        {
            $row->setAttribute( 'last_active', $now );
            $row->setAttribute( 'last_ip', $ip );
            $row->store( array( 'last_active', 'last_ip' ) );
            return;
        }
        // The last use is left out while another write holds the database (SQLite: one writer at a time), so a
        // page view does not wait for a publish and fail with "database is locked"; the next request writes it
        $written = $db->queryUnlessBusy( 'UPDATE ' . expUserSession::TABLE . ' SET last_active=' . (int)$now .
            ", last_ip='" . $db->escapeString( $ip ) . "' WHERE id=" . (int)$row->attribute( 'id' ) );
        if ( $written )
        {
            $row->setAttribute( 'last_active', $now );
            $row->setAttribute( 'last_ip', $ip );
        }
    }

    /**
     * Signs out this session because its row was ended or removed (eZUser::currentUser()).
     *
     * @param eZUser $user
     */
    public static function endRevokedSession( $user )
    {
        if ( self::$ending )
            return;
        self::$ending = true;
        try
        {
            eZUser::logoutCurrent();
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Ending a revoked session: ' . $e->getMessage(), __METHOD__ );
        }
        self::$ending = false;
        self::$checked = array();
        self::$currentRowID = null;
    }

    /**
     * Before a sign-out (eZUser::logoutCurrent()): the session's row ends as signed out, and the token leaves the
     * session.
     *
     * @return int|null the row that ended
     */
    public static function signedOut()
    {
        $id = null;
        try
        {
            $hash = self::currentTokenHash();
            if ( $hash !== null && self::available() )
            {
                $row = expUserSession::fetchByTokenHash( $hash );
                if ( $row )
                {
                    $id = (int)$row->attribute( 'id' );
                    if ( $row->isActive() )
                        $row->end( 'signout', (int)$row->attribute( 'user_id' ) );
                }
            }
            if ( class_exists( 'eZSession' ) && eZSession::hasStarted() )
                eZSession::unsetkey( self::SESSION_KEY, false );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not record the sign-out: ' . $e->getMessage(), __METHOD__ );
        }
        self::$checked = array();
        self::$currentRowID = null;
        return $id;
    }

    /**
     * Ends one session of a user.
     *
     * @param int $userID whose session
     * @param int $sessionID the row
     * @param int $actorID who ends it
     * @return bool whether a running session of that user was ended
     */
    public static function revoke( $userID, $sessionID, $actorID )
    {
        $row = expUserSession::fetchByID( (int)$sessionID );
        if ( !$row || (int)$row->attribute( 'user_id' ) !== (int)$userID || !$row->isActive() )
            return false;
        $admin = (int)$actorID !== (int)$userID;
        $row->end( $admin ? 'revoked_admin' : 'revoked', (int)$actorID );
        self::forgetChecks();
        self::auditRevoke( (int)$userID, (int)$actorID, array( (int)$sessionID ), $admin ? 'admin' : 'user', 'one' );
        return true;
    }

    /**
     * Ends every session of a user but this one (the user's own page).
     *
     * @param int $userID
     * @param int $actorID
     * @return int how many were ended
     */
    public static function revokeOthers( $userID, $actorID )
    {
        $ended = self::endOtherRows( $userID, 'revoked_others', $actorID );
        // the database session handler ends the others' backend rows too (sessions signed in before the tracking)
        try
        {
            if ( class_exists( 'eZSession' ) && eZSession::hasStarted() && (int)$userID === (int)eZSession::get( 'eZUserLoggedInID', 0 ) )
                eZSession::getHandlerInstance()->deleteOtherSessionsOfUser( (int)$userID, (string)session_id() );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeNotice( 'The session handler could not end the other sessions: ' . $e->getMessage(), __METHOD__ );
        }
        self::forgetChecks();
        if ( $ended )
            self::auditRevoke( (int)$userID, (int)$actorID, $ended, 'user', 'others' );
        return count( $ended );
    }

    /**
     * Ends the rows of a user's sessions except this session's, without an audit event of its own (the caller
     * records why: revokeOthers(), a password change in expPasswordPolicy::endOtherSessions()).
     *
     * @param int $userID
     * @param string $reason one of expUserSession::REASONS
     * @param int $actorID
     * @return int[] the rows ended
     */
    public static function endOtherRows( $userID, $reason, $actorID )
    {
        $ended = array();
        if ( !self::enabled() || !self::available() )
            return $ended;
        $keep = self::currentTokenHash();
        foreach ( expUserSession::fetchForUser( (int)$userID, true, 1000 ) as $row )
        {
            if ( $keep !== null && hash_equals( (string)$row->attribute( 'token_hash' ), $keep ) )
                continue;
            $row->end( $reason, (int)$actorID );
            $ended[] = (int)$row->attribute( 'id' );
        }
        self::forgetChecks();
        return $ended;
    }

    /**
     * Ends every session of a user (an administrator, for another user).
     *
     * @param int $userID
     * @param int $actorID
     * @return int how many were ended
     */
    public static function revokeAll( $userID, $actorID )
    {
        $ended = array();
        $keep = (int)$userID === (int)$actorID ? self::currentTokenHash() : null;
        foreach ( expUserSession::fetchForUser( (int)$userID, true, 1000 ) as $row )
        {
            if ( $keep !== null && hash_equals( (string)$row->attribute( 'token_hash' ), $keep ) )
                continue;
            $row->end( 'revoked_admin', (int)$actorID );
            $ended[] = (int)$row->attribute( 'id' );
        }
        try
        {
            if ( (int)$userID !== (int)$actorID && class_exists( 'eZSession' ) )
                eZSession::getHandlerInstance()->deleteByUserIDs( array( (int)$userID ) );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeNotice( 'The session handler could not end the sessions: ' . $e->getMessage(), __METHOD__ );
        }
        self::forgetChecks();
        if ( $ended )
            self::auditRevoke( (int)$userID, (int)$actorID, $ended, 'admin', 'all' );
        return count( $ended );
    }

    /** Forgets the checks of this request (after a revoke: a session revoked in this very request ends now). */
    protected static function forgetChecks()
    {
        self::$checked = array();
    }

    /**
     * @param int $userID
     * @param int $actorID
     * @param int[] $sessionIDs
     * @param string $by user, admin
     * @param string $scope one, others, all
     */
    protected static function auditRevoke( $userID, $actorID, array $sessionIDs, $by, $scope )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        expAuditHook::emit( self::AUDIT_REVOKE, function () use ( $userID, $actorID, $sessionIDs, $by, $scope ) {
            return array( 'object' => expAuditHook::user( $userID ), 'reason' => $by === 'admin' ? 'admin' : 'user_request',
                          'after' => array( 'count' => count( $sessionIDs ), 'sessions' => array_values( $sessionIDs ), 'scope' => $scope,
                                            'by' => $by, 'actor_id' => (int)$actorID ) );
        } );
    }

    /**
     * Whether a sign-in is from a browser, system and device kind the user has not signed in with before (the rows
     * within the retention). The first sign-in a user has here is not new (nothing to compare with), unless
     * [UserSessionSettings] FirstSignInMail=enabled.
     *
     * @param int $userID
     * @param expUserSession $row the new row
     * @return bool
     */
    public static function isNewDevice( $userID, expUserSession $row )
    {
        $others = array();
        foreach ( expUserSession::fetchForUser( (int)$userID, false, 500 ) as $other )
            if ( (int)$other->attribute( 'id' ) !== (int)$row->attribute( 'id' ) )
                $others[] = array( 'browser' => (string)$other->attribute( 'browser' ), 'os' => (string)$other->attribute( 'os' ),
                                   'device_type' => (string)$other->attribute( 'device_type' ) );
        return self::isNewAmong( array( 'browser' => (string)$row->attribute( 'browser' ), 'os' => (string)$row->attribute( 'os' ),
                                        'device_type' => (string)$row->attribute( 'device_type' ) ), $others,
                                 self::setting( 'FirstSignInMail', 'disabled' ) === 'enabled' );
    }

    /**
     * The rule of isNewDevice() without a database.
     *
     * @param array $device browser, os, device_type
     * @param array[] $earlier the same of the earlier sign-ins
     * @param bool $firstCounts the first sign-in counts as new
     * @return bool
     */
    public static function isNewAmong( array $device, array $earlier, $firstCounts = false )
    {
        if ( !$earlier )
            return (bool)$firstCounts;
        $key = strtolower( $device['browser'] . '|' . $device['os'] . '|' . $device['device_type'] );
        foreach ( $earlier as $other )
            if ( strtolower( $other['browser'] . '|' . $other['os'] . '|' . $other['device_type'] ) === $key )
                return false;
        return true;
    }

    /**
     * A sign-in from a new device: the audit event and the mails.
     *
     * @param eZUser $user
     * @param expUserSession $row
     */
    protected static function newDevice( eZUser $user, expUserSession $row )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( self::AUDIT_NEW_DEVICE, function () use ( $user, $row ) {
                return array( 'object' => expAuditHook::user( $user ),
                              'after' => array( 'session' => (int)$row->attribute( 'id' ), 'browser' => (string)$row->attribute( 'browser' ),
                                                'os' => (string)$row->attribute( 'os' ), 'device' => (string)$row->attribute( 'device_type' ),
                                                'method' => (string)$row->attribute( 'method' ) ) );
            } );
        if ( self::userMailWanted( $user ) )
            self::sendUserMail( $user, $row );
        if ( self::setting( 'NewDeviceAdminMail', 'disabled' ) === 'enabled' )
            self::sendAdminMail( $user, $row );
    }

    /**
     * Whether the user gets "New sign-in to your account": [UserSessionSettings] NewDeviceMail is on and the user did
     * not turn it off.
     *
     * @param eZUser $user
     * @return bool
     */
    public static function userMailWanted( eZUser $user )
    {
        if ( self::setting( 'NewDeviceMail', 'enabled' ) !== 'enabled' )
            return false;
        return self::userPreference( $user );
    }

    /**
     * The user's own choice (preference expsession_signin_mail): on unless turned off.
     *
     * @param eZUser $user
     * @return bool
     */
    public static function userPreference( eZUser $user )
    {
        try
        {
            $value = eZPreferences::value( self::MAIL_PREFERENCE, $user );
        }
        catch ( Throwable $e )
        {
            $value = false;
        }
        return $value === false || $value === null || (string)$value !== '0';
    }

    /**
     * The variables of the mail templates.
     *
     * @param eZUser $user
     * @param expUserSession $row
     * @return array
     */
    protected static function mailVariables( eZUser $user, expUserSession $row )
    {
        $url = 'user/sessions';
        eZURI::transformURI( $url, false, 'full' );
        return array( 'user' => $user, 'session' => $row, 'device' => $row->label(), 'device_type' => (string)$row->attribute( 'device_type' ),
                      'ip' => $row->approximateIP(), 'signed_in_at' => (int)$row->attribute( 'created' ),
                      'method' => (string)$row->attribute( 'method' ), 'sessions_url' => $url );
    }

    /**
     * Sends "New sign-in to your account" (design:user/new_signin_mail.tpl), category security.
     *
     * @param eZUser $user
     * @param expUserSession $row
     * @return bool handed to the transport
     */
    public static function sendUserMail( eZUser $user, expUserSession $row )
    {
        $email = (string)$user->attribute( 'email' );
        if ( !eZMail::validate( $email ) )
            return false;
        $vars = self::mailVariables( $user, $row );
        $subject = ezpI18n::tr( 'kernel/user/sessions/mail', 'New sign-in to your account' );
        $body = ezpI18n::tr( 'kernel/user/sessions/mail', 'Your account %login was used to sign in from %device (%ip) on %date.', null,
                             array( '%login' => $user->attribute( 'login' ), '%device' => $vars['device'], '%ip' => $vars['ip'],
                                    '%date' => date( 'Y-m-d H:i', $vars['signed_in_at'] ) ) ) . "\n\n"
              . ezpI18n::tr( 'kernel/user/sessions/mail', 'If this was you, you do not need to do anything. If not, sign this session out at %url and change your password.', null,
                             array( '%url' => $vars['sessions_url'] ) ) . "\n";
        return self::send( $email, 'design:user/new_signin_mail.tpl', $vars, $subject, $body, 'security' );
    }

    /**
     * Sends the note to the administrators ([UserSessionSettings] AdminMailReceivers[], else [MailSettings] AdminEmail),
     * category admin.
     *
     * @param eZUser $user
     * @param expUserSession $row
     * @return bool
     */
    public static function sendAdminMail( eZUser $user, expUserSession $row )
    {
        $receivers = array_filter( (array)self::setting( 'AdminMailReceivers', array() ), function ( $a ) { return eZMail::validate( (string)$a ); } );
        if ( !$receivers )
        {
            $admin = (string)eZINI::instance()->variable( 'MailSettings', 'AdminEmail' );
            if ( eZMail::validate( $admin ) )
                $receivers = array( $admin );
        }
        if ( !$receivers )
            return false;
        $vars = self::mailVariables( $user, $row );
        $url = 'user/sessions/' . (int)$user->attribute( 'contentobject_id' );
        eZURI::transformURI( $url, false, 'full' );
        $vars['admin_url'] = $url;
        $subject = ezpI18n::tr( 'kernel/user/sessions/mail', 'New sign-in of %login', null, array( '%login' => $user->attribute( 'login' ) ) );
        $body = ezpI18n::tr( 'kernel/user/sessions/mail', 'The account %login signed in from a new device: %device (%ip) on %date.', null,
                             array( '%login' => $user->attribute( 'login' ), '%device' => $vars['device'], '%ip' => $vars['ip'],
                                    '%date' => date( 'Y-m-d H:i', $vars['signed_in_at'] ) ) ) . "\n" . $url . "\n";
        $ok = true;
        foreach ( $receivers as $to )
            $ok = self::send( (string)$to, 'design:user/new_signin_admin_mail.tpl', $vars, $subject, $body, 'admin' ) && $ok;
        return $ok;
    }

    /**
     * @return bool
     */
    protected static function send( $email, $template, array $vars, $subject, $body, $category )
    {
        try
        {
            $mail = new eZMail();
            $rendered = class_exists( 'expMailPreferencesService' ) ? expMailPreferencesService::renderTemplate( $template, $vars ) : null;
            if ( $rendered !== null && trim( $rendered['body'] ) !== '' )
            {
                $body = $rendered['body'];
                if ( $rendered['subject'] !== null && trim( $rendered['subject'] ) !== '' )
                    $subject = trim( $rendered['subject'] );
                if ( $rendered['content_type'] )
                    $mail->setContentType( $rendered['content_type'] );
            }
            $ini = eZINI::instance();
            $sender = $ini->variable( 'MailSettings', 'EmailSender' );
            if ( !$sender )
                $sender = $ini->variable( 'MailSettings', 'AdminEmail' );
            $mail->setSender( $sender );
            $mail->setReceiver( $email );
            $mail->setSubject( $subject );
            expMailTemplateBody::applyRendered( $mail, $rendered, $body );
            $mail->setCategory( $category );
            $ok = (bool)eZMailTransport::send( $mail );
            self::$sentMails[] = array( 'template' => $template, 'category' => $category, 'ok' => $ok );
            return $ok;
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not send the sign-in mail: ' . $e->getMessage(), __METHOD__ );
            return false;
        }
    }

    /**
     * The sessions of a user for the page: the running ones (this session first), and the ended ones of the last
     * days when $history.
     *
     * @param int $userID
     * @param bool $history
     * @return array active, ended (each a list of rows as arrays), current_id
     */
    public static function listForUser( $userID, $history = true )
    {
        $current = self::currentTokenHash();
        $active = array();
        $ended = array();
        $timeout = self::sessionTimeout();
        $now = time();
        foreach ( expUserSession::fetchForUser( (int)$userID, false, 200 ) as $row )
        {
            $item = self::describe( $row, $current );
            if ( $row->isActive() && ( $timeout <= 0 || $now - (int)$row->attribute( 'last_active' ) <= $timeout ) )
                $active[] = $item;
            elseif ( $history )
                $ended[] = $item;
        }
        usort( $active, function ( $a, $b ) {
            if ( $a['current'] !== $b['current'] )
                return $a['current'] ? -1 : 1;
            return $b['last_active'] <=> $a['last_active'];
        } );
        $currentID = 0;
        foreach ( $active as $item )
            if ( $item['current'] )
                $currentID = $item['id'];
        return array( 'active' => $active, 'ended' => array_slice( $ended, 0, 20 ), 'current_id' => $currentID );
    }

    /**
     * A row as the page shows it.
     *
     * @param expUserSession $row
     * @param string|null $currentHash
     * @return array
     */
    public static function describe( expUserSession $row, $currentHash = null )
    {
        $parsed = $row->parsed();
        return array(
            'id' => (int)$row->attribute( 'id' ),
            'current' => $currentHash !== null && hash_equals( (string)$row->attribute( 'token_hash' ), (string)$currentHash ),
            'browser' => $parsed['browser'], 'browser_version' => $parsed['browser_version'],
            'os' => $parsed['os'], 'os_version' => $parsed['os_version'], 'device' => $parsed['device'],
            'label' => expUserAgent::label( $parsed ), 'user_agent' => (string)$row->attribute( 'user_agent' ),
            'created' => (int)$row->attribute( 'created' ), 'last_active' => (int)$row->attribute( 'last_active' ),
            'ip' => $row->approximateIP(), 'first_ip' => $row->approximateFirstIP(),
            'ip_full' => (string)$row->attribute( 'last_ip' ),
            'method' => (string)$row->attribute( 'method' ), 'remember_until' => (int)$row->attribute( 'remember_until' ),
            'siteaccess' => (string)$row->attribute( 'siteaccess' ), 'ended' => (int)$row->attribute( 'ended' ),
            'end_reason' => (string)$row->attribute( 'end_reason' ), 'ended_by' => (int)$row->attribute( 'ended_by' ),
        );
    }

    /** @return int [Session] SessionTimeout in seconds (0: none) */
    public static function sessionTimeout()
    {
        $ini = eZINI::instance();
        return $ini->hasVariable( 'Session', 'SessionTimeout' ) ? max( 0, (int)$ini->variable( 'Session', 'SessionTimeout' ) ) : 0;
    }

    /**
     * Retention (cronjob usersessions.php): ends the rows not used for [Session] SessionTimeout (the session is gone
     * on the server too), removes the ended rows older than [UserSessionSettings] RetentionDays and the rows of users
     * that no longer exist.
     *
     * @param int|null $now
     * @return array expired, removed, orphaned
     */
    public static function cleanup( $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $result = array( 'expired' => 0, 'removed' => 0, 'orphaned' => 0 );
        if ( !self::available() )
            return $result;
        $def = expUserSession::definition();
        $timeout = self::sessionTimeout();
        if ( $timeout > 0 )
        {
            do
            {
                $rows = expUserSession::fetchObjectList( $def, null, array( 'ended' => 0, 'last_active' => array( '<', $now - $timeout ) ),
                                                         array( 'id' => 'asc' ), array( 'offset' => 0, 'length' => 500 ) );
                foreach ( (array)$rows as $row )
                {
                    $row->end( 'expired', 0, (int)$row->attribute( 'last_active' ) + $timeout );
                    $result['expired']++;
                }
            } while ( is_array( $rows ) && count( $rows ) === 500 );
        }
        $days = max( 1, (int)self::setting( 'RetentionDays', 90 ) );
        $cut = $now - $days * 86400;
        do
        {
            $rows = expUserSession::fetchObjectList( $def, null, array( 'ended' => array( false, array( 1, $cut - 1 ) ) ), array( 'id' => 'asc' ),
                                                     array( 'offset' => 0, 'length' => 500 ) );
            foreach ( (array)$rows as $row )
            {
                $row->remove();
                $result['removed']++;
            }
        } while ( is_array( $rows ) && count( $rows ) === 500 );
        // the rows of removed users
        $users = array();
        foreach ( (array)expUserSession::fetchObjectList( $def, array( 'user_id' ), null, null, null, false, array( 'user_id' ) ) as $r )
            $users[] = (int)( is_array( $r ) ? $r['user_id'] : $r->attribute( 'user_id' ) );
        foreach ( array_unique( $users ) as $userID )
        {
            if ( $userID > 0 && eZUser::fetch( $userID ) instanceof eZUser )
                continue;
            foreach ( expUserSession::fetchForUser( $userID, false, 1000 ) as $row )
            {
                $row->remove();
                $result['orphaned']++;
            }
        }
        return $result;
    }

    /**
     * Whether a viewer may see and end another user's sessions (policy user/sessions).
     *
     * @param eZUser $viewer
     * @return bool
     */
    public static function canAdminister( eZUser $viewer )
    {
        $access = $viewer->hasAccessTo( 'user', self::ADMIN_FUNCTION );
        return $access['accessWord'] !== 'no';
    }

    /** @return string the key of this request in the per-request statics */
    protected static function requestKey()
    {
        $t = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : (string)( $_SERVER['REQUEST_TIME'] ?? '' );
        return $t . ':' . getmypid();
    }
}
?>
