<?php
/**
 * Password re-entry before the audit's manage actions (doc/bc/6.0/audit.md, Q9; [AuditConsoleSettings]
 * ReauthForManage, ReauthMinutes).
 *
 * With ReauthForManage=enabled, a manage action of the console ("Verify now" on the dashboard, the console and
 * audit/recent) first shows a small form asking for the signed-in user's password. The form posts to the same
 * view with the same action button (and the form token, which ezformtoken adds to every POST form); the password
 * is checked against the user's stored hash with eZUser::authenticateHash(), the check of the kernel's sign-in,
 * without signing anyone in or out and without changing the session's user. A correct password is remembered in
 * the session for ReauthMinutes and for that user only; the action then runs. Every attempt is recorded:
 * access.session.reauth on success, access.session.reauth.failed (result failed, reason credentials; never the
 * password) on failure. Cancel goes back to the page without the action. The command line never asks: whoever
 * runs exp:audit already holds the server.
 *
 *   $form = expAuditReauth::gate( $Module, 'AuditVerifyNowButton', 'audit/dashboard', 'Verify now' );
 *   if ( $form !== null )
 *       return $form;      // the form, or the redirect of Cancel
 *   // ... the action
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditReauth
{
    /** The session variable: user id and the time until which the password counts as re-entered */
    const SESSION_KEY = 'ExpAuditReauth';

    /** POST fields of the form */
    const PASSWORD_FIELD = 'AuditReauthPassword';
    const CONFIRM_BUTTON = 'AuditReauthButton';
    const CANCEL_BUTTON = 'AuditReauthCancelButton';

    /** @var int|null a fixed clock for tests (Unix seconds) */
    protected static $now = null;

    /** @return bool ReauthForManage=enabled */
    public static function enabled()
    {
        try
        {
            return class_exists( 'expAuditIndexSettings' ) && !empty( expAuditIndexSettings::get()['reauth'] );
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /** @return int ReauthMinutes (at least 1) */
    public static function minutes()
    {
        try
        {
            return class_exists( 'expAuditIndexSettings' ) ? max( 1, (int)expAuditIndexSettings::get()['reauthMinutes'] ) : 10;
        }
        catch ( Throwable $e )
        {
            return 10;
        }
    }

    /**
     * Whether the user has to re-enter the password before a manage action now.
     *
     * @param eZUser|null $user the current user
     * @return bool
     */
    public static function required( $user = null )
    {
        if ( !self::enabled() )
            return false;
        $user = $user ?: eZUser::currentUser();
        return !self::isFresh( $user );
    }

    /**
     * Whether the user re-entered the password within ReauthMinutes in this session.
     *
     * @param eZUser $user
     * @return bool
     */
    public static function isFresh( $user )
    {
        if ( !$user instanceof eZUser )
            return false;
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( self::SESSION_KEY ) )
            return false;
        $v = $http->sessionVariable( self::SESSION_KEY );
        return is_array( $v ) && isset( $v['user'], $v['until'] ) && (int)$v['user'] === (int)$user->attribute( 'contentobject_id' )
               && (int)$v['until'] > self::now();
    }

    /**
     * Checks a password against the user's stored hash (the kernel's sign-in check), without signing in.
     *
     * @param eZUser $user
     * @param mixed $password
     * @return bool
     */
    public static function check( $user, $password )
    {
        if ( !$user instanceof eZUser || $user->isAnonymous() || !is_string( $password ) || $password === '' )
            return false;
        return (bool)eZUser::authenticateHash( (string)$user->attribute( 'login' ), $password, $user->site(),
                                               $user->attribute( 'password_hash_type' ), (string)$user->attribute( 'password_hash' ) );
    }

    /**
     * Checks a re-entered password, records the attempt and, when it is right, remembers it for ReauthMinutes.
     *
     * @param eZUser $user
     * @param mixed $password
     * @param string $action what the password is for (e.g. audit/dashboard: Verify now), for the record
     * @return bool
     */
    public static function confirm( $user, $password, $action )
    {
        $ok = self::check( $user, $password );
        if ( $ok )
            eZHTTPTool::instance()->setSessionVariable( self::SESSION_KEY, array( 'user' => (int)$user->attribute( 'contentobject_id' ),
                                                                                  'until' => self::now() + 60 * self::minutes() ) );
        if ( class_exists( 'expAuditHook' ) )
        {
            $data = array( 'object' => $user instanceof eZUser ? expAuditHook::user( $user ) : array( 'type' => 'user' ),
                           'after' => array( 'action' => (string)$action ) );
            if ( !$ok )
                $data += array( 'result' => 'failed', 'reason' => 'credentials' );
            else
                $data['after']['minutes'] = self::minutes();
            expAuditHook::emit( $ok ? 'access.session.reauth' : 'access.session.reauth.failed', $data );
        }
        return $ok;
    }

    /** Forgets a re-entered password (sign-out does that too: the session ends). */
    public static function forget()
    {
        $http = eZHTTPTool::instance();
        if ( $http->hasSessionVariable( self::SESSION_KEY ) )
            $http->removeSessionVariable( self::SESSION_KEY );
    }

    /**
     * Before a manage action: null when it may run now (no re-entry needed, still fresh, or the password was just
     * confirmed), else the module result to return instead: the form (again, with a message after a wrong
     * password), or the redirect back to the page after Cancel.
     *
     * @param eZModule $module
     * @param string $button the POST button of the action (sent again by the form)
     * @param string $url the view the form posts to and Cancel returns to (audit/console/(name)/...)
     * @param string $label the action, as the button says it
     * @param eZUser|null $user null: the current user
     * @return array|null
     */
    public static function gate( $module, $button, $url, $label, $user = null )
    {
        $user = $user ?: eZUser::currentUser();
        if ( !self::required( $user ) )
            return null;
        $http = eZHTTPTool::instance();
        $url = ltrim( (string)$url, '/' );
        if ( $http->hasPostVariable( self::CANCEL_BUTTON ) )
            return $module->redirectTo( '/' . $url );
        $failed = false;
        if ( $http->hasPostVariable( self::CONFIRM_BUTTON ) )
        {
            $password = $http->postVariable( self::PASSWORD_FIELD );
            if ( self::confirm( $user, $password, $url . ': ' . $label ) )
                return null;
            $failed = true;
        }
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'action_url', $url );
        $tpl->setVariable( 'button', (string)$button );
        $tpl->setVariable( 'label', (string)$label );
        $tpl->setVariable( 'failed', $failed );
        $tpl->setVariable( 'minutes', self::minutes() );
        $tpl->setVariable( 'login', (string)$user->attribute( 'login' ) );
        $title = ezpI18n::tr( 'design/admin/audit', 'Confirm your password' );
        if ( class_exists( 'expAuditConsole' ) )
            return expAuditConsole::result( $tpl->fetch( 'design:audit/reauth.tpl' ), $title );
        return array( 'content' => $tpl->fetch( 'design:audit/reauth.tpl' ), 'path' => array( array( 'text' => $title, 'url' => false ) ) );
    }

    /** Sets a fixed clock (tests): Unix seconds, or null for the real one. */
    public static function setNow( $now )
    {
        self::$now = $now === null ? null : (int)$now;
    }

    /** @return int */
    protected static function now()
    {
        return self::$now !== null ? self::$now : time();
    }
}
