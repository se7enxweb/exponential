<?php
/**
 * File containing the expSpannerPGDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Google Cloud Spanner in its PostgreSQL dialect, through PGAdapter: DatabaseImplementation=spanner (also spannerpg,
 * pgadapter). A data-table connection and an import target, not a site database (see the profile).
 * What differs from PostgreSQL is the profile 'spanner' (expPostgreSQLWireProfile); the driver is expPostgreSQLWireDB.
 *
 * @since 6.0.15
 */
class expSpannerPGDB extends expPostgreSQLWireDB
{
    const PROFILE = 'spanner';
}
