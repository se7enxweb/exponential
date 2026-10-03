<?php
/**
 * The audit taxonomy branch of expservices: personal API tokens. Registered in settings/audit.ini.append.php.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expServicesAuditBranch implements expAuditTaxonomyBranch
{
    public function events()
    {
        return array(
            'access.expservices.token.create' => array( 'label' => 'API token created', 'severity' => 'notice', 'channel' => 'access', 'default' => 'on' ),
            'access.expservices.token.revoke' => array( 'label' => 'API token revoked', 'severity' => 'notice', 'channel' => 'access', 'default' => 'on' ),
            'access.expservices.token.failed' => array( 'label' => 'API token refused', 'severity' => 'notice', 'channel' => 'access', 'default' => 'on' ),
        );
    }
}
