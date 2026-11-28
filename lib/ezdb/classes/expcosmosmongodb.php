<?php
/**
 * File containing the expCosmosMongoDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of Azure Cosmos DB for MongoDB, request units (server version 4.2 or later; TLS on port 10255).
 * DatabaseImplementation=cosmosdb. What differs from MongoDB is the profile expMongoWireProfile::get( 'cosmosdb' ); the driver is
 * expMongoDB with it, so the kernel treats the database as MongoDB (databaseName() 'mongo').
 */
class expCosmosMongoDB extends expMongoDB
{
    const PROFILE = 'cosmosdb';
}
