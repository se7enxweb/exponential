<?php
/**
 * File containing the expRestPasswordAuthFilter class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Checks a login and password for the REST interface's basic authentication the way the sign-in does.
 *
 * The user is found by login (and by e-mail address when [UserSettings] AuthenticateMatch allows it), must be
 * enabled, published and not locked by failed logins, and the password is checked with eZUser::authenticateHash()
 * against the user's own hash type, so bcrypt and argon2 (php_default) work as well as the md5 types. A wrong
 * password counts as a failed login, so the lock after [UserSettings] MaxNumberOfFailedLogin applies to the REST
 * interface too. The session is never touched: the request is signed in by the REST layer, not by a cookie.
 */
class expRestPasswordAuthFilter extends ezcAuthenticationFilter
{
    const STATUS_INVALID_CREDENTIALS = 1;
    const STATUS_USER_DISABLED = 2;

    /**
     * The user of the last successful run(); reset by every run().
     *
     * @var eZUser|null
     */
    public static $user = null;

    public function run( $credentials )
    {
        self::$user = null;
        $login = isset( $credentials->id ) ? (string)$credentials->id : '';
        $password = isset( $credentials->password ) ? (string)$credentials->password : '';
        if ( $login === '' || $password === '' )
            return self::STATUS_INVALID_CREDENTIALS;

        $user = self::findUser( $login );
        if ( !$user instanceof eZUser )
        {
            // spend about the time of a hash check, so an unknown login cannot be told from a wrong password
            password_hash( $password, PASSWORD_DEFAULT );
            return self::STATUS_INVALID_CREDENTIALS;
        }

        $userID = (int)$user->attribute( 'contentobject_id' );
        if ( !self::passwordMatches( $user, $password ) )
        {
            eZUser::setFailedLoginAttempts( $userID );
            return self::STATUS_INVALID_CREDENTIALS;
        }
        if ( !$user->isEnabled() || !eZUser::isEnabledAfterFailedLogin( $userID ) )
            return self::STATUS_USER_DISABLED;

        $object = $user->attribute( 'contentobject' );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return self::STATUS_USER_DISABLED;

        self::$user = $user;
        return self::STATUS_OK;
    }

    /**
     * Whether a password is the user's, by the user's own hash type (no database).
     *
     * @param eZUser $user
     * @param string $password
     * @return bool
     */
    public static function passwordMatches( eZUser $user, $password )
    {
        $hashType = (int)$user->attribute( 'password_hash_type' );
        // type 0 is a disabled password (no login at all)
        if ( $hashType === 0 || !is_string( $password ) || $password === '' )
            return false;
        // the old MySQL PASSWORD() type can only be checked by MySQL itself
        if ( $hashType === eZUser::PASSWORD_HASH_MYSQL && eZDB::instance()->databaseName() !== 'mysql' )
            return false;
        return (bool)eZUser::authenticateHash( (string)$user->attribute( 'login' ), $password, eZUser::site(),
                                               $hashType, (string)$user->attribute( 'password_hash' ) );
    }

    /**
     * The user a login names: by login, or by e-mail address when AuthenticateMatch includes email.
     *
     * @param string $login
     * @return eZUser|null
     */
    protected static function findUser( $login )
    {
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser && ( eZUser::authenticationMatch() & eZUser::AUTHENTICATE_EMAIL ) && eZMail::validate( $login ) )
            $user = eZUser::fetchByEmail( $login );
        return $user instanceof eZUser ? $user : null;
    }
}
?>
