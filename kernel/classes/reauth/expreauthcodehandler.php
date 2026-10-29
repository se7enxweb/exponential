<?php
/**
 * File containing the expReauthCodeHandler interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A class that checks the code of a two-step sign-in method for "Verify it's you" (expReauth). The kernel asks
 * every class of site.ini [ReauthSettings] CodeHandlers[]. Guide: doc/eng-us/guides/verify-its-you.md
 */
interface expReauthCodeHandler
{
    /**
     * Whether the account has a code it can confirm itself with (an authenticator app set up).
     *
     * @param eZUser $user
     * @return bool
     */
    public function hasReauthCode( eZUser $user );

    /**
     * Checks a code. A code is used up by a right answer; wrong answers count against the method's own limit.
     *
     * @param eZUser $user
     * @param string $code
     * @return bool
     */
    public function checkReauthCode( eZUser $user, $code );
}
