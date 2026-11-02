<?php
/**
 * File containing the expUpgradeStepMigrationDeployTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Deploys of migrations to remotes: the tables exp_migration_deploy (one row per deploy) and exp_migration_deploy_host
 * (one row per remote of a deploy). Deploys work without them (the reports are files), but they are not recorded in
 * the database until they exist.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepMigrationDeployTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-migration-deploy-tables';
    }

    public function description()
    {
        return 'Deploys of migrations to remotes: the tables exp_migration_deploy and exp_migration_deploy_host';
    }

    public function tables()
    {
        return array( expMigrationDeployRecord::DEPLOY_TABLE, expMigrationDeployRecord::HOST_TABLE );
    }
}
