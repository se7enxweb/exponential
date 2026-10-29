<?php
/**
 * File containing the expUpgradeStepDataTableTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The data tables: named connections, the secret store and the synchronisation with content. The administration
 * creates them when they are missing too; the upgrade creates them up front.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepDataTableTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-data-table-tables';
    }

    public function description()
    {
        return 'Data tables: the tables exp_data_connection, exp_secret, exp_data_sync, exp_data_sync_conflict, exp_data_sync_link';
    }

    public function tables()
    {
        return array( 'exp_data_connection', 'exp_secret', 'exp_data_sync', 'exp_data_sync_conflict', 'exp_data_sync_link' );
    }
}
