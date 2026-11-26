<?php
/**
 * File containing the expSingleStoreDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of SingleStore: SingleStore 8.5 and later (measured on 10.10, the dev container); the kernel's tables are reference tables.
 * DatabaseImplementation=singlestore. What differs from MySQL is the profile expMySQLWireProfile::SINGLESTORE; the driver is
 * expMySQLWireDB with it (doc/eng-us/features/6.0/mysql-wire-databases.md).
 */
class expSingleStoreDB extends expMySQLWireDB
{
    const PROFILE = expMySQLWireProfile::SINGLESTORE;
}
