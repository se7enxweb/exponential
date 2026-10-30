<?php
/**
 * File containing the expUpgradeStepHelpContentTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Help maintained as content: the released help ids and the answers to "Was this helpful?". The same tables
 * ./console exp:help:content install creates.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepHelpContentTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-help-content-tables';
    }

    public function description()
    {
        return 'Help maintained as content: the tables exphelp_help_id and exphelp_feedback';
    }

    public function tables()
    {
        return array( 'exphelp_help_id', 'exphelp_feedback' );
    }
}
