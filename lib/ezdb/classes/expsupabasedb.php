<?php
/**
 * File containing the expSupabaseDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Supabase PostgreSQL driver: DatabaseImplementation=supabase in site.ini.
 * What differs from PostgreSQL is the profile 'supabase' (expPostgreSQLWireProfile); the driver is expPostgreSQLWireDB.
 *
 * @since 6.0.15
 */
class expSupabaseDB extends expPostgreSQLWireDB
{
    const PROFILE = 'supabase';
}
