<?php
/**
 * File containing the expAuroraPostgreSQLDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Amazon Aurora PostgreSQL driver: DatabaseImplementation=aurorapostgresql (also aurora-postgresql; aurora alone is Aurora MySQL) in site.ini.
 * What differs from PostgreSQL is the profile 'aurorapostgresql' (expPostgreSQLWireProfile); the driver is expPostgreSQLWireDB.
 *
 * @since 6.0.15
 */
class expAuroraPostgreSQLDB extends expPostgreSQLWireDB
{
    const PROFILE = 'aurorapostgresql';
}
