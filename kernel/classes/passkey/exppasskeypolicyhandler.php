<?php
/**
 * File containing the expPasskeyPolicyHandler interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A class that changes the passkey policy of an account for an extension (doc/eng-us/guides/passkeys.md, "Policy"):
 * an identity provider that knows a person must use a hardware key, a customer area that never asks. The kernel asks
 * every class of site.ini [PasskeySettings] PolicyHandlers[] in turn, after the settings decided; each gets the
 * decision so far and returns it changed, or null to leave it.
 *
 * Only the web pages apply the policy: REST, SOAP, WebDAV and the expservices login keep their own sign-in.
 *
 * @since 6.0.15
 */
interface expPasskeyPolicyHandler
{
    /**
     * @param eZUser $user
     * @param array $policy policy (optional, preferred, required), password_sign_in (bool)
     * @return array|null the policy changed, null to keep it
     */
    public function passkeyPolicy( eZUser $user, array $policy );
}
