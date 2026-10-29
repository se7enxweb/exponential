<?php
/**
 * File containing the expUpgradeStepOAuthPkceColumns class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * PKCE for the OAuth authorization code flow: ezprest_authcode.code_challenge and code_challenge_method. Until they
 * exist no authorization code can be issued. Not applicable where the REST tables are not installed.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepOAuthPkceColumns extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-oauth-pkce-columns';
    }

    public function description()
    {
        return 'PKCE for the OAuth authorization server: ezprest_authcode.code_challenge and code_challenge_method';
    }

    public function fields()
    {
        return array( 'ezprest_authcode' => array( 'code_challenge', 'code_challenge_method' ) );
    }
}
