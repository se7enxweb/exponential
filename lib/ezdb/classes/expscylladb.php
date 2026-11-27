<?php
/**
 * File containing the expScyllaDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * ScyllaDB (2025.1 and later) on the driver of Apache Cassandra: ScyllaDB speaks the same CQL and the same native
 * protocol, so expCassandraDB does all the work, and this class gives the connection its own name (databaseName()
 * scylladb, the data table engine scylladb, ImplementationAlias[scylladb]). What differs is handled where it differs:
 * a keyspace made by createDatabase() has no tablets unless [CassandraSettings] Tablets=enabled (ScyllaDB 2025.1 does
 * no lightweight transactions on tablets, and the ids of auto_increment columns use them), and the server's version is
 * read from system.versions (system.local says 3.0.8, the Cassandra level ScyllaDB is compatible with).
 *
 * @since 6.0.15
 * @package lib
 */
class expScyllaDB extends expCassandraDB
{
    /** databaseName() of this driver */
    const NAME = 'scylladb';
}
?>
