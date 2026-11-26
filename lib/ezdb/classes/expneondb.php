<?php
/**
 * File containing the expNeonDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Neon serverless PostgreSQL driver: DatabaseImplementation=neon in site.ini.
 * What differs from PostgreSQL is the profile 'neon' (expPostgreSQLWireProfile); the driver is expPostgreSQLWireDB.
 *
 * @since 6.0.15
 */
class expNeonDB extends expPostgreSQLWireDB
{
    const PROFILE = 'neon';
}
