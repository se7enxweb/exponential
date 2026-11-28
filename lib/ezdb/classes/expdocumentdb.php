<?php
/**
 * File containing the expDocumentDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of Amazon DocumentDB 5.0 (TLS, the replica set rs0, no retryable writes).
 * DatabaseImplementation=documentdb. What differs from MongoDB is the profile expMongoWireProfile::get( 'documentdb' ); the driver is
 * expMongoDB with it, so the kernel treats the database as MongoDB (databaseName() 'mongo').
 */
class expDocumentDB extends expMongoDB
{
    const PROFILE = 'documentdb';
}
