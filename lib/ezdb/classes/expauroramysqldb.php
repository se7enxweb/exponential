<?php
/**
 * File containing the expAuroraMySQLDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of Amazon Aurora MySQL: Amazon Aurora MySQL 3 (MySQL 8.0 compatible; a profile, the service cannot run locally).
 * DatabaseImplementation=aurora. What differs from MySQL is the profile expMySQLWireProfile::AURORA; the driver is
 * expMySQLWireDB with it (doc/eng-us/features/6.0/mysql-wire-databases.md).
 */
class expAuroraMySQLDB extends expMySQLWireDB
{
    const PROFILE = expMySQLWireProfile::AURORA;
}
