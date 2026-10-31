<?php
/**
 * File containing the expUpgradeStepRecordTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Step 0: the table in which exp:upgrade records what it applied (expupgrade_step). The other steps run without it,
 * but nothing is recorded until it exists. The step after it, 6.0.15-migration-tables, makes the migration record
 * exp_migration that replaces it and copies its rows; expupgrade_step is then only read.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepRecordTable extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-upgrade-record-table';
    }

    public function description()
    {
        return 'The record of the upgrade steps (table expupgrade_step)';
    }

    public function tables()
    {
        return array( expUpgradeRecord::LEGACY_TABLE );
    }
}
