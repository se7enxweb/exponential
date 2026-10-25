<?php
/**
 * File containing the expPasskeyLogin class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Signs in an account whose passkey expPasskeyService checked, through the same housekeeping as every password
 * sign-in (eZUser::loginSucceeded(): last visit, the new session id, failed attempts reset, access.session.login,
 * whose "handler" then says expPasskeyLogin). No login handler is asked: the passkey, with its user verification,
 * is the whole sign-in, so the second step of a two-step sign-in extension is not asked again.
 */
class expPasskeyLogin extends eZUser
{
    /**
     * @param eZUser $user
     */
    public static function signIn( eZUser $user )
    {
        static::loginSucceeded( $user );
    }
}
