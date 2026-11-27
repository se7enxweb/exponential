<?php
/**
 * File containing the expTursoDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Turso driver: DatabaseImplementation=turso in site.ini, a database in Turso's cloud
 * (libsql://<database>-<organisation>.turso.io). What differs from a libSQL server of one's own is the profile 'turso'
 * (expLibSQLProfile): HTTPS on port 443, and a database token always ([DatabaseSettings] Password,
 * [LibSQLSettings] AuthToken or the environment's TURSO_AUTH_TOKEN). The driver is expLibSQLDB.
 *
 * <code>
 * [DatabaseSettings]
 * DatabaseImplementation=turso
 * Server=libsql://exponential-acme.turso.io
 * Database=exponential
 * Password=<the database token>
 * </code>
 *
 * @since 6.0.15
 */
class expTursoDB extends expLibSQLDB
{
    const PROFILE = 'turso';
}
