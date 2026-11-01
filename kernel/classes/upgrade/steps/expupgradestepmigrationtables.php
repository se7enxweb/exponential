<?php
/**
 * File containing the expUpgradeStepMigrationTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Step 0, the migration record: tables exp_migration (one row per migration, and the record of exp:upgrade) and
 * exp_migration_run (one row per run). The other steps run without them, but nothing is recorded until they exist.
 * The older record expupgrade_step of a pre-release of 6.0.15 is copied over and dropped by the step after it,
 * 6.0.15-upgrade-record-table.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepMigrationTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-migration-tables';
    }

    public function description()
    {
        return 'The migration record (tables exp_migration and exp_migration_run)';
    }

    public function tables()
    {
        return array( expMigrationRecord::TABLE, expMigrationRecord::RUN_TABLE );
    }
}
