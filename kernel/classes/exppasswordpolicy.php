<?php
/**
 * File containing the expPasswordPolicy class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The rules a new password must meet, and what happens after a password was changed.
 *
 * The length rule is site.ini [UserSettings] MinPasswordLength (3 when it is not set, as before). The optional
 * rules are site.ini [PasswordSettings]; they are all off by default, so a site keeps the rules it had. After a
 * change the user/password view calls afterChange(): the session id is renewed, the user's other sessions are
 * ended (EndOtherSessions) and the "your password was changed" mail is sent (ChangeNotificationMail, mail
 * category security). Guide: doc/features/6.0/modern-password-change.md.
 *
 * @package kernel
 */
class expPasswordPolicy
{
    /** The rule ids, in display order */
    const RULE_LENGTH = 'length';
    const RULE_LOWERCASE = 'lowercase';
    const RULE_UPPERCASE = 'uppercase';
    const RULE_DIGIT = 'digit';
    const RULE_SYMBOL = 'symbol';
    const RULE_CLASSES = 'classes';
    const RULE_NOT_LOGIN = 'not_login';
    const RULE_NOT_CURRENT = 'not_current';

    /** The element ids of the three fields, keyed by their POST names */
    protected static $fieldIDs = array( 'oldPassword' => 'password-old', 'newPassword' => 'password-new',
                                        'confirmPassword' => 'password-confirm' );

    /** @var eZINI */
    protected $ini;

    /**
     * @param eZINI|null $ini site.ini (tests pass their own)
     */
    public function __construct( $ini = null )
    {
        $this->ini = $ini instanceof eZINI ? $ini : eZINI::instance();
    }

    /**
     * @return expPasswordPolicy
     */
    public static function instance()
    {
        return new self();
    }

    /**
     * @return array the element ids of the fields, keyed by POST name
     */
    public static function fieldIDs()
    {
        return self::$fieldIDs;
    }

    /**
     * @return int [UserSettings] MinPasswordLength, 3 when it is not set
     */
    public function minLength()
    {
        if ( !$this->ini->hasVariable( 'UserSettings', 'MinPasswordLength' ) )
            return 3;
        return max( 0, (int)$this->ini->variable( 'UserSettings', 'MinPasswordLength' ) );
    }

    /**
     * @param string $name a [PasswordSettings] setting
     * @param bool $default when it is not set
     * @return bool
     */
    public function enabled( $name, $default = false )
    {
        if ( !$this->ini->hasVariable( 'PasswordSettings', $name ) )
            return $default;
        return in_array( strtolower( trim( (string)$this->ini->variable( 'PasswordSettings', $name ) ) ), array( 'enabled', 'true', '1' ), true );
    }

    /**
     * @return int [PasswordSettings] MinCharacterClasses (0 to 4)
     */
    public function minCharacterClasses()
    {
        if ( !$this->ini->hasVariable( 'PasswordSettings', 'MinCharacterClasses' ) )
            return 0;
        return min( 4, max( 0, (int)$this->ini->variable( 'PasswordSettings', 'MinCharacterClasses' ) ) );
    }

    /**
     * @return int the length of a generated password ([UserSettings] GeneratePasswordLength, at least the minimum)
     */
    public function generateLength()
    {
        $length = $this->ini->hasVariable( 'UserSettings', 'GeneratePasswordLength' ) ? (int)$this->ini->variable( 'UserSettings', 'GeneratePasswordLength' ) : 16;
        return max( $length, $this->minLength(), 12 );
    }

    /**
     * The rules in force, in display order.
     *
     * @return array list of array( id, text, min ); text is translated (context kernel/user/password)
     */
    public function rules()
    {
        $min = $this->minLength();
        $rules = array( array( 'id' => self::RULE_LENGTH, 'min' => $min,
            'text' => ezpI18n::tr( 'kernel/user/password', 'At least %1 characters', null, array( $min ) ) ) );
        if ( $this->enabled( 'RequireLowercase' ) )
            $rules[] = array( 'id' => self::RULE_LOWERCASE, 'min' => 1, 'text' => ezpI18n::tr( 'kernel/user/password', 'A lowercase letter' ) );
        if ( $this->enabled( 'RequireUppercase' ) )
            $rules[] = array( 'id' => self::RULE_UPPERCASE, 'min' => 1, 'text' => ezpI18n::tr( 'kernel/user/password', 'An uppercase letter' ) );
        if ( $this->enabled( 'RequireDigit' ) )
            $rules[] = array( 'id' => self::RULE_DIGIT, 'min' => 1, 'text' => ezpI18n::tr( 'kernel/user/password', 'A digit' ) );
        if ( $this->enabled( 'RequireSymbol' ) )
            $rules[] = array( 'id' => self::RULE_SYMBOL, 'min' => 1, 'text' => ezpI18n::tr( 'kernel/user/password', 'A symbol or space (not a letter or digit)' ) );
        $classes = $this->minCharacterClasses();
        if ( $classes > 0 )
            $rules[] = array( 'id' => self::RULE_CLASSES, 'min' => $classes,
                'text' => ezpI18n::tr( 'kernel/user/password', 'At least %1 of: lowercase letters, uppercase letters, digits, symbols', null, array( $classes ) ) );
        if ( $this->enabled( 'ForbidLogin' ) )
            $rules[] = array( 'id' => self::RULE_NOT_LOGIN, 'min' => 0, 'text' => ezpI18n::tr( 'kernel/user/password', 'Does not contain your user name' ) );
        if ( $this->enabled( 'ForbidCurrentPassword' ) )
            $rules[] = array( 'id' => self::RULE_NOT_CURRENT, 'min' => 0, 'text' => ezpI18n::tr( 'kernel/user/password', 'Is not your current password' ) );
        return $rules;
    }

    /**
     * The sentence for a failed rule, said about the new password.
     *
     * @param string $id
     * @return string
     */
    public function errorText( $id, $newPassword = true )
    {
        if ( !$newPassword )
            return $this->accountErrorText( $id );
        switch ( $id )
        {
            case self::RULE_LENGTH:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must be at least %1 characters long.', null, array( $this->minLength() ) );
            case self::RULE_LOWERCASE:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must contain a lowercase letter.' );
            case self::RULE_UPPERCASE:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must contain an uppercase letter.' );
            case self::RULE_DIGIT:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must contain a digit.' );
            case self::RULE_SYMBOL:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must contain a symbol or a space.' );
            case self::RULE_CLASSES:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must mix at least %1 kinds of characters: lowercase letters, uppercase letters, digits, symbols.', null, array( $this->minCharacterClasses() ) );
            case self::RULE_NOT_LOGIN:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must not contain your user name.' );
            case self::RULE_NOT_CURRENT:
                return ezpI18n::tr( 'kernel/user/password', 'The new password must be different from your current password.' );
        }
        return ezpI18n::tr( 'kernel/user/password', 'The new password does not meet the requirements.' );
    }

    /**
     * The sentence for a failed rule on a user account's form (registration, content/edit of a user, the console):
     * the length keeps the datatype's sentence of before.
     *
     * @param string $id
     * @return string
     */
    public function accountErrorText( $id )
    {
        switch ( $id )
        {
            case self::RULE_LENGTH:
                return ezpI18n::tr( 'kernel/classes/datatypes', 'The password must be at least %1 characters long.', null, array( $this->minLength() ) );
            case self::RULE_LOWERCASE:
                return ezpI18n::tr( 'kernel/user/password/account', 'The password must contain a lowercase letter.' );
            case self::RULE_UPPERCASE:
                return ezpI18n::tr( 'kernel/user/password/account', 'The password must contain an uppercase letter.' );
            case self::RULE_DIGIT:
                return ezpI18n::tr( 'kernel/user/password/account', 'The password must contain a digit.' );
            case self::RULE_SYMBOL:
                return ezpI18n::tr( 'kernel/user/password/account', 'The password must contain a symbol or a space.' );
            case self::RULE_CLASSES:
                return ezpI18n::tr( 'kernel/user/password/account', 'The password must mix at least %1 kinds of characters: lowercase letters, uppercase letters, digits, symbols.', null, array( $this->minCharacterClasses() ) );
            case self::RULE_NOT_LOGIN:
                return ezpI18n::tr( 'kernel/user/password/account', 'The password must not contain the user name.' );
            case self::RULE_NOT_CURRENT:
                return ezpI18n::tr( 'kernel/user/password/account', 'The password must be different from the current password.' );
        }
        return ezpI18n::tr( 'kernel/user/password/account', 'The password does not meet the requirements.' );
    }

    /**
     * Checks a password of a user account form and says why it is refused (registration, content/edit of a user,
     * the console). The rules are those of user/password.
     *
     * @param string $password
     * @param eZUser|null $user the account when it exists (for not_current)
     * @param string|null $login the login typed in the form (for not_login; else the account's)
     * @return array list of translated sentences, empty when the password is accepted
     */
    public function accountErrors( $password, $user = null, $login = null )
    {
        $out = array();
        foreach ( $this->validate( $password, $user, $login ) as $id )
            $out[] = $this->accountErrorText( $id );
        return $out;
    }

    /**
     * @param string $password
     * @return int how many of the four kinds (lowercase, uppercase, digit, symbol) the password has
     */
    public static function characterClasses( $password )
    {
        $password = (string)$password;
        $n = 0;
        foreach ( self::classPatterns() as $pattern )
            if ( self::matches( $pattern, $password ) )
                $n++;
        return $n;
    }

    /** @return array the four kinds of characters as patterns */
    protected static function classPatterns()
    {
        return array( self::RULE_LOWERCASE => '/\p{Ll}/u', self::RULE_UPPERCASE => '/\p{Lu}/u',
                      self::RULE_DIGIT => '/\p{Nd}/u', self::RULE_SYMBOL => '/[^\p{L}\p{Nd}]/u' );
    }

    /** A pattern match that also works on a string that is not UTF-8 (then byte by byte) */
    protected static function matches( $pattern, $string )
    {
        $r = @preg_match( $pattern, $string );
        if ( $r === false )
        {
            $ascii = array( '/\p{Ll}/u' => '/[a-z]/', '/\p{Lu}/u' => '/[A-Z]/', '/\p{Nd}/u' => '/[0-9]/', '/[^\p{L}\p{Nd}]/u' => '/[^a-zA-Z0-9]/' );
            $r = isset( $ascii[$pattern] ) ? preg_match( $ascii[$pattern], $string ) : 0;
        }
        return (bool)$r;
    }

    /**
     * Which rules the new password fails.
     *
     * @param string $password the new password
     * @param eZUser|null $user for not_login and not_current
     * @return array list of rule ids, empty when the password meets every rule
     */
    public function validate( $password, $user = null, $login = null )
    {
        $password = (string)$password;
        $failed = array();
        $patterns = self::classPatterns();
        foreach ( $this->rules() as $rule )
        {
            $ok = true;
            switch ( $rule['id'] )
            {
                case self::RULE_LENGTH:
                    // bytes, as before (a multi-byte character counts more, so no password gets refused that passed)
                    $ok = strlen( $password ) >= $rule['min'];
                    break;
                case self::RULE_LOWERCASE:
                case self::RULE_UPPERCASE:
                case self::RULE_DIGIT:
                case self::RULE_SYMBOL:
                    $ok = self::matches( $patterns[$rule['id']], $password );
                    break;
                case self::RULE_CLASSES:
                    $ok = self::characterClasses( $password ) >= $rule['min'];
                    break;
                case self::RULE_NOT_LOGIN:
                    $loginName = $login !== null ? (string)$login : ( $user instanceof eZUser ? (string)$user->attribute( 'login' ) : '' );
                    $ok = strlen( $loginName ) < 3 || stripos( $password, $loginName ) === false;
                    break;
                case self::RULE_NOT_CURRENT:
                    $ok = !( $user instanceof eZUser ) || !self::isCurrentPassword( $user, $password );
                    break;
            }
            if ( !$ok )
                $failed[] = $rule['id'];
        }
        return $failed;
    }

    /**
     * @param eZUser $user
     * @param string $password
     * @return bool the password is the user's current password
     */
    public static function isCurrentPassword( $user, $password )
    {
        return (bool)$user->authenticateHash( $user->attribute( 'login' ), (string)$password, $user->site(),
                                              $user->attribute( 'password_hash_type' ), $user->attribute( 'password_hash' ) );
    }

    /**
     * The rules with their state after a submit, for the template.
     *
     * @param array $failed rule ids that failed
     * @return array list of array( id, text, min, failed )
     */
    public function rulesForTemplate( $failed = array() )
    {
        $out = array();
        foreach ( $this->rules() as $rule )
            $out[] = $rule + array( 'failed' => in_array( $rule['id'], (array)$failed, true ) );
        return $out;
    }

    /**
     * What the client script needs (doc/features/6.0/modern-password-change.md, "The page").
     *
     * @param eZUser|null $user
     * @return array
     */
    public function clientConfig( $user = null )
    {
        $rules = array();
        foreach ( $this->rules() as $rule )
            $rules[] = array( 'id' => $rule['id'], 'min' => (int)$rule['min'], 'text' => $rule['text'] );
        $config = array(
            'minLength' => $this->minLength(),
            'rules' => $rules,
            'strengthMeter' => $this->enabled( 'StrengthMeter', true ),
            'generate' => $this->enabled( 'GenerateButton', true ),
            'generateLength' => $this->generateLength(),
            'fields' => array( 'current' => self::$fieldIDs['oldPassword'], 'new' => self::$fieldIDs['newPassword'],
                               'confirm' => self::$fieldIDs['confirmPassword'] ),
            'i18n' => array(
                'show' => ezpI18n::tr( 'kernel/user/password/js', 'Show' ),
                'hide' => ezpI18n::tr( 'kernel/user/password/js', 'Hide' ),
                'showPassword' => ezpI18n::tr( 'kernel/user/password/js', 'Show password' ),
                'hidePassword' => ezpI18n::tr( 'kernel/user/password/js', 'Hide password' ),
                'match' => ezpI18n::tr( 'kernel/user/password/js', 'The passwords match.' ),
                'noMatch' => ezpI18n::tr( 'kernel/user/password/js', 'The passwords do not match yet.' ),
                'strength' => ezpI18n::tr( 'kernel/user/password/js', 'Strength' ),
                'strength0' => ezpI18n::tr( 'kernel/user/password/js', 'Very weak' ),
                'strength1' => ezpI18n::tr( 'kernel/user/password/js', 'Weak' ),
                'strength2' => ezpI18n::tr( 'kernel/user/password/js', 'Fair' ),
                'strength3' => ezpI18n::tr( 'kernel/user/password/js', 'Good' ),
                'strength4' => ezpI18n::tr( 'kernel/user/password/js', 'Strong' ),
                'ruleMet' => ezpI18n::tr( 'kernel/user/password/js', 'met' ),
                'ruleUnmet' => ezpI18n::tr( 'kernel/user/password/js', 'not met yet' ),
                'generated' => ezpI18n::tr( 'kernel/user/password/js', 'A strong password was generated and filled into both fields. Store it before you save.' ),
            ),
        );
        if ( $this->enabled( 'ForbidLogin' ) && $user instanceof eZUser )
            $config['login'] = (string)$user->attribute( 'login' );
        return $config;
    }

    /**
     * Ends the user's other sessions after a password change ([PasswordSettings] EndOtherSessions) and renews the
     * id of this one.
     *
     * @param int $userID
     * @param string|null $keepSessionKey the session to keep (null: this request's session)
     * @return int|null how many sessions were ended; null when the setting is off or the handler cannot
     */
    public function endOtherSessions( $userID, $keepSessionKey = null )
    {
        if ( !$this->enabled( 'EndOtherSessions', true ) )
            return null;
        if ( $keepSessionKey === null )
        {
            // a new id for this session: whoever knew the old one does not keep it
            if ( class_exists( 'eZSession' ) && eZSession::hasStarted() )
                eZSession::regenerate();
            $keepSessionKey = session_id();
        }
        try
        {
            $ended = eZSession::getHandlerInstance()->deleteOtherSessionsOfUser( (int)$userID, (string)$keepSessionKey );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not end the other sessions: ' . $e->getMessage(), __METHOD__ );
            return null;
        }
        if ( $ended !== null )
        {
            eZUser::clearSessionCache();
            if ( class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'access.session.revoke', array( 'object' => expAuditHook::user( (int)$userID ),
                    'reason' => 'password_change', 'after' => array( 'count' => (int)$ended ) ) );
        }
        return $ended;
    }

    /** The session variable that holds the password stamp, next to eZUserLoggedInID */
    const SESSION_KEY = 'eZUserPasswordStamp';

    /** @var bool a stale session is being ended (the logout and its audit ask for the current user again) */
    protected static $endingStaleSession = false;

    /**
     * The stamp of a sign-in: which user signed in with which password, without the password or its hash. A keyed
     * hash (HMAC-SHA256 with a key derived from the site secret) of the user id and the stored password hash; it
     * changes when the password changes. Null when there is nothing to stamp: no user, or an account without a
     * password of its own (an empty hash: LDAP, text file and single sign-on users), or no site secret.
     *
     * @param int $userID
     * @param string $hash the stored password_hash
     * @param int|string $hashType the stored password_hash_type
     * @return string|null "<userID>:<32 characters>"
     */
    public static function stampFor( $userID, $hash, $hashType )
    {
        $userID = (int)$userID;
        $hash = (string)$hash;
        if ( $userID <= 0 || $hash === '' || (int)$hashType === eZUser::PASSWORD_HASH_EMPTY )
            return null;
        try
        {
            $key = expMailSecret::derive( 'password-session-stamp' );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'No site secret for the password stamp: ' . $e->getMessage(), __METHOD__ );
            return null;
        }
        $mac = rtrim( strtr( base64_encode( hash_hmac( 'sha256', $userID . '|' . $hash, $key, true ) ), '+/', '-_' ), '=' );
        return $userID . ':' . substr( $mac, 0, 32 );
    }

    /**
     * @param eZUser $user
     * @return string|null the stamp of the user's current password (stampFor())
     */
    public static function stampOf( $user )
    {
        if ( !$user instanceof eZUser || !$user->isRegistered() )
            return null;
        return self::stampFor( $user->attribute( 'contentobject_id' ), $user->attribute( 'password_hash' ), $user->attribute( 'password_hash_type' ) );
    }

    /**
     * @return bool [PasswordSettings] EndOtherSessions (also switches the stamp check off)
     */
    public static function stampCheckEnabled()
    {
        $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'PasswordSettings', 'EndOtherSessions' ) )
            return true;
        return in_array( strtolower( trim( (string)$ini->variable( 'PasswordSettings', 'EndOtherSessions' ) ) ), array( 'enabled', 'true', '1' ), true );
    }

    /**
     * Compares a session's stamp with the user's current password.
     *
     * @param eZUser $user the session's user
     * @param mixed $stored what the session holds (null: nothing)
     * @return string skip (anonymous, no password of its own, check off), legacy (the session has no stamp of this
     *                user: signed in before the stamp existed, it stays valid until its next sign-in), match, mismatch
     */
    public static function compareStamp( $user, $stored )
    {
        if ( !self::stampCheckEnabled() )
            return 'skip';
        $want = self::stampOf( $user );
        if ( $want === null )
            return 'skip';
        $prefix = (int)$user->attribute( 'contentobject_id' ) . ':';
        if ( !is_string( $stored ) || strpos( $stored, $prefix ) !== 0 )
            return 'legacy';
        return hash_equals( $want, $stored ) ? 'match' : 'mismatch';
    }

    /**
     * After a sign-in (eZUser::setCurrentlyLoggedInUser()): this session was made with the user's current password.
     *
     * @param eZUser $user
     * @return bool a stamp was written
     */
    public static function stampSession( $user )
    {
        if ( !class_exists( 'eZSession' ) || !eZSession::hasStarted() )
            return false;
        $stamp = self::stampCheckEnabled() ? self::stampOf( $user ) : null;
        if ( $stamp === null )
        {
            eZSession::unsetkey( self::SESSION_KEY, false );
            return false;
        }
        eZSession::set( self::SESSION_KEY, $stamp );
        return true;
    }

    /**
     * After a store of a user (eZUser::store()): when it is the session's own user, the session takes the stamp of
     * the stored password, so the session that changes its own password stays signed in.
     *
     * @param eZUser $user
     * @return bool the session was stamped again
     */
    public static function restampOwnSession( $user )
    {
        if ( !$user instanceof eZUser )
            return false;
        // the user objects this request keeps (eZUser::instance()) take the stored password, or the next
        // currentUser() of this very request would find the session's new stamp against the old hash
        $id = (int)$user->attribute( 'contentobject_id' );
        foreach ( array( 'eZUserGlobalInstance_', 'eZUserGlobalInstance_' . $id ) as $key )
        {
            if ( isset( $GLOBALS[$key] ) && $GLOBALS[$key] instanceof eZUser && $GLOBALS[$key] !== $user
                 && (int)$GLOBALS[$key]->attribute( 'contentobject_id' ) === $id )
            {
                $GLOBALS[$key]->setAttribute( 'password_hash', $user->attribute( 'password_hash' ) );
                $GLOBALS[$key]->setAttribute( 'password_hash_type', $user->attribute( 'password_hash_type' ) );
            }
        }
        if ( !class_exists( 'eZSession' ) || !eZSession::hasStarted() )
            return false;
        if ( (int)eZSession::get( 'eZUserLoggedInID', 0 ) !== (int)$user->attribute( 'contentobject_id' ) )
            return false;
        return self::stampSession( $user );
    }

    /**
     * Whether this session may go on as the user (eZUser::currentUser()): false when the session was signed in with
     * a password that has been changed since. A session without a stamp (signed in before the upgrade), a temporary
     * switch to another user, the anonymous user and accounts without a password of their own pass.
     *
     * @param eZUser $user
     * @return bool
     */
    public static function sessionIsCurrent( $user )
    {
        if ( self::$endingStaleSession || !$user instanceof eZUser || !class_exists( 'eZSession' ) || !eZSession::hasStarted() )
            return true;
        $id = (int)$user->attribute( 'contentobject_id' );
        if ( (int)eZSession::get( 'eZUserLoggedInID', 0 ) !== $id )
            return true;
        return self::compareStamp( $user, eZSession::get( self::SESSION_KEY, false ) ) !== 'mismatch';
    }

    /**
     * Ends this session because its password was changed elsewhere: signed out (eZUser::logoutCurrent()) and
     * recorded as access.session.revoke (reason password_changed).
     *
     * @param eZUser $user
     */
    public static function endStaleSession( $user )
    {
        if ( self::$endingStaleSession )
            return;
        self::$endingStaleSession = true;
        try
        {
            $id = $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
            eZUser::logoutCurrent();
            if ( class_exists( 'eZSession' ) && eZSession::hasStarted() )
                eZSession::unsetkey( self::SESSION_KEY, false );
            if ( $id > 0 && class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'access.session.revoke', array( 'object' => expAuditHook::user( $id ),
                    'reason' => 'password_changed', 'after' => array( 'count' => 1 ) ) );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Ending a stale session: ' . $e->getMessage(), __METHOD__ );
        }
        self::$endingStaleSession = false;
    }

    /**
     * Sends "your password was changed" to the user ([PasswordSettings] ChangeNotificationMail), mail category
     * security. The mail never contains the password.
     *
     * @param eZUser $user
     * @return bool the mail was handed to the transport
     */
    public function sendChangedMail( $user, $sessionsEnded = null, $otherSessionsSignedOut = false )
    {
        if ( !$user instanceof eZUser || !$this->enabled( 'ChangeNotificationMail', true ) )
            return false;
        $email = (string)$user->attribute( 'email' );
        if ( !eZMail::validate( $email ) )
            return false;
        $time = time();
        $ip = class_exists( 'eZSys' ) ? (string)eZSys::clientIP() : '';
        $subject = ezpI18n::tr( 'kernel/user/password/mail', 'Your password was changed' );
        $body = ezpI18n::tr( 'kernel/user/password/mail', 'The password of your account %login was changed on %date.', null,
                             array( '%login' => $user->attribute( 'login' ), '%date' => date( 'Y-m-d H:i', $time ) ) )
              . "\n\n"
              . ezpI18n::tr( 'kernel/user/password/mail', 'If you did this, you do not need to do anything.' ) . "\n"
              . ezpI18n::tr( 'kernel/user/password/mail', 'If you did not, reset your password at once with "Forgot your password?" on the login page, and contact us.' ) . "\n";
        $mail = new eZMail();
        $rendered = class_exists( 'expMailPreferencesService' )
            ? expMailPreferencesService::renderTemplate( 'design:user/password_changed_mail.tpl',
                                                         array( 'user' => $user, 'changed_at' => $time, 'ip' => $ip, 'sessions_ended' => $sessionsEnded,
                                                                 'other_sessions_signed_out' => (bool)$otherSessionsSignedOut ) )
            : null;
        if ( $rendered !== null && trim( $rendered['body'] ) !== '' )
        {
            $body = $rendered['body'];
            if ( $rendered['subject'] !== null && trim( $rendered['subject'] ) !== '' )
                $subject = trim( $rendered['subject'] );
            if ( $rendered['content_type'] )
                $mail->setContentType( $rendered['content_type'] );
        }
        $sender = $this->ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$sender )
            $sender = $this->ini->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSender( $sender );
        $mail->setReceiver( $email );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $mail->setCategory( 'security' );
        return (bool)eZMailTransport::send( $mail );
    }
}
?>
