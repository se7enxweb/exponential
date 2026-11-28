<?php
/**
 * File containing the expCosmosVCoreDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of Azure Cosmos DB for MongoDB vCore (TLS on port 10260).
 * DatabaseImplementation=cosmosdbvcore. What differs from MongoDB is the profile expMongoWireProfile::get( 'cosmosdbvcore' ); the driver is
 * expMongoDB with it, so the kernel treats the database as MongoDB (databaseName() 'mongo').
 */
class expCosmosVCoreDB extends expMongoDB
{
    const PROFILE = 'cosmosdbvcore';
}
