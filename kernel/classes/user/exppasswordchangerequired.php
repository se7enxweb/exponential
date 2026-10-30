<?php
/**
 * File containing the expPasswordChangeRequired class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * "Change password at next sign-in": an administrator marks an account, and after its next sign-in the user is sent
 * to the password change form (user/password) before any other page, until a new password is set. Changing the
 * password clears the mark. The use: an administrator creates an account with a temporary password, hands it over by
 * a second channel, and the person sets a password only they know.
 *
 * The mark is a user preference (the ezpreferences table, name PREFERENCE, value the time it was set): a row per
 * account the kernel already keeps, read through eZPreferences, so no schema changes and every database engine
 * (MongoDB too). The session keeps the value it read until the next sign-in, which is why a mark set while the user is
 * signed in takes effect at their next sign-in. The view user/preferences refuses to write the name (isReserved()),
 * so the mark can only be set and cleared here: the account edit form (ezuser datatype), user/password, and the
 * console command exp:user-password-change-required.
 *
 * Where it is enforced: every web request (ezpKernelWeb::dispatchLoop(), moduleResult()) except the views of
 * site.ini [PasswordChangeRequiredSettings] ExemptViews[] (user/password, user/logout, the two-step sign-in views of
 * user2fa); the REST interface (ezpRestAuthConfiguration answers 403 password_change_required); WebDAV and SOAP
 * refuse the sign-in. Console commands are not affected. Two-step sign-in comes first: it runs before the user is
 * signed in, the password change after.
 *
 * Demo mode (EXP_DEMO_MODE=1, expDemoMode): an administrator's password cannot change there, so the mark is ignored
 * for administrators (isRequired()), or it would lock them out.
 *
 * Audit (doc/eng-us/bc/6.0/audit.md): access.user.password.change_required.set, .clear and .redirect.
 * Guide: doc/eng-us/features/6.0/password-change-at-next-sign-in.md
 */
class expPasswordChangeRequired
{
    /** The name of the user preference that holds the mark */
    const PREFERENCE = 'exp_password_change_required';

    /** The value of ezpAuth_reason for the REST interface, and the error code of its answer */
    const REST_ERROR = 'password_change_required';

    /** Where the user is sent */
    const PASSWORD_URI = '/user/password';

    /** The views that stay open while the mark is set, when site.ini has no list (a worker started before it had) */
    const DEFAULT_EXEMPT_VIEWS = array( 'user/password', 'user/logout', 'user2fa' );

    const AUDIT_SET = 'access.user.password.change_required.set';
    const AUDIT_CLEAR = 'access.user.password.change_required.clear';
    const AUDIT_REDIRECT = 'access.user.password.change_required.redirect';

    /**
     * Whether a stored preference value is a mark: a value that is set, not empty and not "0".
     *
     * @param mixed $value
     * @return bool
     */
    public static function isMark( $value )
    {
        if ( $value === null || $value === false || is_array( $value ) || is_object( $value ) )
            return false;
        $value = trim( (string)$value );
        return $value !== '' && $value !== '0';
    }

    /**
     * The decision itself, without a database: a signed-in account with the mark must change its password, except
     * an administrator in demo mode.
     *
     * @param bool $marked
     * @param bool $isAnonymous
     * @param bool $demoProtectsPassword expDemoMode::protectsPassword() of the account
     * @return bool
     */
    public static function decide( $marked, $isAnonymous, $demoProtectsPassword )
    {
        return (bool)$marked && !$isAnonymous && !$demoProtectsPassword;
    }

    /**
     * Whether the account carries the mark (whatever demo mode says).
     *
     * @param eZUser|int $user
     * @return bool
     */
    public static function isMarked( $user )
    {
        $user = self::user( $user );
        if ( !$user instanceof eZUser || $user->isAnonymous() )
            return false;
        return self::isMark( eZPreferences::value( self::PREFERENCE, $user ) );
    }

    /**
     * Whether the account must change its password before it may do anything else.
     *
     * @param eZUser|int $user
     * @return bool
     */
    public static function isRequired( $user )
    {
        $user = self::user( $user );
        if ( !$user instanceof eZUser || $user->isAnonymous() )
            return false;
        $marked = static::isMarked( $user );
        if ( !$marked )
            return false;
        $demo = static::demoProtects( $user );
        return self::decide( $marked, false, $demo );
    }

    /**
     * Whether demo mode keeps the account's password as it is (an administrator while EXP_DEMO_MODE=1): then the mark
     * is ignored, or it would lock the administrator out.
     *
     * @param eZUser $user
     * @return bool
     */
    public static function demoProtects( eZUser $user )
    {
        return class_exists( 'expDemoMode' ) && expDemoMode::protectsPassword( $user );
    }

    /**
     * Sets the mark on an account. Records access.user.password.change_required.set when the account had none.
     *
     * @param eZUser|int $user
     * @param string $source where it was set: edit, console ...
     * @return bool the account has the mark now
     */
    public static function set( $user, $source = 'edit' )
    {
        $user = self::user( $user );
        if ( !$user instanceof eZUser || $user->isAnonymous() )
            return false;
        if ( static::isMarked( $user ) )
            return true;
        $userID = (int)$user->attribute( 'contentobject_id' );
        if ( !self::store( $userID, (string)time() ) )
            return false;
        self::audit( self::AUDIT_SET, $user, array( 'source' => (string)$source ) );
        return true;
    }

    /**
     * Clears the mark of an account. Records access.user.password.change_required.clear when it had one.
     *
     * @param eZUser|int $user
     * @param string $source where it was cleared: password (the user changed it), edit, console ...
     * @return bool the account had the mark
     */
    public static function clear( $user, $source = 'password' )
    {
        $user = self::user( $user );
        if ( !$user instanceof eZUser || $user->isAnonymous() || !static::isMarked( $user ) )
            return false;
        // an empty value is no mark, on every engine (setValue() writes the session of the user too)
        if ( !self::store( (int)$user->attribute( 'contentobject_id' ), '' ) )
            return false;
        self::audit( self::AUDIT_CLEAR, $user, array( 'source' => (string)$source ) );
        return true;
    }

    /**
     * Sets or clears the mark as the account edit form asked (eZUserType::onPublish()), for another person's account
     * only: an account's owner cannot set or clear their own mark there.
     *
     * @param eZUser $account
     * @param bool|null $wanted null: the form did not show the choice, nothing changes
     * @param eZUser|null $editor the user who publishes (default: the current user)
     * @return bool|null the mark now, or null when nothing was changed
     */
    public static function applyFromEdit( eZUser $account, $wanted, $editor = null )
    {
        if ( $wanted === null )
            return null;
        if ( !$editor instanceof eZUser )
            $editor = eZUser::currentUser();
        if ( !self::editorMayChoose( $editor, $account ) )
            return null;
        if ( $wanted )
            return self::set( $account, 'edit' );
        self::clear( $account, 'edit' );
        return false;
    }

    /**
     * Whether $editor may set the mark of $account in the edit form: a signed-in user, not the account's owner.
     *
     * @param eZUser|null $editor
     * @param eZUser|null $account
     * @return bool
     */
    public static function editorMayChoose( $editor, $account )
    {
        if ( !$editor instanceof eZUser || !$account instanceof eZUser || $editor->isAnonymous() )
            return false;
        return (int)$editor->attribute( 'contentobject_id' ) !== (int)$account->attribute( 'contentobject_id' );
    }

    /**
     * Whether the checkbox of the account edit form is ticked: the choice of a draft, else ticked for an account that
     * is being created (never published), else the mark the account has.
     *
     * @param bool|null $draft the choice the form stored in the draft
     * @param bool $isNewAccount the user object was never published
     * @param bool $marked the account has the mark
     * @return bool
     */
    public static function checkboxState( $draft, $isNewAccount, $marked )
    {
        if ( $draft !== null )
            return (bool)$draft;
        return $isNewAccount ? true : (bool)$marked;
    }

    /**
     * The views that stay open while the mark is set: site.ini [PasswordChangeRequiredSettings] ExemptViews[].
     * user/password is always among them, or no one could ever clear the mark.
     *
     * @return array "module/view" or "module" (every view of the module)
     */
    public static function exemptViews()
    {
        $list = self::DEFAULT_EXEMPT_VIEWS;
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'PasswordChangeRequiredSettings', 'ExemptViews' ) )
                $list = (array)$ini->variable( 'PasswordChangeRequiredSettings', 'ExemptViews' );
        }
        $list[] = 'user/password';
        return array_values( array_unique( array_filter( array_map( 'strval', $list ), 'strlen' ) ) );
    }

    /**
     * Whether a view stays open: its "module/view" or its module is in the list.
     *
     * @param string $module
     * @param string $view
     * @param array $exempt see exemptViews()
     * @return bool
     */
    public static function isExempt( $module, $view, array $exempt )
    {
        $module = strtolower( trim( (string)$module, '/ ' ) );
        $view = strtolower( trim( (string)$view, '/ ' ) );
        foreach ( $exempt as $entry )
        {
            $entry = strtolower( trim( (string)$entry, '/ ' ) );
            if ( $entry === '' )
                continue;
            if ( $entry === $module || $entry === $module . '/' . $view || $entry === $module . '/*' )
                return true;
        }
        return false;
    }

    /**
     * The kernel's check before a view runs (ezpKernelWeb::dispatchLoop()): a redirect to user/password for a signed-in
     * account with the mark, on every siteaccess, or null to run the view.
     *
     * @param eZModule $module
     * @param string $view
     * @return array|null a module result
     */
    public static function moduleResult( $module, $view )
    {
        if ( !$module instanceof eZModule )
            return null;
        $moduleName = (string)$module->attribute( 'name' );
        if ( self::isExempt( $moduleName, (string)$view, self::exemptViews() ) )
            return null;
        $user = eZUser::currentUser();
        if ( !$user->isRegistered() || !self::isRequired( $user ) )
            return null;

        self::audit( self::AUDIT_REDIRECT, $user, array( 'channel' => 'web', 'view' => $moduleName . '/' . $view ) );
        // the answer depends on who asked: no shared cache, no browser keeps it
        if ( class_exists( 'ezpRequestRuleKernel' ) )
            ezpRequestRuleKernel::keepOutOfSharedCaches();
        $module->redirectTo( self::PASSWORD_URI );
        return array( 'content' => '' );
    }

    /**
     * For a sign-in outside the web pages (REST, WebDAV, SOAP): whether the account is refused because it must change
     * its password first. Records access.user.password.change_required.redirect with the channel.
     *
     * @param eZUser|null $user
     * @param string $channel rest, webdav, soap
     * @return bool refused
     */
    public static function refusesSignIn( $user, $channel )
    {
        if ( !$user instanceof eZUser || !self::isRequired( $user ) )
            return false;
        self::audit( self::AUDIT_REDIRECT, $user, array( 'channel' => (string)$channel, 'result' => 'refused' ) );
        return true;
    }

    /**
     * Whether a preference name may not be written through user/preferences.
     *
     * @param string $name
     * @return bool
     */
    public static function isReserved( $name )
    {
        return is_string( $name ) && strcasecmp( trim( $name ), self::PREFERENCE ) === 0;
    }

    /**
     * Writes the preference in a transaction of its own, as eZPreferences::setValue() asks (it reads, then writes):
     * a transaction waits for another writer where a lone statement can fail on a busy database (SQLite), and a
     * failed write is reported instead of taken as done.
     *
     * @param int $userID
     * @param string $value
     * @return bool written
     */
    protected static function store( $userID, $value )
    {
        $db = eZDB::instance();
        $db->begin();
        $stored = eZPreferences::setValue( self::PREFERENCE, $value, (int)$userID );
        $committed = $db->commit();
        if ( !$stored || $committed === false )
        {
            eZDebug::writeError( 'Could not store "change password at next sign-in" of user ' . (int)$userID . ': '
                                 . (string)$db->errorMessage(), __METHOD__ );
            return false;
        }
        return true;
    }

    /**
     * @param eZUser|int $user
     * @return eZUser|null
     */
    protected static function user( $user )
    {
        if ( $user instanceof eZUser )
            return $user;
        if ( is_numeric( $user ) && (int)$user > 0 )
        {
            $user = eZUser::fetch( (int)$user );
            return $user instanceof eZUser ? $user : null;
        }
        return null;
    }

    /**
     * @param string $name
     * @param eZUser $user
     * @param array $after
     */
    protected static function audit( $name, eZUser $user, array $after )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        $result = isset( $after['result'] ) ? $after['result'] : 'success';
        unset( $after['result'] );
        expAuditHook::emit( $name, function () use ( $user, $after, $result ) {
            return array( 'object' => expAuditHook::user( $user ), 'result' => $result, 'after' => $after );
        } );
    }
}

?>
