<?php
/**
 * File containing the expUpgradeStepStaticPublishTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Static publishing: the tables exp_static_run (one row per publish or rollback of a target) and exp_static_page
 * (the pages of a target as last published). Publishing works without them, but nothing is recorded until they exist.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepStaticPublishTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-static-publish-tables';
    }

    public function description()
    {
        return 'Static publishing: the tables exp_static_run and exp_static_page';
    }

    public function tables()
    {
        return array( expStaticStatus::RUN_TABLE, expStaticStatus::PAGE_TABLE );
    }
}
