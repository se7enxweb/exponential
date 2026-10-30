<?php
/**
 * File containing the expUpgradeStepMailPreferenceTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * E-mail preferences and consent: five new tables, nothing existing changes.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepMailPreferenceTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-mail-preference-tables';
    }

    public function description()
    {
        return 'E-mail preferences and consent: the tables expmail_category, expmail_consent_log, expmail_pending, expmail_preference, expmail_suppression';
    }

    public function tables()
    {
        return array( 'expmail_category', 'expmail_consent_log', 'expmail_pending', 'expmail_preference', 'expmail_suppression' );
    }
}
