<?php
/**
 * File containing the expTiDBDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of TiDB: TiDB 6.5 and later (measured on 8.5.1), self-hosted or TiDB Cloud.
 * DatabaseImplementation=tidb. What differs from MySQL is the profile expMySQLWireProfile::TIDB; the driver is
 * expMySQLWireDB with it (doc/eng-us/features/6.0/mysql-wire-databases.md).
 */
class expTiDBDB extends expMySQLWireDB
{
    const PROFILE = expMySQLWireProfile::TIDB;
}
