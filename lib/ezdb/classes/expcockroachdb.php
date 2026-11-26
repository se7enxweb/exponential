<?php
/**
 * File containing the expCockroachDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The CockroachDB driver: DatabaseImplementation=cockroachdb (also cockroach, crdb) in site.ini.
 * What differs from PostgreSQL is the profile 'cockroachdb' (expPostgreSQLWireProfile); the driver is expPostgreSQLWireDB.
 *
 * @since 6.0.15
 */
class expCockroachDB extends expPostgreSQLWireDB
{
    const PROFILE = 'cockroachdb';
}
