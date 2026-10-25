<?php
/**
 * File containing the expSignInSecondFactorHandler interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a two-step sign-in extension tells the kernel about sign-ins outside the web login form (REST with a password,
 * WebDAV, SOAP, the expservices login): whether a password that was right is enough for an account, and whether a
 * secret is one of the account's app passwords. The kernel asks every class of site.ini
 * [SignInSecondFactorSettings] Handlers[] (expSignInSecondFactor); without one nothing changes.
 *
 * Guide: doc/eng-us/features/6.0/second-factor-on-other-sign-in-paths.md
 */
interface expSignInSecondFactorHandler
{
    /**
     * Whether an account whose password was right may sign in through a channel.
     *
     * @param eZUser $user the account, its password checked
     * @param string $channel expSignInSecondFactor::CHANNEL_* (rest, webdav, soap, expservices)
     * @param string|null $code a one-time code the request carries (header or field), null when it carries none
     * @return array|null null: the sign-in may go on; else array( 'reason' => expSignInSecondFactor::REASON_*,
     *                    'message' => a sentence for the client ) and the sign-in is refused
     */
    public function checkSignIn( eZUser $user, $channel, $code );

    /**
     * The account an app password belongs to, when $secret is one of its app passwords and may be used on the channel.
     * Called before the password is checked, so an app password never counts as a failed password.
     *
     * @param string $login the login name (or e-mail address) sent with it
     * @param string $secret what was sent as the password
     * @param string $channel expSignInSecondFactor::CHANNEL_*
     * @return eZUser|null
     */
    public function appPasswordUser( $login, $secret, $channel );
}

?>
