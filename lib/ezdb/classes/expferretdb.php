<?php
/**
 * File containing the expFerretDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The driver of FerretDB 2: the MongoDB wire protocol over PostgreSQL with the DocumentDB extension.
 * DatabaseImplementation=ferretdb. What differs from MongoDB is the profile expMongoWireProfile::get( 'ferretdb' ); the driver is
 * expMongoDB with it, so the kernel treats the database as MongoDB (databaseName() 'mongo').
 */
class expFerretDB extends expMongoDB
{
    const PROFILE = 'ferretdb';
}
