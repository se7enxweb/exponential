<?php
/**
 * File containing the expVitessDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of Vitess: Vitess and PlanetScale, an unsharded keyspace (measured on vttestserver).
 * DatabaseImplementation=vitess. What differs from MySQL is the profile expMySQLWireProfile::VITESS; the driver is
 * expMySQLWireDB with it (doc/eng-us/features/6.0/mysql-wire-databases.md).
 */
class expVitessDB extends expMySQLWireDB
{
    const PROFILE = expMySQLWireProfile::VITESS;
}
